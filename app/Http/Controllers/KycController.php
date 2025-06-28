<?php

namespace App\Http\Controllers;

use App\Models\Kyc;
use App\Models\Client;
use App\Models\User;
use App\Models\Puce;
use Illuminate\Http\Request;
use App\Http\Requests\StoreKycRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KycController extends Controller
{
    /**
     * Nombre d'éléments par page pour la pagination
     *
     * @var int
     */
    protected $perPage = 15;
    /**
     * Affiche la liste des KYC avec onglets de statut
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $status = $request->get('status', Kyc::STATUS_EN_COURS);
        
        // Valider le statut demandé
        if (!in_array($status, [Kyc::STATUS_EN_COURS, Kyc::STATUS_VALIDE, Kyc::STATUS_REJETE])) {
            $status = Kyc::STATUS_EN_COURS;
        }
        
        // Construire la requête avec les relations et le tri
        $query = Kyc::with(['user', 'client'])
            ->where('status', $status)
            ->orderByDesc('created_at');
        
        // Recherche par terme si fourni
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('numero_npi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $kycs = $query->paginate($this->perPage)->withQueryString();
        
        // Compter les KYC par statut pour les onglets
        $counts = [
            'all' => Kyc::count(),
            Kyc::STATUS_EN_COURS => Kyc::where('status', Kyc::STATUS_EN_COURS)->count(),
            Kyc::STATUS_VALIDE => Kyc::where('status', Kyc::STATUS_VALIDE)->count(),
            Kyc::STATUS_REJETE => Kyc::where('status', Kyc::STATUS_REJETE)->count(),
        ];
        
        return view('kyc.index', [
            'kycs' => $kycs,
            'status' => $status,
            'counts' => $counts,
            'search' => $search ?? '',
            'statusLabels' => [
                Kyc::STATUS_EN_COURS => 'En cours',
                Kyc::STATUS_VALIDE => 'Validé',
                Kyc::STATUS_REJETE => 'Rejeté',
            ]
        ]);
    }

    /**
     * Affiche le formulaire de création d'un KYC
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // On ne propose que les utilisateurs avec statut_kyc = 'NonSoumis' ou 'Rejete' et qui n'ont pas déjà de KYC
        $users = User::whereIn('statut_kyc', [User::KYC_NON_SOUMIS, User::KYC_REJETE])
            ->whereDoesntHave('kyc')
            ->select('id', 'nom', 'prenom', 'email', 'statut_kyc')
            ->orderBy('nom')
            ->orderBy('prenom')
            ->get()
            ->map(function($user) {
                $user->full_name = "{$user->prenom} {$user->nom} ({$user->email})";
                return $user;
            })
            ->pluck('full_name', 'id');
        
        if ($users->isEmpty()) {
            return redirect()->route('kyc.index')
                ->with('info', 'Aucun utilisateur ne nécessite de création de KYC pour le moment.');
        }
        
        // Liste des pays pour le select
        $pays = [
            'France' => 'France',
            'Belgique' => 'Belgique',
            'Suisse' => 'Suisse',
            'Luxembourg' => 'Luxembourg',
            'Canada' => 'Canada',
            'Autre' => 'Autre',
        ];
        
        return view('kyc.create', [
            'users' => $users,
            'pays' => $pays,
            'statusOptions' => [
                Kyc::STATUS_EN_COURS => 'En cours de traitement',
                Kyc::STATUS_VALIDE => 'Validé',
                Kyc::STATUS_REJETE => 'Rejeté',
            ]
        ]);
    }

    /**
     * Enregistre un nouveau KYC
     *
     * @param StoreKycRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreKycRequest $request)
    {
        $data = $request->validated();
        
        try {
            DB::beginTransaction();
            
            // Vérifier que l'utilisateur existe et a un statut valide pour la création d'un KYC
            $user = User::where('id', $data['user_id'])
                ->whereIn('statut_kyc', [User::KYC_NON_SOUMIS, User::KYC_REJETE])
                ->firstOrFail();
            
            // Vérifier que l'utilisateur n'a pas déjà un KYC (double vérification)
            if ($user->kyc) {
                return redirect()->route('kyc.index')
                    ->with('error', 'Cet utilisateur a déjà un KYC.');
            }
            
            // Vérifier que le numéro NPI n'existe pas déjà (double vérification)
            if (Kyc::where('numero_npi', $data['numero_npi'])->exists()) {
                return back()->with('error', 'Ce numéro NPI est déjà utilisé.')->withInput();
            }
            
            // Déterminer le statut du KYC (par défaut EnCours)
            $status = $data['status'] ?? Kyc::STATUS_EN_COURS;
            
            // Créer le KYC
            $kyc = new Kyc([
                'user_id' => $user->id,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'nationalite' => $data['nationalite'],
                'telephone' => $data['telephone'],
                'adresse_postale' => $data['adresse_postale'],
                'numero_npi' => $data['numero_npi'],
                'status' => $status,
                'raison_rejet' => $data['raison_rejet'] ?? null,
            ]);
            
            $kyc->save();
            
            // Mettre à jour le statut KYC de l'utilisateur
            $user->statut_kyc = $status === Kyc::STATUS_VALIDE ? User::KYC_VALIDE : 
                               ($status === Kyc::STATUS_REJETE ? User::KYC_REJETE : User::KYC_EN_COURS);
            $user->save();
            
            // Si le KYC est validé, on peut créer un client si demandé
            if ($status === Kyc::STATUS_VALIDE && $request->has('creer_client') && $request->boolean('creer_client')) {
                $this->creerClientDepuisKyc($kyc);
            }
            
            DB::commit();
            
            $message = 'KYC ' . strtolower($status) . ' avec succès pour ' . $kyc->prenom . ' ' . $kyc->nom;
            
            return redirect()->route('kyc.show', $kyc)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la création du KYC: ' . $e->getMessage(), [
                'user_id' => $data['user_id'] ?? null,
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la création du KYC : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche les détails d'un KYC
     *
     * @param Kyc $kyc
     * @return \Illuminate\View\View
     */
    public function show(Kyc $kyc)
    {
        // Charger les relations nécessaires avec tri
        $kyc->load([
            'user', 
            'client', 
            'client.puces' => function($query) {
                $query->orderBy('numero_serie');
            }
        ]);
        
        // Déterminer les actions disponibles en fonction du statut
        $availableActions = [];
        
        if ($kyc->isEnCours()) {
            $availableActions['validate'] = route('kyc.validate', $kyc);
            $availableActions['reject'] = route('kyc.reject', $kyc);
        }
        
        // Si le KYC est validé mais qu'aucun client n'existe encore
        $peutCreerClient = $kyc->isValide() && !$kyc->client;
        
        // Récupérer les puces disponibles pour lier au client
        $pucesDisponibles = [];
        if ($kyc->client) {
            $pucesDisponibles = Puce::where('statut', Puce::STATUS_LIBRE)
                ->orWhere('client_id', $kyc->client->id)
                ->orderBy('numero_serie')
                ->get();
        }
        
        // Statistiques pour les badges d'information
        $statistiques = [
            'puces_attribuees' => $kyc->client ? $kyc->client->puces->count() : 0,
            'puces_actives' => $kyc->client ? $kyc->client->puces->where('statut', Puce::STATUS_ACTIVE)->count() : 0,
            'puces_suspendues' => $kyc->client ? $kyc->client->puces->where('statut', Puce::STATUS_SUSPENDUE)->count() : 0,
        ];
        
        // Historique des actions sur le KYC (à implémenter si nécessaire)
        $historique = [];
        
        return view('kyc.show', [
            'kyc' => $kyc,
            'availableActions' => $availableActions,
            'pucesDisponibles' => $pucesDisponibles,
            'peutCreerClient' => $peutCreerClient,
            'statistiques' => $statistiques,
            'historique' => $historique,
            'statusLabels' => [
                Kyc::STATUS_EN_COURS => 'En cours de traitement',
                Kyc::STATUS_VALIDE => 'Validé',
                Kyc::STATUS_REJETE => 'Rejeté',
            ],
            'statusBadgeClasses' => [
                Kyc::STATUS_EN_COURS => 'bg-blue-100 text-blue-800',
                Kyc::STATUS_VALIDE => 'bg-green-100 text-green-800',
                Kyc::STATUS_REJETE => 'bg-red-100 text-red-800',
            ]
        ]);
    }

    /**
     * Valider un KYC
     *
     * @param Request $request
     * @param Kyc $kyc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function validateKyc(Request $request, Kyc $kyc)
    {
        // Vérifier que le KYC est bien en statut 'EnCours'
        if (!$kyc->isEnCours()) {
            return redirect()->route('kyc.show', $kyc)
                ->with('error', 'Seul un KYC en cours peut être validé.');
        }
        
        // Valider la requête
        $validated = $request->validate([
            'creer_client' => 'sometimes|boolean',
            'attribuer_puce' => 'required_with:creer_client|boolean',
            'puce_id' => 'required_if:attribuer_puce,1|exists:puces,id,status,' . Puce::STATUS_LIBRE,
        ], [
            'puce_id.required_if' => 'Veuillez sélectionner une puce à attribuer.',
            'puce_id.exists' => 'La puce sélectionnée n\'est pas disponible ou n\'existe pas.'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le statut du KYC
            $kyc->status = Kyc::STATUS_VALIDE;
            $kyc->validated_at = now();
            $kyc->validated_by = auth()->id();
            $kyc->raison_rejet = null; // Effacer toute raison de rejet précédente
            $kyc->save();
            
            // Mettre à jour le statut de l'utilisateur
            $kyc->user->statut_kyc = User::KYC_VALIDE;
            $kyc->user->save();
            
            $message = 'KYC validé avec succès';
            
            // Créer automatiquement un client si demandé
            if ($request->boolean('creer_client', false)) {
                $client = $this->creerClientDepuisKyc($kyc);
                
                // Lier automatiquement une puce si demandé
                if ($request->boolean('attribuer_puce', false)) {
                    $puceId = $request->input('puce_id');
                    $puce = Puce::findOrFail($puceId);
                    
                    // Vérifier que la puce est bien libre
                    if ($puce->statut !== Puce::STATUS_LIBRE) {
                        throw new \Exception('La puce sélectionnée n\'est plus disponible.');
                    }
                    
                    // Attribuer la puce au client
                    $puce->client_id = $client->id;
                    $puce->statut = Puce::STATUS_ATTRIBUEE;
                    $puce->date_activation = now();
                    $puce->save();
                    
                    $message .= ' et client créé avec la puce ' . $puce->numero_serie . ' attribuée.';
                } else {
                    $message .= ' et client créé sans attribution de puce.';
                }
                
                // Ajouter un message si des puces sont disponibles mais non attribuées
                $pucesDisponibles = Puce::where('statut', Puce::STATUS_LIBRE)->exists();
                if ($pucesDisponibles && !$request->boolean('attribuer_puce', false)) {
                    session()->flash('info', 'Des puces sont disponibles pour attribution.');
                }
            }
            
            DB::commit();
            
            Log::info('KYC validé', [
                'kyc_id' => $kyc->id,
                'user_id' => $kyc->user_id,
                'validated_by' => auth()->id(),
                'client_created' => $request->boolean('creer_client', false),
                'puce_attribuee' => $request->boolean('attribuer_puce', false)
            ]);
            
            return redirect()->route('kyc.show', $kyc)
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la validation du KYC: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la validation du KYC : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche le formulaire d'édition d'un KYC
     *
     * @param Kyc $kyc
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Kyc $kyc)
    {
        // Vérifier que le KYC peut être modifié (uniquement si en cours)
        if (!$kyc->isEnCours()) {
            return redirect()->route('kyc.show', $kyc)
                ->with('error', 'Seul un KYC en cours peut être modifié.');
        }
        
        // Liste des pays pour le select
        $pays = [
            'France' => 'France',
            'Belgique' => 'Belgique',
            'Suisse' => 'Suisse',
            'Luxembourg' => 'Luxembourg',
            'Canada' => 'Canada',
            'Autre' => 'Autre',
        ];
        
        return view('kyc.edit', [
            'kyc' => $kyc,
            'pays' => $pays,
            'statusOptions' => [
                Kyc::STATUS_EN_COURS => 'En cours de traitement',
                Kyc::STATUS_VALIDE => 'Validé',
                Kyc::STATUS_REJETE => 'Rejeté',
            ]
        ]);
    }
    
    /**
     * Met à jour un KYC existant
     *
     * @param Request $request
     * @param Kyc $kyc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Kyc $kyc)
    {
        // Vérifier que le KYC peut être modifié (uniquement si en cours)
        if (!$kyc->isEnCours()) {
            return redirect()->route('kyc.show', $kyc)
                ->with('error', 'Seul un KYC en cours peut être modifié.');
        }
        
        // Règles de validation
        $rules = [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'nationalite' => 'required|string|max:50',
            'telephone' => 'required|string|max:20|regex:/^[0-9+\s\-\(\)\.]{10,20}$/',
            'adresse_postale' => 'required|string|max:255',
            'numero_npi' => [
                'required',
                'string',
                'size:10',
                'regex:/^[0-9]{10}$/',
                Rule::unique('kycs', 'numero_npi')->ignore($kyc->id)
            ],
        ];
        
        $validated = $request->validate($rules, [
            'numero_npi.regex' => 'Le numéro NPI doit contenir exactement 10 chiffres.',
            'numero_npi.unique' => 'Ce numéro NPI est déjà utilisé par un autre KYC.',
            'telephone.regex' => 'Le format du numéro de téléphone est invalide.'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le KYC
            $kyc->update($validated);
            
            // Journalisation
            Log::info('KYC mis à jour', [
                'kyc_id' => $kyc->id,
                'updated_by' => auth()->id(),
                'changes' => $validated
            ]);
            
            DB::commit();
            
            return redirect()->route('kyc.show', $kyc)
                ->with('success', 'Le KYC a été mis à jour avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la mise à jour du KYC: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du KYC : ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Supprime un KYC
     *
     * @param Kyc $kyc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Kyc $kyc)
    {
        // Vérifier que le KYC peut être supprimé (uniquement si en cours)
        if (!$kyc->isEnCours()) {
            return redirect()->route('kyc.show', $kyc)
                ->with('error', 'Seul un KYC en cours peut être supprimé.');
        }
        
        try {
            DB::beginTransaction();
            
            $userId = $kyc->user_id;
            
            // Supprimer le KYC
            $kyc->delete();
            
            // Mettre à jour le statut de l'utilisateur
            $user = User::find($userId);
            if ($user) {
                $user->statut_kyc = User::KYC_NON_SOUMIS;
                $user->save();
            }
            
            // Journalisation
            Log::info('KYC supprimé', [
                'kyc_id' => $kyc->id,
                'user_id' => $userId,
                'deleted_by' => auth()->id()
            ]);
            
            DB::commit();
            
            return redirect()->route('kyc.index')
                ->with('success', 'Le KYC a été supprimé avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de la suppression du KYC: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du KYC : ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Attribue une puce à un client à partir d'un KYC validé
     *
     * @param Request $request
     * @param Kyc $kyc
     * @return \Illuminate\Http\JsonResponse
     */
    public function attribuerPuce(Request $request, Kyc $kyc)
    {
        // Vérifier que le KYC est validé et a un client
        if (!$kyc->isValide() || !$kyc->client) {
            return response()->json([
                'success' => false,
                'message' => 'Le KYC doit être validé et associé à un client pour attribuer une puce.'
            ], 400);
        }
        
        $validated = $request->validate([
            'puce_id' => 'required|exists:puces,id,statut,' . Puce::STATUS_LIBRE,
        ]);
        
        try {
            DB::beginTransaction();
            
            $puce = Puce::findOrFail($validated['puce_id']);
            
            // Vérifier que la puce est bien libre
            if ($puce->statut !== Puce::STATUS_LIBRE) {
                throw new \Exception('La puce sélectionnée n\'est plus disponible.');
            }
            
            // Attribuer la puce au client
            $puce->client_id = $kyc->client->id;
            $puce->statut = Puce::STATUS_ATTRIBUEE;
            $puce->date_activation = now();
            $puce->save();
            
            // Journalisation
            Log::info('Puce attribuée à un client', [
                'puce_id' => $puce->id,
                'client_id' => $kyc->client->id,
                'kyc_id' => $kyc->id,
                'attributed_by' => auth()->id()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'La puce a été attribuée avec succès au client.',
                'puce' => [
                    'id' => $puce->id,
                    'numero_serie' => $puce->numero_serie,
                    'statut' => $puce->statut,
                    'date_activation' => $puce->date_activation->format('d/m/Y H:i')
                ]
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors de l\'attribution de la puce: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'puce_id' => $request->input('puce_id'),
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'attribution de la puce : ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Rejeter un KYC
     *
     * @param Request $request
     * @param Kyc $kyc
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, Kyc $kyc)
    {
        // Vérifier que le KYC est bien en statut 'EnCours'
        if (!$kyc->isEnCours()) {
            return redirect()->route('kyc.show', $kyc)
                ->with('error', 'Seul un KYC en cours peut être rejeté.');
        }
        
        // Valider la requête avec des messages personnalisés
        $validatedData = $request->validate([
            'raison_rejet' => 'required|string|min:10|max:1000',
        ], [
            'raison_rejet.required' => 'La raison du rejet est obligatoire.',
            'raison_rejet.min' => 'La raison du rejet doit contenir au moins :min caractères.',
            'raison_rejet.max' => 'La raison du rejet ne peut pas dépasser :max caractères.'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Mettre à jour le statut du KYC
            $kyc->status = Kyc::STATUS_REJETE;
            $kyc->raison_rejet = $validatedData['raison_rejet'];
            $kyc->rejected_at = now();
            $kyc->rejected_by = auth()->id();
            $kyc->save();
            
            // Mettre à jour le statut de l'utilisateur
            $kyc->user->statut_kyc = User::KYC_REJETE;
            $kyc->user->save();
            
            // Si un client existait pour ce KYC, le marquer comme inactif
            if ($kyc->client) {
                $client = $kyc->client;
                $client->update(['statut' => Client::STATUS_INACTIF]);
                
                // Libérer les puces associées
                Puce::where('client_id', $client->id)
                    ->update([
                        'client_id' => null,
                        'status' => Puce::STATUS_LIBRE,
                        'date_desactivation' => now()
                    ]);
            }
            
            // Journalisation de l'action
            Log::info('KYC rejeté', [
                'kyc_id' => $kyc->id,
                'user_id' => $kyc->user_id,
                'rejected_by' => auth()->id(),
                'client_affected' => $kyc->client ? true : false
            ]);
            
            DB::commit();
            
            // Envoyer une notification à l'utilisateur (à implémenter si nécessaire)
            // Notification::send($kyc->user, new KycRejected($kyc));
            
            return redirect()->route('kyc.show', $kyc)
                ->with('success', 'Le KYC a été rejeté avec succès.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erreur lors du rejet du KYC: ' . $e->getMessage(), [
                'kyc_id' => $kyc->id,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);
            
            return back()
                ->with('error', 'Une erreur est survenue lors du rejet du KYC : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Recherche KYC par nom/prenom/numéro NPI + filtre status
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function search(Request $request)
    {
        $query = Kyc::with(['user', 'client']);
        
        // Filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', '%'.$search.'%')
                  ->orWhere('prenom', 'like', '%'.$search.'%')
                  ->orWhere('numero_npi', 'like', '%'.$search.'%')
                  ->orWhere('telephone', 'like', '%'.$search.'%');
            });
        }
        
        // Filtre par statut
        if ($request->filled('status') && in_array($request->status, [
            Kyc::STATUS_EN_COURS, 
            Kyc::STATUS_VALIDE, 
            Kyc::STATUS_REJETE
        ])) {
            $query->where('status', $request->status);
            $status = $request->status;
        } else {
            $status = Kyc::STATUS_EN_COURS;
        }
        
        // Pagination et tri
        $kycs = $query->orderByDesc('created_at')
            ->paginate(15)
            ->appends($request->except('page'));
        
        // Compter les KYC par statut pour les onglets
        $counts = [
            Kyc::STATUS_EN_COURS => Kyc::where('status', Kyc::STATUS_EN_COURS)->count(),
            Kyc::STATUS_VALIDE => Kyc::where('status', Kyc::STATUS_VALIDE)->count(),
            Kyc::STATUS_REJETE => Kyc::where('status', Kyc::STATUS_REJETE)->count(),
        ];
        
        return view('kyc.index', compact('kycs', 'status', 'counts'));
    }
    
    /**
     * Crée un client à partir d'un KYC validé
     *
     * @param Kyc $kyc
     * @return \App\Models\Client
     * @throws \Exception
     */
    protected function creerClientDepuisKyc(Kyc $kyc)
    {
        // Vérifier que le KYC est bien validé
        if (!$kyc->isValide()) {
            throw new \Exception('Impossible de créer un client à partir d\'un KYC non validé.');
        }
        
        // Vérifier qu'un client n'existe pas déjà pour ce KYC
        if ($kyc->client) {
            return $kyc->client;
        }
        
        // Créer le client avec les informations du KYC
        $client = new Client([
            'user_id' => $kyc->user_id,
            'kyc_id' => $kyc->id,
            'nom' => $kyc->nom,
            'prenom' => $kyc->prenom,
            'email' => $kyc->user->email,
            'telephone' => $kyc->telephone,
            'adresse' => $kyc->adresse_postale,
            'pays' => $kyc->nationalite,
            'numero_npi' => $kyc->numero_npi,
            'statut' => Client::STATUS_ACTIF,
        ]);
        
        $client->save();
        
        // Mettre à jour la référence du client dans le KYC
        $kyc->client_id = $client->id;
        $kyc->save();
        
        return $client;
    }
}
