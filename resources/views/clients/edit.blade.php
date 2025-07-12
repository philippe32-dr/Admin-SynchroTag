@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8 mb-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-2xl font-bold mb-6 text-primary flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mr-2 text-accent" viewBox="0 0 20 20" fill="currentColor">
            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
        </svg>
        {{ $client->prenom }} {{ $client->nom }}
    </h2>
    
    <form method="POST" action="{{ route('clients.update', $client) }}" id="client-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="action" value="update_client">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Informations du client -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-100 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Informations du client</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nom" class="block text-sm font-medium text-gray-600">Nom</label>
                        <input type="text" name="nom" id="nom" value="{{ old('nom', $client->nom) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                    <div>
                        <label for="prenom" class="block text-sm font-medium text-gray-600">Prénom</label>
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $client->prenom) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $client->user->email) }}" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent">
                    </div>
                    <div>
                        <label for="statusActif" class="block text-sm font-medium text-gray-600">Statut</label>
                        <select name="statusActif" id="statusActif" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent">
                            <option value="Active" {{ old('statusActif', $client->statusActif) == 'Active' ? 'selected' : '' }}>Actif</option>
                            <option value="Inactive" {{ old('statusActif', $client->statusActif) == 'Inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                        @error('statusActif')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-600">Mot de passe</label>
                        <input type="password" name="password" id="password" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent" placeholder="Nouveau mot de passe">
                        @error('password')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-600">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent" placeholder="Confirmer mot de passe">
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" name="action" value="update_client" class="bg-gradient-to-r from-primary to-accent text-white px-5 py-2 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Enregistrer
                    </button>
                </div>
            </div>
            
            <!-- Puces attribuées -->
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-100 mb-6">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Gestion des puces</h2>
                <div class="space-y-4">
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
                            <div class="relative mb-3">
                                <input type="text" id="assigned-puce-search" placeholder="Rechercher par numéro ou clé unique..." class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent pl-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="flex items-center text-sm font-medium text-gray-700">
                                    <input type="checkbox" id="select-all-assigned" class="mr-2 rounded border-gray-300 text-accent focus:ring-accent">
                                    Tout sélectionner
                                </label>
                                <span id="assigned-selected-count" class="text-sm text-gray-500">0 puce(s) sélectionnée(s)</span>
                            </div>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                @foreach($client->puces as $puce)
                                    <label class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0 assigned-puce-item" data-numero="{{ strtolower($puce->numero_puce) }}" data-cle="{{ strtolower($puce->cle_unique) }}">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="deassign_puces[]" value="{{ $puce->id }}" class="assigned-puce-checkbox rounded border-gray-300 text-accent focus:ring-accent mr-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $puce->numero_puce }}</div>
                                                <div class="text-sm text-gray-500">Clé: {{ $puce->cle_unique }}</div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            @if($client->puces->count() > 1)
                                <button type="submit" name="action" value="deassign_puces" class="mt-3 bg-gradient-to-r from-red-500 to-red-600 text-white px-5 py-2 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    Désattribuer les puces
                                </button>
                            @else
                                <div class="mt-2 text-gray-500 text-sm italic">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9z" clip-rule="evenodd" />
                                    </svg>
                                    Impossible de désattribuer - un client doit avoir au moins une puce
                                </div>
                            @endif
                            <div class="mt-4 text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 112 0v4a1 1 0 11-2 0V9z" clip-rule="evenodd" />
                                </svg>
                                Un client doit toujours avoir au moins une puce attribuée.
                            </div>
                        @endif
                    </div>
                    <div class="mt-4">
                        <label for="puce-search" class="block text-sm font-medium text-gray-600 mb-2">Attribuer de nouvelles puces</label>
                        <div class="relative mb-3">
                            <input type="text" id="puce-search" placeholder="Rechercher par numéro ou clé unique..." class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-accent focus:border-accent pl-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        @if($availablePuces->count() > 0)
                            <div class="flex items-center justify-between mb-3">
                                <label class="flex items-center text-sm font-medium text-gray-700">
                                    <input type="checkbox" id="select-all-puces" class="mr-2 rounded border-gray-300 text-accent focus:ring-accent">
                                    Tout sélectionner
                                </label>
                                <span id="selected-count" class="text-sm text-gray-500">0 puce(s) sélectionnée(s)</span>
                            </div>
                            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                                @foreach($availablePuces as $puce)
                                    <label class="flex items-center justify-between p-3 hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0 puce-item" data-numero="{{ strtolower($puce->numero_puce) }}" data-cle="{{ strtolower($puce->cle_unique) }}">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="new_puces[]" value="{{ $puce->id }}" class="puce-checkbox rounded border-gray-300 text-accent focus:ring-accent mr-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $puce->numero_puce }}</div>
                                                <div class="text-sm text-gray-500">Clé: {{ $puce->cle_unique }}</div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" name="action" value="assign_puces" class="mt-3 bg-gradient-to-r from-primary to-accent text-white px-5 py-2 rounded-lg shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 -mt-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Attribuer les puces
                            </button>
                            @error('new_puces')
                                <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                            @enderror
                        @else
                            <p class="text-gray-500 italic">Aucune puce libre disponible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
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

        // Gestion des puces disponibles
        const searchInput = document.getElementById('puce-search');
        const puceItems = document.querySelectorAll('.puce-item');
        const selectAll = document.getElementById('select-all-puces');
        const checkboxes = document.querySelectorAll('.puce-checkbox');
        const selectedCount = document.getElementById('selected-count');

        // Gestion des puces attribuées
        const assignedSearchInput = document.getElementById('assigned-puce-search');
        const assignedPuceItems = document.querySelectorAll('.assigned-puce-item');
        const selectAllAssigned = document.getElementById('select-all-assigned');
        const assignedCheckboxes = document.querySelectorAll('.assigned-puce-checkbox');
        const assignedSelectedCount = document.getElementById('assigned-selected-count');

        // Mettre à jour les compteurs
        function updateSelectedCount() {
            const selected = document.querySelectorAll('.puce-checkbox:checked').length;
            selectedCount.textContent = `${selected} puce(s) sélectionnée(s)`;
        }

        function updateAssignedSelectedCount() {
            const selected = document.querySelectorAll('.assigned-puce-checkbox:checked').length;
            assignedSelectedCount.textContent = `${selected} puce(s) sélectionnée(s)`;
        }

        // Recherche
        function setupSearch(input, items) {
            input.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                items.forEach(item => {
                    const numero = item.dataset.numero;
                    const cle = item.dataset.cle;
                    item.style.display = (numero.includes(query) || cle.includes(query)) ? 'flex' : 'none';
                });
            });
        }

        setupSearch(searchInput, puceItems);
        setupSearch(assignedSearchInput, assignedPuceItems);

        // Tout sélectionner
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => {
                if (checkbox.closest('.puce-item').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateSelectedCount();
        });

        selectAllAssigned.addEventListener('change', function() {
            assignedCheckboxes.forEach(checkbox => {
                if (checkbox.closest('.assigned-puce-item').style.display !== 'none') {
                    checkbox.checked = this.checked;
                }
            });
            updateAssignedSelectedCount();
        });

        // Mettre à jour les compteurs lors de la sélection individuelle
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                updateSelectedCount();
            });
        });

        assignedCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked) selectAllAssigned.checked = false;
                updateAssignedSelectedCount();
            });
        });
    });
</script>
@endpush
@endsection