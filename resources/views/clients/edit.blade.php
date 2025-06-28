@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8 mb-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-2xl font-bold mb-6 text-primary flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-accent" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
        </svg>
        {{ $client->prenom }} {{ $client->nom }}
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Informations du client -->
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-accent" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                Informations du client
            </h3>
            
            <form method="POST" action="{{ route('clients.update', $client) }}">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                    <select name="statusActif" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent">
                        <option value="Active" @if($client->statusActif=='Active') selected @endif>Actif</option>
                        <option value="Inactive" @if($client->statusActif=='Inactive') selected @endif>Inactif</option>
                    </select>
                    @error('statusActif')
                        <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-5 py-2 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Puces attribuées -->
        <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
            <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-accent" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                </svg>
                Puces attribuées ({{ $client->puces->count() }})
            </h3>
            
            @if($client->puces->isEmpty())
                <div class="text-center py-4 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p>Aucune puce attribuée à ce client.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($client->puces as $puce)
                        <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 shadow-sm hover:shadow transition-shadow">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-50 rounded-full mr-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $puce->numero_puce }}</div>
                                    <div class="text-sm text-gray-500">
                                        @if($puce->cle_unique)
                                            <span class="ml-2 text-xs text-gray-400">Clé: {{ $puce->cle_unique }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($client->puces->count() > 1)
                                <form action="{{ route('clients.puces.remove', ['client' => $client, 'puce' => $puce]) }}" method="POST" class="inline" >
                                    @csrf
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-sm text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9z" clip-rule="evenodd" />
                    </svg>
                    Un client doit toujours avoir au moins une puce attribuée.
                </div>
            @endif
        </div>
    </div>
</div>

@if(session('success')) 
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center" id="success-message">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center" id="error-message">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('error') }}
    </div>
@endif

@push('scripts')
<script>
    // Masquer les messages de succès/erreur après 5 secondes
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.transition = 'opacity 0.5s';
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }, 5000);
        }
        
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.transition = 'opacity 0.5s';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }, 5000);
        }
    });
</script>
@endpush
@endsection