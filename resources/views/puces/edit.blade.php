@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Modifier puce</h2>
    <form method="POST" action="{{ route('puces.update', $puce) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Clé unique</label>
            <input name="cle_unique" value="{{ old('cle_unique', $puce->cle_unique) }}" required class="w-full border rounded px-3 py-2">
            @error('cle_unique')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Latitude</label>
            <input name="latitude" value="{{ old('latitude', $puce->latitude) }}" class="w-full border rounded px-3 py-2">
            @error('latitude')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Longitude</label>
            <input name="longitude" value="{{ old('longitude', $puce->longitude) }}" class="w-full border rounded px-3 py-2">
            @error('longitude')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Statut</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Libre" @if($puce->status=='Libre') selected @endif>Libre</option>
                <option value="Attribuee" @if($puce->status=='Attribuee') selected @endif>Attribuée</option>
            </select>
            @error('status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('puces.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Annuler</a>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
