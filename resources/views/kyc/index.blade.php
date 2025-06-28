@extends('layouts.app')
@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold mb-4 text-primary">Gestion des KYC</h2>
    @if(session('success'))
        <div x-data="{show:true}" x-show="show" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded" @click="show=false">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{show:true}" x-show="show" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded" @click="show=false">
            {{ session('error') }}
        </div>
    @endif
    <!-- Onglets status -->
    <div class="flex gap-2 mb-4">
        @foreach(['EnCours'=>'En cours','Valide'=>'Validés','Rejete'=>'Rejetés'] as $tab=>$label)
            <a href="{{ route('kyc.index', ['status'=>$tab]) }}" class="px-4 py-2 rounded-t-lg font-semibold {{ ($status??'EnCours')==$tab ? 'bg-gradient-to-r from-primary to-accent text-white' : 'bg-gray-100 text-primary' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <!-- Recherche/filtre -->
    <form action="{{ route('kyc.search') }}" method="POST" class="flex flex-wrap gap-2 mb-4 items-end">
        @csrf

        <select name="status" class="border rounded px-3 py-2">
            <option value="">Tous statuts</option>
            <option value="EnCours" @if(($status??'')=='EnCours') selected @endif>En cours</option>
            <option value="Valide" @if(($status??'')=='Valide') selected @endif>Validé</option>
            <option value="Rejete" @if(($status??'')=='Rejete') selected @endif>Rejeté</option>
        </select>
        <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-4 py-2 rounded">Rechercher</button>
        <a href="{{ route('kyc.create') }}" class="ml-auto bg-gradient-to-r from-primary to-accent text-white px-4 py-2 rounded">Nouveau KYC</a>
    </form>
    <!-- Tableau -->
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg border-4 border-transparent" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Utilisateur</th>
                    <th class="px-4 py-2">N° NPI</th>
                    <th class="px-4 py-2">Nationalité</th>
                    <th class="px-4 py-2">Téléphone</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kycs as $kyc)
                <tr>
                    <td class="px-4 py-2">{{ $kyc->id }}</td>
                    <td class="px-4 py-2">{{ $kyc->user->nom ?? '?' }} {{ $kyc->user->prenom ?? '' }} <span class='text-xs text-gray-500'>({{ $kyc->user->email ?? '' }})</span></td>
                    <td class="px-4 py-2 font-mono">{{ $kyc->numero_npi }}</td>
                    <td class="px-4 py-2">{{ $kyc->nationalite }}</td>
                    <td class="px-4 py-2">{{ $kyc->telephone }}</td>
                    <td class="px-4 py-2">
                        <span class="px-2 py-1 rounded {{ $kyc->status=='Valide' ? 'bg-green-100 text-green-700' : ($kyc->status=='Rejete' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $kyc->status }}
                        </span>
                    </td>
                    <td class="px-4 py-2 flex gap-2">
                        <a href="{{ route('kyc.show', $kyc) }}" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded">Voir</a>

                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-gray-400 py-8">Aucun KYC trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $kycs->links() }}</div>
</div>
<!-- Modale JS vanilla -->
<div id="modal-bg" style="display:none;position:fixed;inset:0;background:#0008;z-index:50;"></div>
<div id="modal-action" style="display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:51;" class="bg-white rounded-xl shadow-xl p-8 max-w-sm w-full text-center">
    <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>
    <form id="modal-form" method="POST">
        @csrf
        <button type="button" onclick="closeModal()" class="px-4 py-2 rounded bg-gray-200 text-gray-700 mr-2">Annuler</button>
        <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow">Confirmer</button>
    </form>
</div>
<script>
function openModal(action,id) {
    document.getElementById('modal-bg').style.display='block';
    document.getElementById('modal-action').style.display='block';
    if(action==='validate') {
        document.getElementById('modal-title').innerText = 'Valider ce KYC ?';
        document.getElementById('modal-form').action = '/admin/kyc/'+id+'/validate';
    } else {
        document.getElementById('modal-title').innerText = 'Rejeter ce KYC ?';
        document.getElementById('modal-form').action = '/admin/kyc/'+id+'/reject';
    }
}
function closeModal() {
    document.getElementById('modal-bg').style.display='none';
    document.getElementById('modal-action').style.display='none';
}
</script>
@endsection
