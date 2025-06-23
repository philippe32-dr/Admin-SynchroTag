@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto mt-8">
    {{-- Toasts --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="fixed top-6 right-6 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded shadow-lg flex items-center gap-2" style="min-width: 220px;">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="fixed top-6 right-6 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded shadow-lg flex items-center gap-2" style="min-width: 220px;">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-primary">Liste des clients</h1>
        <a href="{{ route('clients.create') }}" class="bg-gradient-to-r from-primary to-accent text-white px-5 py-2 rounded-lg shadow hover:scale-105 transition-transform">Ajouter un client</a>
    </div>
    <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-4 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher nom, prénom, email..." class="px-3 py-2 border rounded w-64">
        <select name="statusActif" class="px-3 py-2 border rounded">
            <option value="">Tous statuts</option>
            <option value="Active" @if(request('statusActif')=='Active') selected @endif>Actif</option>
            <option value="Inactive" @if(request('statusActif')=='Inactive') selected @endif>Inactif</option>
        </select>
        <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-4 py-2 rounded hover:scale-105 transition">Filtrer</button>
    </form>
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg ">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Nom</th>
                    <th class="px-4 py-2">Prénom</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Nbr Puces</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $client->id }}</td>
                        <td class="px-4 py-2">{{ $client->nom }}</td>
                        <td class="px-4 py-2">{{ $client->prenom }}</td>
                        <td class="px-4 py-2">{{ $client->user->email ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs @if($client->statusActif=='Active') bg-green-100 text-green-700 @else bg-red-100 text-red-700 @endif">{{ $client->statusActif }}</span>
                        </td>
                        <td class="px-4 py-2 text-center">{{ $client->puces->count() }}</td>
                        <td class="px-4 py-2 flex gap-2 justify-center">
                            <a href="{{ route('clients.edit', $client) }}" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded shadow text-xs">Modifier</a>
                            <form method="POST" action="{{ route('clients.deactivate', $client) }}" onsubmit="return confirm('Désactiver ce client ?');">
                                @csrf
                                <button type="submit" class="bg-gradient-to-r from-red-400 to-red-600 text-white px-3 py-1 rounded shadow text-xs">Désactiver</button>
                            </form>
                            <a href="{{ route('clients.show', $client) }}" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded shadow text-xs">Consulter</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-gray-500">Aucun client trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $clients->links() }}</div>
</div>
@endsection
