@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Nouveau KYC</h2>
    
    @if($users->isEmpty())
        <div class="mb-4 p-4 bg-yellow-50 rounded-lg">
            Aucun utilisateur n'est éligible pour la création d'un KYC.
        </div>
    @else
        <form method="POST" action="{{ route('kyc.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Utilisateur</label>
                <select name="user_id" required class="w-full border rounded px-3 py-2">
                    <option value="">Sélectionner un utilisateur</option>
                    @foreach($users as $user)
    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
        {{ $user->full_name }}
    </option>
@endforeach
                </select>
                @error('user_id')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold">Nationalité</label>
                <input type="text" name="nationalite" value="{{ old('nationalite') }}" required 
                       class="w-full border rounded px-3 py-2">
                @error('nationalite')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold">Téléphone</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" required 
                       class="w-full border rounded px-3 py-2">
                @error('telephone')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold">Adresse postale</label>
                <textarea name="adresse_postale" required 
                          class="w-full border rounded px-3 py-2">{{ old('adresse_postale') }}</textarea>
                @error('adresse_postale')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-semibold">Numéro NPI</label>
                <input type="text" name="numero_npi" value="{{ old('numero_npi') }}" required 
                       class="w-full border rounded px-3 py-2" maxlength="50">
                @error('numero_npi')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-200">
                <a href="{{ route('kyc.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:opacity-90 transition-opacity">
                    Valider le KYC
                </button>
            </div>
        </form>
    @endif
</div>
@endsection
