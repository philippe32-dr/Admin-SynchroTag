@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto mt-8 bg-white rounded-2xl shadow-lg border-4 border-transparent p-8" style="border-image: linear-gradient(135deg, #1BB4D8, #90E0EF) 1;">
    <h2 class="text-xl font-bold mb-6 text-primary">Modifier puce</h2>
    <form method="POST" action="{{ route('puces.update', $puce) }}">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Clé unique</label>
            <input name="cle_unique" value="{{ old('cle_unique', $puce->cle_unique) }}" required class="w-full border rounded px-3 py-2">
            @error('cle_unique')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Latitude</label>
            <input name="latitude" value="{{ old('latitude', $puce->latitude) }}" class="w-full border rounded px-3 py-2">
            @error('latitude')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Longitude</label>
            <input name="longitude" value="{{ old('longitude', $puce->longitude) }}" class="w-full border rounded px-3 py-2">
            @error('longitude')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="mb-4">
            <label class="block mb-1 font-semibold">Statut</label>
            <select name="status" class="w-full border rounded px-3 py-2">
                <option value="Libre" @if($puce->status=='Libre') selected @endif>Libre</option>
                <option value="Attribuee" @if($puce->status=='Attribuee') selected @endif>Attribuée</option>
            </select>
            @error('status')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        
        <div id="user-selection" class="mb-4 hidden">
            <label class="block mb-1 font-semibold">Attribuer à un utilisateur</label>
            <div class="relative">
                <div class="relative">
                    <input type="text" id="user-search" 
                           placeholder="Rechercher un utilisateur..." 
                           class="w-full border rounded px-3 py-2 pr-10 focus:ring-2 focus:ring-accent focus:border-accent"
                           autocomplete="off">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                <input type="hidden" name="user_id" id="user_id" value="{{ $puce->client->user_id ?? '' }}">
                <div id="search-results" class="absolute z-10 w-full mt-1 px-4 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto hidden">
                    <div class="px-3 py-2 text-sm text-gray-500">Chargement des utilisateurs...</div>
                </div>
            </div>
            <div id="selected-user" class="mt-2 p-2 bg-gray-50 rounded {{ $puce->client ? '' : 'hidden' }}">
                <div class="flex justify-between items-center cursor-pointer">
                    <span id="selected-user-name" class="text-sm ">
                        @if($puce->client && $puce->client->user)
                            {{ $puce->client->user->prenom }} {{ $puce->client->user->nom }}
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded ml-2">
                                {{ $puce->client->user->email }}
                            </span>
                        @endif
                    </span>
                    <button type="button" id="clear-selection" class="text-gray-400 hover:text-red-500 focus:outline-none">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            @error('user_id')
                <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('puces.index') }}" class="px-4 py-2 rounded bg-gray-200 text-gray-700">Annuler</a>
            <button type="submit" class="bg-gradient-to-r from-primary to-accent text-white px-6 py-2 rounded shadow hover:scale-105 transition">Enregistrer</button>
        </div>
    </form>
</div>
@push('styles')
<style>
    .user-option {
        @apply p-2 hover:bg-gray-50 cursor-pointer flex items-center justify-between;
    }
    .user-option:hover {
        @apply bg-gray-50;
    }
    .user-option.active {
        @apply bg-blue-50;
    }
    .user-option .badge {
        @apply text-xs px-2 py-0.5 rounded-full ml-2;
    }
    .badge-client {
        @apply bg-green-100 text-green-800;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusSelect = document.querySelector('select[name="status"]');
        const userSelection = document.getElementById('user-selection');
        const userSearch = document.getElementById('user-search');
        const searchResults = document.getElementById('search-results');
        const selectedUser = document.getElementById('selected-user');
        const selectedUserName = document.getElementById('selected-user-name');
        const userIdInput = document.getElementById('user_id');
        const clearSelection = document.getElementById('clear-selection');
        
        let searchTimeout;
        let isDropdownOpen = false;

        // Show/hide user selection based on status
        function toggleUserSelection() {
            if (statusSelect.value === 'Attribuee') {
                userSelection.classList.remove('hidden');
                if (!userIdInput.value) {
                    loadUsers('');
                }
            } else {
                userSelection.classList.add('hidden');
            }
        }

        // Load users from API
        function loadUsers(query = '') {
            searchResults.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Chargement...</div>';
            searchResults.classList.remove('hidden');
            
            fetch(`/api/users/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(users => {
                    if (users.length > 0) {
                        searchResults.innerHTML = users.map(user => `
                            <div class="p-2 cursor-pointer hover:bg-sky-50 user-option ${userIdInput.value == user.id ? 'active' : ''}" 
                                 data-id="${user.id}" 
                                 data-name="${user.prenom} ${user.nom}"
                                 data-email="${user.email}">
                                <div>
                                    ${user.prenom} ${user.nom}
                                    ${user.is_client ? '<span class="badge badge-client">Client existant</span>' : ''}
                                </div>
                                <div class="text-sm text-gray-500">${user.email}</div>
                            </div>
                        `).join('');
                    } else {
                        searchResults.innerHTML = '<div class="p-3 text-sm text-gray-500">Aucun utilisateur trouvé</div>';
                    }
                    isDropdownOpen = true;
                })
                .catch(() => {
                    searchResults.innerHTML = '<div class="p-3 text-sm text-red-500">Erreur de chargement</div>';
                });
        }

        // Handle status change
        statusSelect.addEventListener('change', toggleUserSelection);
        
        // Toggle dropdown on input focus
        userSearch.addEventListener('focus', function() {
            if (!isDropdownOpen) {
                loadUsers(userSearch.value);
            }
        });
        
        // Handle user search input
        userSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            searchTimeout = setTimeout(() => {
                loadUsers(query);
            }, 300);
        });

        // Handle click on search result
        searchResults.addEventListener('click', function(e) {
            const result = e.target.closest('.user-option');
            if (result) {
                const userId = result.dataset.id;
                const userName = result.dataset.name;
                const userEmail = result.dataset.email;
                
                userIdInput.value = userId;
                selectedUserName.innerHTML = `
                    ${userName}
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded ml-2">
                        ${userEmail}
                    </span>
                `;
                selectedUser.classList.remove('hidden');
                searchResults.classList.add('hidden');
                isDropdownOpen = false;
                
                // Mark the selected user in the dropdown
                document.querySelectorAll('.user-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                result.classList.add('active');
            }
        });

        // Handle clear selection
        clearSelection.addEventListener('click', function(e) {
            e.stopPropagation();
            userIdInput.value = '';
            selectedUser.classList.add('hidden');
            userSearch.value = '';
            userSearch.focus();
            loadUsers('');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userSearch.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
                isDropdownOpen = false;
            }
        });
        
        // Initialize on page load
        toggleUserSelection();
    });
</script>
@endpush

@endsection
