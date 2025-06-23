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
        <h1 class="text-2xl font-bold text-primary">Liste des puces</h1>
        <a href="{{ route('puces.create') }}" class="bg-gradient-to-r from-primary to-accent text-white px-5 py-2 rounded-lg shadow hover:scale-105 transition-transform">Ajouter une puce</a>
    </div>
    <form method="GET" action="{{ route('puces.index') }}" class="flex flex-wrap gap-4 mb-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher clé unique..." class="px-3 py-2 border rounded w-64">
        <select name="status" class="px-3 py-2 border rounded">
            <option value="">Tous statuts</option>
            <option value="Libre" @if(request('status')=='Libre') selected @endif>Libre</option>
            <option value="Attribuee" @if(request('status')=='Attribuee') selected @endif>Attribuée</option>
        </select>
        <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-4 py-2 rounded hover:scale-105 transition">Filtrer</button>
    </form>
    <div class="overflow-x-auto bg-white rounded-2xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Clé unique</th>
                    <th class="px-4 py-2">Latitude</th>
                    <th class="px-4 py-2">Longitude</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($puces as $puce)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $puce->id }}</td>
                        <td class="px-4 py-2">{{ $puce->cle_unique }}</td>
                        <td class="px-4 py-2">{{ $puce->latitude }}</td>
                        <td class="px-4 py-2">{{ $puce->longitude }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs @if($puce->status=='Libre') bg-gray-100 text-gray-500 @else bg-blue-100 text-blue-700 @endif">{{ $puce->status }}</span>
                        </td>
                        <td class="px-4 py-2 flex gap-2 justify-center">
                            <a href="{{ route('puces.edit', $puce) }}" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded shadow text-xs">Modifier</a>
                            @if(Str::lower($puce->status) == 'attribue' || Str::lower($puce->status) == 'attribuee')
                                <form method="POST" action="{{ route('puces.unassign', $puce) }}">
                                    @csrf
                                    <button type="submit" class="bg-gradient-to-r from-red-400 to-red-600 text-white px-3 py-1 rounded shadow text-xs">Libérer</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('puces.assign', $puce) }}">
                                    @csrf
                                    <input type="hidden" name="client_id" value="">
                                    <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-3 py-1 rounded shadow text-xs">Attribuer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-gray-500">Aucune puce trouvée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $puces->links() }}</div>
</div>
@endsection
