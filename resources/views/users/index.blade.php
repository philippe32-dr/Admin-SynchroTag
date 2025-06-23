{{-- Liste des utilisateurs --}}
@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto mt-8">
    {{-- Notifications Toasts --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 3000)" 
            x-show="show" 
            x-transition:leave="transition ease-in duration-200" 
            x-transition:leave-start="opacity-100 translate-y-0" 
            x-transition:leave-end="opacity-0 -translate-y-2" 
            class="fixed top-6 right-6 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded shadow-lg flex items-center gap-2"
            style="min-width: 220px;"
        >
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ $errors->first() }}
        </div>
    @endif
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        {{-- Formulaire recherche/filtrage --}}
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="q" placeholder="Nom, prénom ou email" value="{{ request('q') }}" class="border rounded p-2 focus:ring-primary" />
            <select name="status" class="border rounded p-2">
                <option value="">Status</option>
                <option value="Active" @if(request('status')=='Active') selected @endif>Active</option>
                <option value="Inactive" @if(request('status')=='Inactive') selected @endif>Inactive</option>
            </select>
            <select name="statut_kyc" class="border rounded p-2">
                <option value="">Statut KYC</option>
                <option value="NonSoumis" @if(request('statut_kyc')=='NonSoumis') selected @endif>Non soumis</option>
                <option value="EnCours" @if(request('statut_kyc')=='EnCours') selected @endif>En cours</option>
                <option value="Valide" @if(request('statut_kyc')=='Valide') selected @endif>Validé</option>
                <option value="Rejete" @if(request('statut_kyc')=='Rejete') selected @endif>Rejeté</option>
            </select>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white font-semibold px-4 py-2 rounded">Rechercher</button>
        </form>
        <a href="{{ route('users.create') }}" class="bg-gradient-to-r from-primary to-accent text-white font-semibold px-4 py-2 rounded shadow">+ Ajouter</a>
    </div>
    {{-- Tableau utilisateurs --}}
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut KYC</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-2">{{ $user->id }}</td>
                        <td class="px-4 py-2">{{ $user->nom }}</td>
                        <td class="px-4 py-2">{{ $user->prenom }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs {{ $user->status=='Active' ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500' }}">{{ $user->status }}</span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs
                                @if($user->statut_kyc=='NonSoumis') bg-gray-100 text-gray-500
                                @elseif($user->statut_kyc=='EnCours') bg-yellow-100 text-yellow-800
                                @elseif($user->statut_kyc=='Valide') bg-green-100 text-green-700
                                @else bg-red-100 text-red-700 @endif
                            ">{{ $user->statut_kyc }}</span>
                        </td>
                        <td class="px-4 py-2 text-center flex gap-2 justify-center">
                            <a href="{{ route('users.edit', $user) }}" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded shadow text-xs">Modifier</a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Désactiver cet utilisateur ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-gradient-to-r from-red-400 to-red-600 text-white px-3 py-1 rounded shadow text-xs">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-gray-500">Aucun utilisateur trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $users->withQueryString()->links() }}</div>
</div>
@endsection
