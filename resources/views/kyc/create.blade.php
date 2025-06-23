@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Nouveau KYC</h2>
    <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Utilisateur</label>
            <select name="user_id" required class="w-full border rounded px-3 py-2">
                <option value="">Sélectionner un utilisateur...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if(old('user_id')==$user->id) selected @endif>{{ $user->nom }} {{ $user->prenom }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">Nationalité</label>
            <input name="nationalite" type="text" placeholder="Nationalité (ex: Français)" value="{{ old('nationalite') }}" required class="w-full border rounded px-3 py-2">
            @error('nationalite')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Téléphone</label>
            <input name="telephone" type="tel" placeholder="Numéro de téléphone" value="{{ old('telephone') }}" required class="w-full border rounded px-3 py-2">
            @error('telephone')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Adresse postale</label>
            <textarea name="adresse_postale" placeholder="Adresse postale complète" required class="w-full border rounded px-3 py-2">{{ old('adresse_postale') }}</textarea>
            @error('adresse_postale')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-6">
            <label class="block mb-1 font-semibold">Document CIP (PDF)</label>
            <input type="file" name="pdf_cip" accept="application/pdf" required class="w-full border rounded px-3 py-2" placeholder="Sélectionner un fichier PDF...">
            @error('pdf_cip')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('kyc.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Annuler</a>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition">Ajouter</button>
        </div>
    </form>
</div>
@endsection
