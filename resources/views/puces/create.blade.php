@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Ajouter une puce</h2>
    <form method="POST" action="{{ route('puces.store') }}" class="flex flex-col items-center">
        @csrf
        <div class="mb-4 w-full">
            <label class="block mb-1 font-semibold">Clé unique</label>
            <input name="cle_unique" value="{{ old('cle_unique') }}" required autofocus placeholder="Exemple : PUCE-123456" class="w-full border rounded px-3 py-2 text-lg">
            @error('cle_unique')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
            <p class="text-gray-500 text-xs mt-1">La latitude, la longitude et le statut seront générés automatiquement.</p>
        </div>
        <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
            </svg>
            Ajouter
        </button>
    </form>
</div>
@endsection
