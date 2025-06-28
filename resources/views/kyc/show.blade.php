@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Détail KYC</h2>
    <div class="mb-4">
        <span class="font-semibold">Utilisateur :</span> {{ $kyc->user->nom ?? '?' }} {{ $kyc->user->prenom ?? '' }} <span class='text-xs text-gray-500'>({{ $kyc->user->email ?? '' }})</span>
    </div>
    <div class="mb-4">
        <span class="font-semibold">Nom :</span> {{ $kyc->nom }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Prénom :</span> {{ $kyc->prenom }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Nationalité :</span> {{ $kyc->nationalite }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Téléphone :</span> {{ $kyc->telephone }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Adresse postale :</span> {{ $kyc->adresse_postale }}
    </div>
    <div class="mb-4">
        <span class="font-semibold">Numéro NPI :</span>
        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $kyc->numero_npi }}</span>
    </div>
    <div class="mb-6">
        <span class="font-semibold">Statut :</span>
        <span class="px-2 py-1 rounded {{ $kyc->status=='Valide' ? 'bg-green-100 text-green-700' : ($kyc->status=='Rejete' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
            {{ $kyc->status }}
        </span>
        
        @if($kyc->status === 'Rejete' && $kyc->raison_rejet)
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h4 class="font-semibold text-red-800 mb-2">Raison du rejet :</h4>
                <p class="text-red-700 whitespace-pre-line">{{ $kyc->raison_rejet }}</p>
                @if($kyc->rejected_at)
                    <p class="mt-2 text-sm text-red-600">
                        Rejeté le : {{ $kyc->rejected_at->format('d/m/Y à H:i') }}
                        @if($kyc->rejectedBy)
                            par {{ $kyc->rejectedBy->name }}
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>
    <div class="flex justify-end gap-2">
        @if($kyc->status !== 'Valide' && $kyc->status !== 'Rejete')
            <form action="{{ route('kyc.validate', $kyc) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Valider</button>
            </form>
            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" type="button" class="ml-2 px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                Rejeter
            </button>
        @endif

        <!-- Modal de rejet -->
        <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-left">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Motif du rejet</h3>
                    <form action="{{ route('kyc.reject', $kyc) }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-4">
                            <label for="raison_rejet" class="block text-sm font-medium text-gray-700">Raison du rejet <span class="text-red-500">*</span></label>
                            <textarea name="raison_rejet" id="raison_rejet" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required minlength="10" maxlength="1000"></textarea>
                            @error('raison_rejet')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                Confirmer le rejet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <a href="{{ route('kyc.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Retour</a>
    </div>
</div>
@endsection
