@extends('layouts.app')

@section('title', 'User Management')
@section('header', 'User Management')
@section('head')
@vite(['resources/css/pages/users.css', 'resources/js/pages/users.js'])
@endsection
@php
    $hasCreateUserErrors = $errors->has('name')
        || $errors->has('email')
        || $errors->has('username')
        || $errors->has('password')
        || $errors->has('role')
        || $errors->has('protected_area_id');
@endphp

@section('content')
    <div
        id="users-page-config"
        data-export-pdf="{{ route('users.export.pdf') }}"
        data-export-excel="{{ route('users.export.excel') }}"
        data-export-print="{{ route('users.export.print') }}"
        class="hidden"
        aria-hidden="true"
    ></div>

    @if (session('success'))
        <div id="users-success-toast" class="users-toast users-toast--success" role="status" aria-live="polite">
            <div class="flex items-center">
                <i data-lucide="check-circle" class="lucide-icon w-5 h-5 mr-2 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="users-error-toast" class="users-toast users-toast--error" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="lucide-icon w-5 h-5 mr-2 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($hasCreateUserErrors)
        <div id="users-validation-toast" class="users-toast users-toast--error" role="alert" aria-live="assertive">
            <div class="flex items-center">
                <i data-lucide="alert-circle" class="lucide-icon w-5 h-5 mr-2 flex-shrink-0"></i>
                <span>Please fix the highlighted user form errors.</span>
            </div>
        </div>
    @endif

    <div class="filter-panel">
        <form method="GET" action="{{ route('users.index') }}" class="overflow-x-auto">
            <div class="flex items-end gap-3 flex-nowrap min-w-[980px]">
                <div class="filter-panel__field flex-1 min-w-[180px]">
                    <label for="role" class="filter-panel__label">Role</label>
                    <select name="role" id="role" class="filter-panel__select">
                        <option value="">All</option>
                        <option value="admin" @selected(request('role') === 'admin')>Administrator</option>
                        <option value="pa_user" @selected(request('role') === 'pa_user')>Protected Area User</option>
                    </select>
                </div>
                <div class="filter-panel__field flex-1 min-w-[220px]">
                    <label for="protected_area_id" class="filter-panel__label">Protected Area</label>
                    <select name="protected_area_id" id="protected_area_id" class="filter-panel__select">
                        <option value="">All</option>
                        @foreach($protectedAreas as $protectedArea)
                            <option value="{{ $protectedArea->id }}" @selected((string) request('protected_area_id') === (string) $protectedArea->id)>
                                {{ $protectedArea->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-panel__field flex-1 min-w-[170px]">
                    <label for="status" class="filter-panel__label">Status</label>
                    <select name="status" id="status" class="filter-panel__select">
                        <option value="">All</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="filter-panel__field flex-1 min-w-[170px]">
                    <label for="sort_by" class="filter-panel__label">Sort By</label>
                    <select name="sort_by" id="sort_by" class="filter-panel__select">
                        <option value="name" @selected(request('sort_by', 'name') === 'name')>Name</option>
                        <option value="email" @selected(request('sort_by') === 'email')>Email</option>
                        <option value="username" @selected(request('sort_by') === 'username')>Username</option>
                        <option value="role" @selected(request('sort_by') === 'role')>Role</option>
                        <option value="is_active" @selected(request('sort_by') === 'is_active')>Status</option>
                    </select>
                </div>
                <input type="hidden" name="sort_direction" value="{{ request('sort_direction', 'asc') }}">
                <div class="filter-panel__actions shrink-0 pb-[1px]">
                    <button type="submit" class="btn-filter-apply">Apply</button>
                    <a href="{{ route('users.index') }}" class="btn-filter-clear">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <div class="action-bar-card">
        <div class="action-bar-card__header">
            <h2 class="action-bar-card__title">System Users ({{ $users->total() }} records)</h2>
            <div class="action-bar">
                <form method="GET" action="{{ route('users.index') }}" class="action-bar__search-wrap">
                    @foreach(request()->except(['search', 'page']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <div class="action-bar__search action-bar__search--with-submit">
                        <span class="action-bar__search-icon" aria-hidden="true">
                            <i data-lucide="search" class="lucide-icon"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" class="action-bar__search-input" placeholder="Search name, email, username...">
                    </div>
                    <button type="submit" class="action-bar__search-submit-btn">
                        <i data-lucide="search" class="lucide-icon"></i>
                        <span>Search</span>
                    </button>
                </form>
                <div class="action-bar__actions">
                    <div class="action-bar__export-wrap">
                        <button type="button" id="users-export-dropdown-btn" class="action-bar__export-btn">
                            <i data-lucide="download" class="lucide-icon"></i>
                            <span>Export</span>
                            <i data-lucide="chevron-down" class="lucide-icon"></i>
                        </button>
                        <div id="users-export-dropdown" class="action-bar__export-dropdown">
                            <button type="button" onclick="exportUsers('pdf')">
                                <i data-lucide="file-text" class="lucide-icon"></i>
                                <span>Export as PDF</span>
                            </button>
                            <button type="button" onclick="exportUsers('excel')">
                                <i data-lucide="file-spreadsheet" class="lucide-icon"></i>
                                <span>Export as Excel</span>
                            </button>
                            <button type="button" onclick="exportUsers('print')">
                                <i data-lucide="printer" class="lucide-icon"></i>
                                <span>Print</span>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="action-bar__add-btn" onclick="showCreateUserModal()">
                        <i data-lucide="plus" class="lucide-icon"></i>
                        <span>Add User</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Protected Area</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}<br><span class="text-xs text-gray-500">{{ '@'.$user->username }}</span></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role === 'admin' ? 'Administrator' : 'Protected Area User' }}</td>
                            <td>{{ $user->protectedArea?->name ?? '—' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="data-table-status-badge data-table-status-badge--active">Active</span>
                                @else
                                    <span class="data-table-status-badge data-table-status-badge--no-data">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="species-observations-actions">
                                    <button
                                        type="button"
                                        class="species-observation-action-btn view"
                                        title="View User"
                                        aria-label="View User"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-username="{{ $user->username }}"
                                        data-user-role="{{ $user->role }}"
                                        data-user-protected-area="{{ $user->protectedArea?->name ?? '—' }}"
                                        data-user-status="{{ $user->is_active ? 'Active' : 'Inactive' }}"
                                        onclick="openUserViewModal(this)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M21 17v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2"/><path d="M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"/><circle cx="12" cy="12" r="1"/><path d="M18.944 12.33a1 1 0 0 0 0-.66 7.5 7.5 0 0 0-13.888 0 1 1 0 0 0 0 .66 7.5 7.5 0 0 0 13.888 0"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="species-observation-action-btn edit"
                                        title="Edit User"
                                        aria-label="Edit User"
                                        data-user-id="{{ $user->id }}"
                                        data-user-name="{{ $user->name }}"
                                        data-user-email="{{ $user->email }}"
                                        data-user-username="{{ $user->username }}"
                                        data-user-role="{{ $user->role }}"
                                        data-user-protected-area-id="{{ $user->protected_area_id ?? '' }}"
                                        data-user-is-active="{{ $user->is_active ? '1' : '0' }}"
                                        data-update-url="{{ route('users.update', $user) }}"
                                        onclick="openUserEditModal(this)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="species-observation-action-btn delete"
                                        title="Delete User"
                                        aria-label="Delete User"
                                        data-user-name="{{ $user->name }}"
                                        data-delete-url="{{ route('users.destroy', $user) }}"
                                        onclick="openUserDeleteModal(this)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="species-observation-action-icon" aria-hidden="true"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="data-table-empty-cell">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-table-pagination">
            <div class="data-table-pagination__info">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
            </div>
            @if($users->hasPages())
                <nav class="data-table-pagination__nav">
                    @if($users->onFirstPage())
                        <button type="button" disabled>&lsaquo; Previous</button>
                    @else
                        <a href="{{ $users->previousPageUrl() }}">&lsaquo; Previous</a>
                    @endif
                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}">Next &rsaquo;</a>
                    @else
                        <button type="button" disabled>Next &rsaquo;</button>
                    @endif
                </nav>
            @endif
        </div>
    </div>

    <div id="create-user-modal" class="user-modal fixed inset-0 z-[9999] hidden" data-has-errors="{{ $hasCreateUserErrors ? '1' : '0' }}">
        <div class="user-modal-backdrop absolute inset-0 transition-opacity duration-300" onclick="hideCreateUserModal()"></div>
        <div class="user-modal-dialog relative flex items-center justify-center min-h-screen p-4">
            <div class="user-modal-content relative w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="create-user-modal-content" style="z-index: 10000;">
                <div class="user-modal-header">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Create User Account</h3>
                        <p class="text-sm text-gray-500 mt-1">Fill in all required user details below.</p>
                    </div>
                    <button type="button" class="user-modal-icon-btn" onclick="hideCreateUserModal()" aria-label="Close">
                        <i data-lucide="x" class="lucide-icon w-5 h-5"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('users.store') }}" class="user-modal-body grid grid-cols-1 gap-4 p-6">
                    @csrf
                    @if ($hasCreateUserErrors)
                        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            <p class="font-semibold mb-1">Unable to save user account:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="user-modal-panel grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div>
                            <label for="create-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input id="create-name" type="text" name="name" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Enter full name" value="{{ old('name') }}" required>
                        </div>
                        <div>
                            <label for="create-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input id="create-email" type="email" name="email" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Enter email address" value="{{ old('email') }}" required>
                        </div>
                        <div>
                            <label for="create-username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input id="create-username" type="text" name="username" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Enter username" value="{{ old('username') }}" required>
                        </div>
                        <div>
                            <label for="create-password" class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                            <input id="create-password" type="password" name="password" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Minimum 8 characters" required>
                        </div>
                        <div>
                            <label for="create-role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="create-role" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="admin" @selected(old('role') === 'admin')>Administrator</option>
                                <option value="pa_user" @selected(old('role') === 'pa_user')>Protected Area User</option>
                            </select>
                        </div>
                        <div id="create-protected-area-field">
                            <label for="create-protected-area" class="block text-sm font-medium text-gray-700 mb-1">Protected Area</label>
                            <select name="protected_area_id" id="create-protected-area" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 disabled:text-gray-500" @disabled(old('role') !== 'pa_user')>
                                <option value="">Select Protected Area</option>
                                @foreach($protectedAreas as $protectedArea)
                                    <option value="{{ $protectedArea->id }}" @selected((string) old('protected_area_id') === (string) $protectedArea->id)>{{ $protectedArea->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="create-is-active" class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input id="create-is-active" type="checkbox" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            Set account as active
                        </label>
                    </div>
                    <div class="user-modal-footer">
                        <button type="button" class="user-modal-btn user-modal-btn--secondary" onclick="hideCreateUserModal()">Cancel</button>
                        <button type="submit" class="user-modal-btn user-modal-btn--primary">
                            <i data-lucide="plus" class="lucide-icon"></i>
                            <span>Create User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="view-user-modal" class="user-modal fixed inset-0 z-[9999] hidden">
        <div class="user-modal-backdrop absolute inset-0 transition-opacity duration-300" onclick="hideUserViewModal()"></div>
        <div class="user-modal-dialog relative flex items-center justify-center min-h-screen p-4">
            <div class="user-modal-content relative w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="view-user-modal-content" style="z-index: 10000;">
                <div class="user-modal-header">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">View User Account</h3>
                        <p class="text-sm text-gray-500 mt-1">User account details overview.</p>
                    </div>
                    <button type="button" class="user-modal-icon-btn" onclick="hideUserViewModal()" aria-label="Close">
                        <i data-lucide="x" class="lucide-icon w-5 h-5"></i>
                    </button>
                </div>
                <div class="user-modal-body user-modal-panel grid grid-cols-1 gap-4 p-6 rounded-lg border border-gray-200 bg-gray-50 m-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Full Name</p>
                        <p id="view-user-name" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email Address</p>
                        <p id="view-user-email" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Username</p>
                        <p id="view-user-username" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</p>
                        <p id="view-user-role" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Protected Area</p>
                        <p id="view-user-protected-area" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</p>
                        <p id="view-user-status" class="text-sm text-gray-900 mt-1">—</p>
                    </div>
                    <div class="user-modal-footer user-modal-footer--embedded">
                        <button type="button" class="user-modal-btn user-modal-btn--secondary" onclick="hideUserViewModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-user-modal" class="user-modal fixed inset-0 z-[9999] hidden">
        <div class="user-modal-backdrop absolute inset-0 transition-opacity duration-300" onclick="hideUserEditModal()"></div>
        <div class="user-modal-dialog relative flex items-center justify-center min-h-screen p-4">
            <div class="user-modal-content relative w-full max-w-2xl transform transition-all duration-300 scale-95 opacity-0" id="edit-user-modal-content" style="z-index: 10000;">
                <div class="user-modal-header">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Edit User Account</h3>
                        <p class="text-sm text-gray-500 mt-1">Update user details and access assignment.</p>
                    </div>
                    <button type="button" class="user-modal-icon-btn" onclick="hideUserEditModal()" aria-label="Close">
                        <i data-lucide="x" class="lucide-icon w-5 h-5"></i>
                    </button>
                </div>
                <form id="edit-user-form" method="POST" action="{{ route('users.index') }}" class="user-modal-body grid grid-cols-1 gap-4 p-6">
                    @csrf
                    @method('PUT')
                    <div class="user-modal-panel grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                        <div>
                            <label for="edit-name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input id="edit-name" type="text" name="name" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" required>
                        </div>
                        <div>
                            <label for="edit-email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input id="edit-email" type="email" name="email" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" required>
                        </div>
                        <div>
                            <label for="edit-username" class="block text-sm font-medium text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                            <input id="edit-username" type="text" name="username" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" required>
                        </div>
                        <div>
                            <label for="edit-password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input id="edit-password" type="password" name="password" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200" placeholder="Leave blank to keep current password">
                        </div>
                        <div>
                            <label for="edit-role" class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" id="edit-role" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-200">
                                <option value="admin">Administrator</option>
                                <option value="pa_user">Protected Area User</option>
                            </select>
                        </div>
                        <div id="edit-protected-area-field">
                            <label for="edit-protected-area" class="block text-sm font-medium text-gray-700 mb-1">Protected Area</label>
                            <select name="protected_area_id" id="edit-protected-area" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-green-500 focus:ring-2 focus:ring-green-200 disabled:bg-gray-100 disabled:text-gray-500">
                                <option value="">Select Protected Area</option>
                                @foreach($protectedAreas as $protectedArea)
                                    <option value="{{ $protectedArea->id }}">{{ $protectedArea->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="edit-is-active" class="inline-flex items-center gap-2 text-sm text-gray-700">
                            <input id="edit-is-active" type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                            Set account as active
                        </label>
                    </div>
                    <div class="user-modal-footer">
                        <button type="button" class="user-modal-btn user-modal-btn--secondary" onclick="hideUserEditModal()">Cancel</button>
                        <button type="submit" class="user-modal-btn user-modal-btn--primary">
                            <i data-lucide="save" class="lucide-icon"></i>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="delete-user-modal" class="user-modal fixed inset-0 z-[9999] hidden">
        <div class="user-modal-backdrop absolute inset-0 transition-opacity duration-300" onclick="hideUserDeleteModal()"></div>
        <div class="user-modal-dialog relative flex items-center justify-center min-h-screen p-4">
            <div class="user-modal-content relative w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0" id="delete-user-modal-content" style="z-index: 10000;">
                <div class="user-modal-header">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete User Account</h3>
                        <p class="text-sm text-gray-500 mt-1">This action cannot be undone.</p>
                    </div>
                    <button type="button" class="user-modal-icon-btn" onclick="hideUserDeleteModal()" aria-label="Close">
                        <i data-lucide="x" class="lucide-icon w-5 h-5"></i>
                    </button>
                </div>
                <form id="delete-user-form" method="POST" action="{{ route('users.index') }}" class="user-modal-body p-6">
                    @csrf
                    @method('DELETE')
                    <div class="user-modal-danger-panel rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm text-red-700">
                            Are you sure you want to delete
                            <span id="delete-user-name" class="font-semibold"></span>?
                        </p>
                    </div>
                    <div class="user-modal-footer">
                        <button type="button" class="user-modal-btn user-modal-btn--secondary" onclick="hideUserDeleteModal()">Cancel</button>
                        <button type="submit" class="user-modal-btn user-modal-btn--danger">Delete User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
