@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Détail du client</h2>
    <div class="mb-4">
        <div class="font-semibold">Nom :</div>
        <div>{{ $client->nom }}</div>
    </div>
    <div class="mb-4">
        <div class="font-semibold">Prénom :</div>
        <div>{{ $client->prenom }}</div>
    </div>
    <div class="mb-4">
        <div class="font-semibold">Email :</div>
        <div>{{ $client->user->email ?? '-' }}</div>
    </div>
    <div class="mb-4">
        <div class="font-semibold">Statut :</div>
        <span class="px-2 py-1 rounded text-xs @if($client->statusActif=='Active') bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">{{ $client->statusActif }}</span>
    </div>
    <div class="mb-4">
        <div class="font-semibold">Puces attribuées :</div>
        <ul class="list-disc ml-6">
            @php
                $pucesAttribuees = $client->puces->where('status', 'Attribuee');
            @endphp
            @if($pucesAttribuees->count())
                @foreach($pucesAttribuees as $puce)
                    <li>{{ $puce->cle_unique }} ({{ $puce->latitude }}, {{ $puce->longitude }})</li>
                @endforeach
            @else
                <li class="text-gray-500 italic">Aucune puce attribuée à ce client</li>
            @endif
        </ul>
    </div>
    <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Retour à la liste</a>
</div>
@endsection
