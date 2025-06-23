@extends('layouts.app')
@section('content')
<div class="max-w-2xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Ajouter un client</h2>
    <form method="POST" action="{{ route('clients.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Utilisateur (KYC validé)</label>
            <select name="user_id" class="w-full border rounded px-3 py-2">
                <option value="">Sélectionner...</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @if(old('user_id')==$user->id) selected @endif>{{ $user->nom }} {{ $user->prenom }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('user_id')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4"
            x-data="{
                puces: [],
                selected: [],
                search: '',
                get filteredPuces() {
                    let s = this.search.toLowerCase();
                    return this.puces.filter(p => !this.selected.map(String).includes(String(p.id)) && p.cle_unique.toLowerCase().includes(s));
                },
                addPuce(puce) {
                    const idStr = String(puce.id);
                    if (!this.selected.map(String).includes(idStr)) {
                        this.selected.push(idStr);
                    }
                    this.search = '';
                },
                removePuce(id) {
                    const idStr = String(id);
                    this.selected = this.selected.filter(i => String(i) !== idStr);
                }
            }"
            x-init='
                puces = JSON.parse($refs.pucesjson.textContent);
                selected = JSON.parse($refs.pucesold.textContent);
            '
        >
            <span x-show="$root" class="text-green-600 text-xs font-bold">Alpine OK</span>
            <label class="block mb-1 font-semibold">Puces à attribuer (obligatoire)</label>
            <template x-if="!puces.length">
                <div class="text-gray-400 italic">Aucune puce libre disponible.</div>
            </template>
            <template x-if="puces.length">
                <div>
                    <input type="text" x-model="search" placeholder="Rechercher une puce par clé unique..." class="w-full border px-3 py-2 rounded mb-3" autocomplete="off">
                    <div class="flex flex-wrap gap-2 mb-2">
                        <template x-for="puce in filteredPuces" :key="puce.id">
                            <button type="button" @click="addPuce(puce)" class="bg-primary/10 border border-primary text-primary rounded-full px-3 py-1 text-xs hover:bg-primary/20 transition flex items-center animate-pulse-once">
                                <span x-text="puce.cle_unique"></span>
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </template>
                        <span x-show="!filteredPuces.length && search.length > 0" class="text-gray-400 italic">Aucune puce trouvée.</span>
                    </div>
                    <div class="mb-2">
                        <template x-for="id in selected" :key="id">
                            <span class="inline-flex items-center bg-accent/10 border border-accent text-accent rounded-full px-3 py-1 text-xs mr-2 mb-1 animate-pulse-once">
                                <span x-text="puces.find(p=>String(p.id)===String(id))?.cle_unique"></span>
                                <button type="button" @click="removePuce(id)" class="ml-2 text-red-500 hover:text-red-700">&times;</button>
                                <input type="hidden" name="puces[]" :value="id">
                            </span>
                        </template>
                        <span x-show="!selected.length" class="text-gray-400 italic">Aucune puce sélectionnée.</span>
                    </div>
                </div>
            </template>
            @error('puces')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <noscript>
                <div class="text-gray-500 text-xs">JavaScript désactivé : sélectionnez les puces via la liste ci-dessous.</div>
                <select name="puces[]" multiple required class="w-full border rounded px-3 py-2 h-32">
                    @foreach($pucesLibres as $puce)
                        <option value="{{ $puce->id }}">{{ $puce->cle_unique }}</option>
                    @endforeach
                </select>
            </noscript>
            <span x-ref="pucesjson" class="hidden">@json($pucesLibres->map(function($p){return ['id'=>$p->id,'cle_unique'=>$p->cle_unique];})->values())</span>
            <span x-ref="pucesold" class="hidden">@json(old('puces', []))</span>
        </div>
        <style>
        .animate-pulse-once {
            animation: pulseOnce 0.3s;
        }
        @keyframes pulseOnce {
            0% { box-shadow: 0 0 0 0 #1BB4D880; }
            70% { box-shadow: 0 0 0 6px #1BB4D800; }
            100% { box-shadow: 0 0 0 0 #1BB4D800; }
        }
        </style>

        <div class="flex justify-end gap-2">
            <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Annuler</a>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition">Créer</button>
        </div>
    </form>
</div>
@endsection
