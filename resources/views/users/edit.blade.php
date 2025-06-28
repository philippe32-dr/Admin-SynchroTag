@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-10 bg-white rounded-2xl shadow-lg p-8">
    <h2 class="text-2xl font-bold mb-6 text-primary">Modifier l'utilisateur</h2>
    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block mb-1 font-semibold">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $user->nom) }}" required class="w-full border rounded p-2 focus:ring-primary">
            @error('nom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Prénom</label>
            <input type="text" name="prenom" value="{{ old('prenom', $user->prenom) }}" required class="w-full border rounded p-2 focus:ring-primary">
            @error('prenom') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full border rounded p-2 focus:ring-primary">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label class="block mb-1 font-semibold">Status</label>
            <select name="status" class="w-full border rounded p-2 focus:ring-primary">
                <option value="Active" @if(old('status', $user->status)=='Active') selected @endif>Active</option>
                <option value="Inactive" @if(old('status', $user->status)=='Inactive') selected @endif>Inactive</option>
            </select>
            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <div class="border-t border-gray-200 my-6 pt-4">
            <h3 class="text-lg font-semibold mb-3">Changer le mot de passe</h3>
            <p class="text-sm text-gray-500 mb-4">Laissez ces champs vides pour conserver le mot de passe actuel.</p>
            
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Nouveau mot de passe</label>
                <input type="password" name="password" class="w-full border rounded p-2 focus:ring-primary">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" class="w-full border rounded p-2 focus:ring-primary">
            </div>
        </div>
        
        <div class="flex gap-4 mt-6">
            <button type="submit" class="flex-1 bg-gradient-to-r from-primary to-accent text-white font-semibold py-2 rounded shadow">Enregistrer</button>
            <a href="{{ route('users.index') }}" class="flex-1 text-center bg-gray-100 text-primary font-semibold py-2 rounded border">Annuler</a>
        </div>
    </form>
</div>
@endsection
