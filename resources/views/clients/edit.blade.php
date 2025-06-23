@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Modifier client</h2>
    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Statut</label>
            <select name="statusActif" class="w-full border rounded px-3 py-2">
                <option value="Active" @if($client->statusActif=='Active') selected @endif>Actif</option>
                <option value="Inactive" @if($client->statusActif=='Inactive') selected @endif>Inactif</option>
            </select>
            @error('statusActif')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Annuler</a>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
