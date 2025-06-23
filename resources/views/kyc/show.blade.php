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
        <span class="font-semibold">Statut :</span>
        <span class="px-2 py-1 rounded {{ $kyc->status=='Valide' ? 'bg-green-100 text-green-700' : ($kyc->status=='Rejete' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
            {{ $kyc->status }}
        </span>
    </div>
    <div class="mb-6">
        <span class="font-semibold">Document CIP :</span>
        @if($kyc->pdf_cip)
            <a href="{{ Storage::disk('public')->url($kyc->pdf_cip) }}" target="_blank" class="text-primary underline ml-2">Télécharger PDF</a>
            <iframe src="{{ Storage::disk('public')->url($kyc->pdf_cip) }}" class="w-full mt-4 rounded border" style="height:400px;"></iframe>
        @else
            <span class="text-gray-400">Aucun document</span>
        @endif
    </div>
    <div class="flex justify-end gap-2">
        @if($kyc->status !== 'Valide' && $kyc->status !== 'Rejete')
            <form action="{{ route('kyc.validate', $kyc) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">Valider</button>
            </form>
            <form action="{{ route('kyc.reject', $kyc) }}" method="POST" class="inline ml-2">
                @csrf
                <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Rejeter</button>
            </form>
        @endif
        <a href="{{ route('kyc.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Retour</a>
    </div>
</div>
@endsection
