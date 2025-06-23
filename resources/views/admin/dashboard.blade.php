{{-- Dashboard d’administration --}}
@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
    {{-- Carte Utilisateurs --}}
    <div class="relative bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center border-4 border-transparent hover:scale-105 transition-transform group"
         style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg border-4 border-primary">
            <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M16 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        </div>
        <span class="text-xl font-bold text-text mt-10 mb-2">Utilisateurs</span>
        <div class="flex gap-6 mt-4">
            <div class="flex flex-col items-center">
                <span class="text-3xl font-extrabold text-primary">{{ $usersActifs }}</span>
                <span class="text-base text-gray-500">Actifs</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-3xl font-extrabold text-accent">{{ $usersInactifs }}</span>
                <span class="text-base text-gray-500">Inactifs</span>
            </div>
        </div>
    </div>
    {{-- Carte Clients --}}
    <div class="relative bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center border-4 border-transparent hover:scale-105 transition-transform group"
         style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg border-4 border-primary">
            <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        </div>
        <span class="text-xl font-bold text-text mt-10 mb-2">Clients</span>
        <span class="text-4xl font-extrabold text-primary mt-4">{{ $clients }}</span>
    </div>
    {{-- Carte Puces --}}
    <div class="relative bg-white rounded-2xl shadow-lg p-8 flex flex-col items-center border-4 border-transparent hover:scale-105 transition-transform group"
         style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
        <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-white rounded-full p-2 shadow-lg border-4 border-primary">
            <svg class="w-12 h-12 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01"/></svg>
        </div>
        <span class="text-xl font-bold text-text mt-10 mb-2">Puces</span>
        <div class="flex gap-6 mt-4">
            <div class="flex flex-col items-center">
                <span class="text-3xl font-extrabold text-primary">{{ $pucesLibres }}</span>
                <span class="text-base text-gray-500">Libres</span>
            </div>
            <div class="flex flex-col items-center">
                <span class="text-3xl font-extrabold text-accent">{{ $pucesAttribuees }}</span>
                <span class="text-base text-gray-500">Attribuées</span>
            </div>
        </div>
    </div>
</div>

{{-- Boutons d'accès rapide --}}
<div class="flex flex-col md:flex-row gap-8 justify-center mt-16">
    <a href="#" class="group flex-1 min-w-[220px] bg-white/60 backdrop-blur border-4 border-transparent rounded-2xl shadow-md flex flex-col items-center py-8 hover:scale-105 transition-all relative"
        style="border-image: linear-gradient(90deg, #1BB4D8, #90E0EF) 1;"
        title="Voir les clients">
        <svg class="w-12 h-12 mb-2 text-primary group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
        <span class="text-lg font-bold text-text">Accès Clients</span>
        <span class="absolute bottom-2 text-xs text-gray-500 opacity-80">Gérer les clients</span>
    </a>
    <a href="#" class="group flex-1 min-w-[220px] bg-white/60 backdrop-blur border-4 border-transparent rounded-2xl shadow-md flex flex-col items-center py-8 hover:scale-105 transition-all relative"
        style="border-image: linear-gradient(90deg, #1BB4D8, #90E0EF) 1;"
        title="Voir les puces">
        <svg class="w-12 h-12 mb-2 text-primary group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01"/></svg>
        <span class="text-lg font-bold text-text">Accès Puces</span>
        <span class="absolute bottom-2 text-xs text-gray-500 opacity-80">Gérer les puces</span>
    </a>
    <a href="#" class="group flex-1 min-w-[220px] bg-white/60 backdrop-blur border-4 border-transparent rounded-2xl shadow-md flex flex-col items-center py-8 hover:scale-105 transition-all relative"
        style="border-image: linear-gradient(90deg, #1BB4D8, #90E0EF) 1;"
        title="Voir les KYC">
        <svg class="w-12 h-12 mb-2 text-primary group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-lg font-bold text-text">Accès KYC</span>
        <span class="absolute bottom-2 text-xs text-gray-500 opacity-80">Gérer les KYC</span>
    </a>
</div>
@endsection
