@extends('layouts.app')

@section('title', isset($user) ? 'Edit User' : 'Create User')
@section('page-title', isset($user) ? 'Edit User' : 'Create New User')
@section('page-subtitle', isset($user) ? 'Update user information' : 'Add a new user to the system')

@section('content')
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">
                        <i class="bx bx-home-alt"></i> Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.users.index') }}">
                        <i class="bx bx-user"></i> User Management
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="bx {{ isset($user) ? 'bx-edit' : 'bx-user-plus' }}"></i>
                    {{ isset($user) ? 'Edit User' : 'Create User' }}
                </li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">
                            <i class="bx {{ isset($user) ? 'bx-user-edit' : 'bx-user-plus' }} me-2"></i>
                            {{ isset($user) ? 'Edit User Information' : 'Create New User' }}
                        </h3>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i>
                            Back to Users
                        </a>
                    </div>

                    <form method="POST"
                        action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.store') }}"
                        id="userForm">
                        @csrf
                        @if (isset($user))
                            @method('PUT')
                        @endif

                        <div class="card-body">
                            <!-- Flash Messages -->
                            @if (session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="bx bx-check-circle me-2"></i>
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bx bx-error-circle me-2"></i>
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <i class="bx bx-error-alt me-2"></i>
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bx bx-error-circle me-2"></i>
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Roles Section - Radio Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <label class="form-label fw-semibold mb-3">
                                                <i class="bx bx-shield-alt me-2"></i>
                                                System Role <span class="text-danger">*</span>
                                            </label>
                                            @error('role')
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <i class="bx bx-error-circle me-2"></i>
                                                    {{ $message }}
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                                        aria-label="Close"></button>
                                                </div>
                                            @enderror

                                            <div class="row">
                                                @foreach ($roles as $roleName => $roleDisplay)
                                                    @php
                                                        $roleData = $rolesData->firstWhere('name', $roleName);
                                                        $stakeholder = $roleData->stakeholder ?? '';
                                                    @endphp
                                                    <div class="col-md-4 col-lg-3 mb-3">
                                                        <div class="form-check card-role {{ $roleName == 'Super Admin' && !auth()->user()->hasRole('Super Admin') ? 'disabled-role' : '' }}"
                                                            data-role="{{ $roleName }}"
                                                            data-stakeholder="{{ $stakeholder }}">
                                                            <input class="form-check-input" type="radio" name="role"
                                                                value="{{ $roleName }}" id="role_{{ $roleName }}"
                                                                {{ (isset($userRole) && in_array($roleName, $userRole)) || old('role') == $roleName ? 'checked' : '' }}
                                                                {{ $roleName == 'Super Admin' && !auth()->user()->hasRole('Super Admin') ? 'disabled' : '' }}>
                                                            <label class="form-check-label w-100"
                                                                for="role_{{ $roleName }}">
                                                                <div class="d-flex align-items-center">
                                                                    <i class="bx bx-shield-quarter me-2 text-primary"></i>
                                                                    <span class="fw-medium">{{ $roleDisplay }}</span>
                                                                </div>
                                                                @if ($stakeholder)
                                                                    <small class="text-info d-block mt-1">
                                                                        <i class="bx bx-building me-1"></i>
                                                                        Stakeholder: {{ ucfirst($stakeholder) }}
                                                                    </small>
                                                                @endif
                                                                <small class="text-muted d-block mt-1">
                                                                    @switch($roleName)
                                                                        @case('Super Admin')
                                                                            Full system access
                                                                        @break

                                                                        @case('State Admin')
                                                                            State level access
                                                                        @break

                                                                        @case('District Admin')
                                                                            District level access
                                                                        @break

                                                                        @case('Block Admin')
                                                                            Block level access
                                                                        @break

                                                                        @case('SI')
                                                                            Sub-Inspector level access
                                                                        @break

                                                                        @case('Circle')
                                                                            Circle level access
                                                                        @break

                                                                        @case('School Admin')
                                                                            School level access
                                                                        @break

                                                                        @case('HOI Primary')
                                                                            Head of Institution (Primary)
                                                                        @break

                                                                        @case('School')
                                                                            School level user
                                                                        @break
                                                                    @endswitch
                                                                </small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @if (!auth()->user()->hasRole('Super Admin'))
                                                <div class="alert alert-warning mt-3 mb-0">
                                                    <i class="bx bx-info-circle me-2"></i>
                                                    <strong>Note:</strong> You cannot assign or modify Super Admin roles.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Role Based Location Section -->
                            <div class="row mt-4" id="locationSection" style="display: none;">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <label class="form-label fw-semibold mb-3">
                                                <i class="bx bx-map me-2"></i>
                                                Location Assignment
                                            </label>
                                            <div class="row">
                                                <!-- District -->
                                                <div class="col-md-3 mb-3" id="districtBox" style="display: none;">
                                                    <label class="form-label">District <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="district_id" name="district_id">
                                                        <option value="">Select District</option>
                                                        @foreach ($districts ?? [] as $district)
                                                            <option value="{{ $district->encrypted_id }}"
                                                                data-district-schcd="{{ $district->schcd ?? '' }}"
                                                                {{ old('district_id') == $district->encrypted_id || (isset($user) && $user->district_id == $district->encrypted_id) ? 'selected' : '' }}>
                                                                {{ $district->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Circle -->
                                                <div class="col-md-3 mb-3" id="circleBox" style="display: none;">
                                                    <label class="form-label">Circle <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="circle_id" name="circle_id">
                                                        <option value="">Select Circle</option>
                                                        @if (isset($user) && $user->circle_id)
                                                            <option value="{{ $user->circle_id }}" selected>
                                                                {{ $user->circle->name ?? 'Selected Circle' }}</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <!-- SI -->
                                                <div class="col-md-3 mb-3" id="siBox" style="display: none;">
                                                    <label class="form-label">SI <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="si_id" name="si_id">
                                                        <option value="">Select SI</option>
                                                        @if (isset($user) && $user->si_id)
                                                            <option value="{{ $user->si_id }}" selected>
                                                                {{ $user->si->name ?? 'Selected SI' }}</option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <!-- Management -->
                                                <div class="col-md-3 mb-3" id="managementBox" style="display: none;">
                                                    <label class="form-label">Management</label>
                                                    <select class="form-select" id="management_id" name="management_id">
                                                        <option value="">Select Management</option>
                                                        @if (isset($user) && $user->management_id)
                                                            <option value="{{ $user->management_id }}" selected>
                                                                {{ $user->management->name ?? 'Selected Management' }}
                                                            </option>
                                                        @endif
                                                    </select>
                                                </div>

                                                <!-- School -->
                                                <div class="col-md-3 mb-3" id="schoolBox" style="display: none;">
                                                    <label class="form-label">School <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="school_id" name="school_id">
                                                        <option value="">Select School</option>
                                                        @if (isset($user) && $user->school_id)
                                                            <option value="{{ $user->school_id }}" selected>
                                                                {{ $user->school->name ?? 'Selected School' }}</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DISE Code Section -->
                            <div class="row mt-4" id="diseCodeSection" style="display: none;">
                                <div class="col-12">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="dise_code" class="form-label">
                                                            <i class="bx bx-id-card me-1"></i>
                                                            <span id="codeLabel">DISE Code / School Code</span>
                                                            <span class="text-danger">*</span>
                                                            <span class="text-muted small ms-2" id="diseHelpText"></span>
                                                        </label>
                                                        <input type="text"
                                                            class="form-control @error('dise_code') is-invalid @enderror"
                                                            id="dise_code" name="dise_code"
                                                            value="{{ old('dise_code', $user->dise_code ?? '') }}"
                                                            placeholder="Auto-populated based on selection" readonly>
                                                        @error('dise_code')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                        <div class="form-text">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            This field is auto-populated based on your selection above.
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Stakeholder Display -->
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            <i class="bx bx-group me-1"></i>
                                                            Stakeholder Type
                                                        </label>
                                                        <div class="form-control bg-light" id="stakeholderDisplay"
                                                            readonly>
                                                            <span class="text-muted" id="stakeholderText">Not
                                                                selected</span>
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="bx bx-info-circle me-1"></i>
                                                            Based on selected role
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rest of the form remains the same -->
                            <!-- Basic Information -->
                            <div class="row pt-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            <i class="bx bx-user me-1"></i>
                                            Full Name <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $user->name ?? '') }}"
                                            placeholder="Enter full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="bx bx-envelope me-1"></i>
                                            Email Address <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email', $user->email ?? '') }}"
                                            placeholder="Enter email address" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            <i class="bx bx-phone me-1"></i>
                                            Phone Number
                                        </label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                            id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}"
                                            placeholder="Enter phone number" maxlength="15">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            <i class="bx bx-user-circle me-1"></i>
                                            Username
                                        </label>
                                        <input type="text" class="form-control @error('username') is-invalid @enderror"
                                            id="username" name="username"
                                            value="{{ old('username', $user->username ?? '') }}"
                                            placeholder="Enter username">
                                        @error('username')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="department" class="form-label">
                                            <i class="bx bx-building me-1"></i>
                                            Department
                                        </label>
                                        <input type="text"
                                            class="form-control @error('department') is-invalid @enderror" id="department"
                                            name="department" value="{{ old('department', $user->department ?? '') }}"
                                            placeholder="Enter department">
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="designation" class="form-label">
                                            <i class="bx bx-briefcase me-1"></i>
                                            Designation
                                        </label>
                                        <input type="text"
                                            class="form-control @error('designation') is-invalid @enderror"
                                            id="designation" name="designation"
                                            value="{{ old('designation', $user->designation ?? '') }}"
                                            placeholder="Enter designation">
                                        @error('designation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 bg-light mb-4">
                                        <div class="card-header bg-transparent border-bottom">
                                            <h6 class="mb-0">
                                                <i class="bx bx-lock me-2"></i>
                                                Password Settings
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label">
                                                            {{ isset($user) ? 'New Password' : 'Password' }}
                                                            @if (!isset($user))
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="password"
                                                            class="form-control @error('password') is-invalid @enderror"
                                                            id="password" name="password"
                                                            placeholder="{{ isset($user) ? 'Leave blank to keep current password' : 'Enter strong password' }}"
                                                            {{ !isset($user) ? 'required' : '' }} minlength="8">
                                                        @error('password')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <!-- Password Strength Meter -->
                                                        <div class="password-strength mt-2">
                                                            <div class="progress" style="height: 5px;">
                                                                <div class="progress-bar" id="password-strength-bar"
                                                                    role="progressbar" style="width: 0%"></div>
                                                            </div>
                                                            <small class="form-text text-muted"
                                                                id="password-strength-text">
                                                                Password strength: Very Weak
                                                            </small>
                                                        </div>

                                                        <!-- Password Requirements -->
                                                        <div class="password-requirements mt-2">
                                                            <small class="form-text text-muted">
                                                                <strong>Password must contain:</strong>
                                                            </small>
                                                            <ul class="list-unstyled mt-1 mb-0 small">
                                                                <li id="req-length" class="text-muted">
                                                                    <i class="bx bx-x text-danger me-1"></i>
                                                                    At least 8 characters
                                                                </li>
                                                                <li id="req-uppercase" class="text-muted">
                                                                    <i class="bx bx-x text-danger me-1"></i>
                                                                    One uppercase letter
                                                                </li>
                                                                <li id="req-lowercase" class="text-muted">
                                                                    <i class="bx bx-x text-danger me-1"></i>
                                                                    One lowercase letter
                                                                </li>
                                                                <li id="req-number" class="text-muted">
                                                                    <i class="bx bx-x text-danger me-1"></i>
                                                                    One number
                                                                </li>
                                                                <li id="req-special" class="text-muted">
                                                                    <i class="bx bx-x text-danger me-1"></i>
                                                                    One special character
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="password_confirmation" class="form-label">
                                                            {{ isset($user) ? 'Confirm New Password' : 'Confirm Password' }}
                                                            @if (!isset($user))
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        <input type="password"
                                                            class="form-control @error('password_confirmation') is-invalid @enderror"
                                                            id="password_confirmation" name="confirm-password"
                                                            placeholder="{{ isset($user) ? 'Confirm new password' : 'Confirm password' }}"
                                                            {{ !isset($user) ? 'required' : '' }}>
                                                        @error('password_confirmation')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror

                                                        <!-- Password Match Indicator -->
                                                        <div class="password-match mt-2">
                                                            <small id="password-match-text" class="form-text text-muted">
                                                                <i class="bx bx-info-circle me-1"></i>
                                                                Passwords must match
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @if (isset($user))
                                                <div class="alert alert-info mt-2 mb-0">
                                                    <i class="bx bx-info-circle me-2"></i>
                                                    Leave password fields blank to keep the current password unchanged.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status"
                                                name="status" value="1"
                                                {{ old('status', isset($user) ? $user->status : true) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="status">
                                                <i class="bx bx-check-circle me-1"></i>
                                                Active User Account
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Rest of the form continues... -->

                        </div>

                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    @if (isset($user))
                                        <small class="text-muted">
                                            <i class="bx bx-calendar-edit me-1"></i>
                                            Last updated: {{ $user->updated_at->format('M j, Y g:i A') }}
                                        </small>
                                    @endif
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                                        <i class="bx bx-x me-2"></i>
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bx {{ isset($user) ? 'bx-save' : 'bx-user-plus' }} me-2"></i>
                                        {{ isset($user) ? 'Update User' : 'Create User' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card-role {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .card-role:hover:not(.disabled-role) {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }

        .card-role.selected {
            border-color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.05);
        }

        .card-role.disabled-role {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .card-role .form-check-input {
            cursor: pointer;
        }

        .card-role.disabled-role .form-check-input {
            cursor: not-allowed;
        }

        .password-requirements .valid {
            color: #28a745;
        }

        .password-requirements .valid i {
            color: #28a745;
        }

        .password-match .valid {
            color: #28a745;
        }

        .password-match .invalid {
            color: #dc3545;
        }
    </style>
@endpush

@push('scripts')

    <script>
        $(document).ready(function() {
            // Configuration object - Easy to modify for future roles
            const ROLE_CONFIG = {
                'Super Admin': {
                    locations: [],
                    diseCode: false,
                    stakeholder: 'system'
                },
                'State Admin': {
                    locations: [],
                    diseCode: false,
                    stakeholder: 'state'
                },
                'District Admin': {
                    locations: ['district'],
                    diseCode: true,
                    codeSource: 'district',
                    stakeholder: 'district'
                },
                'Block Admin': {
                    locations: ['district', 'circle'],
                    diseCode: true,
                    codeSource: 'circle',
                    stakeholder: 'block'
                },
                'SI': {
                    locations: ['district', 'circle'],
                    diseCode: true,
                    codeSource: 'circle',
                    stakeholder: 'circle'
                },
                'Circle': {
                    locations: ['district', 'circle'],
                    diseCode: true,
                    codeSource: 'circle',
                    stakeholder: 'circle'
                },
                'School Admin': {
                    locations: ['district', 'circle', 'school'],
                    diseCode: true,
                    codeSource: 'school',
                    stakeholder: 'school'
                },
                'HOI Primary': {
                    locations: ['district', 'circle', 'school'],
                    diseCode: true,
                    codeSource: 'school',
                    stakeholder: 'school'
                },
                'School': {
                    locations: ['district', 'circle', 'management', 'school'],
                    diseCode: true,
                    codeSource: 'school',
                    stakeholder: 'school'
                }
            };

            // DOM Elements
            const $locationSection = $('#locationSection');
            const $diseCodeSection = $('#diseCodeSection');
            const $districtBox = $('#districtBox');
            const $districtSelect = $('#district_id');
            const $circleBox = $('#circleBox');
            const $circleSelect = $('#circle_id');
            const $siBox = $('#siBox');
            const $siSelect = $('#si_id');
            const $managementBox = $('#managementBox');
            const $managementSelect = $('#management_id');
            const $schoolBox = $('#schoolBox');
            const $schoolSelect = $('#school_id');
            const $diseCodeInput = $('#dise_code');
            const $diseHelpText = $('#diseHelpText');
            const $codeLabel = $('#codeLabel');
            const $stakeholderDisplay = $('#stakeholderDisplay');
            const $stakeholderText = $('#stakeholderText');
            const $roleRadios = $('input[name="role"]');
            const $password = $('#password');
            const $confirmPassword = $('#password_confirmation');
            const $form = $('#userForm');
            const $submitBtn = $('#submitBtn');

            // Current state
            let currentRole = null;
            let currentStakeholder = '';
            let isLoading = false;

            // Initialize
            init();

            function init() {
                setupEventListeners();
                initializeRoleSelection();
                setupPasswordValidation();

                // Check if editing existing user
                const initialRole = $('input[name="role"]:checked').val();
                if (initialRole) {
                    handleRoleChange(initialRole);
                }
            }

            function setupEventListeners() {
                // Role selection
                $('.card-role:not(.disabled-role)').on('click', function() {
                    const $radio = $(this).find('input[type="radio"]');
                    if (!$radio.prop('disabled')) {
                        $radio.prop('checked', true);
                        handleRoleChange($radio.val());
                        updateRoleSelectionUI();
                    }
                });

                $roleRadios.on('change', function() {
                    handleRoleChange($(this).val());
                    updateRoleSelectionUI();
                });

                // District change
                $districtSelect.on('change', function() {
                    const districtId = $(this).val();
                    const role = getSelectedRole();
                    const config = ROLE_CONFIG[role];

                    $diseCodeInput.val(''); // Clear DISE code on district change

                    if (!config) return;
                    if (districtId && config.codeSource === 'district') {
                        // For District Admin - get district code immediately
                        populateDiseCode($districtSelect.find('option:selected').data('district-schcd'));
                    }

                    if (districtId && config.locations.includes('circle')) {

                        loadCircles(districtId);
                        resetDownstreamFields(['si', 'management', 'school']);
                    }

                    else if (!districtId) {
                        resetDownstreamFields(['circle', 'si', 'management', 'school']);
                    }
                });

                // Circle change
                $circleSelect.on('change', function() {
                    const circleId = $(this).val();
                    const role = getSelectedRole();
                    const config = ROLE_CONFIG[role];

                    if (!config) return;



                    if (circleId) {

                        if (role === 'SI') {
                            populateDiseCode($circleSelect.find('option:selected').data('dise'));
                            // loadSIs(circleId);
                        } else if (role === 'School') {
                            loadManagements(circleId);
                        } else if (config.locations.includes('school')) {
                            loadSchools(circleId);
                        }
                    } else {
                        resetDownstreamFields(['si', 'management', 'school']);
                    }
                });

                // School change
                $schoolSelect.on('change', function() {
                    const schoolId = $(this).val();
                    const role = getSelectedRole();
                    const config = ROLE_CONFIG[role];

                    if (!config) return;

                    if (schoolId && config.codeSource === 'school') {
                        // For School Admin, HOI Primary, School roles
                        populateDiseCode(schoolId, 'school');
                    }
                });

                // Form submission
                $form.on('submit', function(e) {
                    if (!validateForm()) {
                        e.preventDefault();
                        return false;
                    }

                    $submitBtn.prop('disabled', true).html(
                        '<i class="bx bx-loader bx-spin me-2"></i> Processing...'
                    );
                });
            }

            function handleRoleChange(role) {
                currentRole = role;
                const config = ROLE_CONFIG[role];

                // Get stakeholder from card data attribute
                const $selectedCard = $(`.card-role[data-role="${role}"]`);
                currentStakeholder = $selectedCard.data('stakeholder') || config?.stakeholder || '';

                if (!config) {
                    hideAllLocationFields();
                    hideStakeholder();
                    return;
                }

                // Reset all fields
                resetAllLocationFields();
                $diseCodeInput.val('');

                // Show/hide location section
                if (config.locations.length > 0) {
                    $locationSection.show();

                    // Show required fields based on role
                    config.locations.forEach(field => {
                        $(`#${field}Box`).show();
                    });

                    // Set field requirements
                    setFieldRequirements(config.locations);
                } else {
                    $locationSection.hide();
                }

                // Show/hide DISE code section
                if (config.diseCode) {
                    $diseCodeSection.show();
                    updateDiseHelpText(config.codeSource);
                    updateCodeLabel(config.codeSource);
                } else {
                    $diseCodeSection.hide();
                }

                // Update stakeholder display
                updateStakeholderDisplay();

                // Initialize existing data if editing
                if ($('#userForm').data('edit-mode') === true) {
                    initializeExistingData();
                }
            }

            function updateDiseHelpText(codeSource) {
                const texts = {
                    'district': 'District Code will be auto-populated',
                    'circle': 'Circle Code will be auto-populated',
                    'si': 'SI Code will be auto-populated',
                    'school': 'School DISE Code will be auto-populated'
                };
                $diseHelpText.text(texts[codeSource] || '');
            }

            function updateCodeLabel(codeSource) {
                const labels = {
                    'district': 'District Code',
                    'circle': 'Circle Code',
                    'si': 'SI Code',
                    'school': 'DISE Code'
                };
                $codeLabel.text(labels[codeSource] || 'DISE Code / School Code');
            }

            function updateStakeholderDisplay() {
                if (currentStakeholder) {
                    $stakeholderText.text(ucfirst(currentStakeholder)).removeClass('text-muted').addClass(
                        'text-primary');
                    $stakeholderDisplay.removeClass('bg-light').addClass('bg-light-blue');
                } else {
                    $stakeholderText.text('Not selected').addClass('text-muted').removeClass('text-primary');
                    $stakeholderDisplay.removeClass('bg-light-blue').addClass('bg-light');
                }
            }

            function ucfirst(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            }

            function hideStakeholder() {
                $stakeholderText.text('Not selected').addClass('text-muted').removeClass('text-primary');
                $stakeholderDisplay.removeClass('bg-light-blue').addClass('bg-light');
            }

            // AJAX Functions
            function loadCircles(districtId) {
                if (isLoading) return;

                isLoading = true;
                showLoading($circleSelect, 'Loading circles...');

                $.ajax({
                    url: '/get-circles',
                    method: 'GET',
                    data: {
                        district_id: districtId
                    },
                    success: function(data) {
                        populateSelect($circleSelect, data, 'Select Circle');
                        $circleBox.show();

                        // Auto-select if editing
                        if ($circleSelect.data('initial-value')) {
                            $circleSelect.val($circleSelect.data('initial-value')).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading circles:', error);
                        showError('Failed to load circles. Please try again.');
                        resetDownstreamFields(['circle', 'si', 'management', 'school']);
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading($circleSelect);
                    }
                });
            }


            function loadSchools(circleId) {
                if (isLoading) return;
                $diseCodeInput.val(''); // Clear DISE code on district change
                isLoading = true;
                showLoading($schoolSelect, 'Loading schools...');

                $.ajax({
                    url: '/get-schools',
                    method: 'GET',
                    data: {
                        district_id: $districtSelect.val(),
                        circle_id: circleId
                    },
                    success: function(data) {
                        populateSelectWithDise($schoolSelect, data, 'Select School');
                        $schoolBox.show();

                        if ($schoolSelect.data('initial-value')) {
                            $schoolSelect.val($schoolSelect.data('initial-value')).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading schools:', error);
                        showError('Failed to load schools. Please try again.');
                        resetDownstreamFields(['school']);
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading($schoolSelect);
                    }
                });
            }

            function loadManagements(circleId) {
                if (isLoading) return;

                isLoading = true;
                showLoading($managementSelect, 'Loading managements...');

                $.ajax({
                    url: '/api/circles/' + circleId + '/managements',
                    method: 'GET',
                    success: function(data) {
                        populateSelect($managementSelect, data, 'Select Management');
                        $managementBox.show();

                        if ($managementSelect.data('initial-value')) {
                            $managementSelect.val($managementSelect.data('initial-value')).trigger(
                                'change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading managements:', error);
                        showError('Failed to load managements. Please try again.');
                        resetDownstreamFields(['management', 'school']);
                    },
                    complete: function() {
                        isLoading = false;
                        hideLoading($managementSelect);
                    }
                });
            }

            function populateDiseCode(code) {
                if (code) {
                    $diseCodeInput.val(code);
                }
            }

            // Helper Functions
            function populateSelect($select, data, placeholder) {
                let html = `<option value="">${placeholder}</option>`;

                data.forEach(item => {
                    html += `<option value="${item.encrypted_id || item.id}" data-dise="${item.code || item.dise_code || ''}">${item.name}</option>`;
                });

                $select.html(html);
            }

            function populateSelectWithDise($select, data, placeholder) {
                let html = `<option value="">${placeholder}</option>`;

                data.forEach(item => {
                    html += `<option value="${item.encrypted_id || item.id}"
                              data-dise="${item.code || item.dise_code || ''}">
                              ${item.name || item.school_name}
                              </option>`;
                });

                $select.html(html);

                // Bind DISE code update on change
                $select.off('change.dise').on('change.dise', function() {
                    const selectedOption = $(this).find('option:selected');
                    const diseCode = selectedOption.data('dise');
                    $diseCodeInput.val(diseCode || '');
                });
            }

            function resetAllLocationFields() {
                const fields = ['district', 'circle', 'si', 'management', 'school'];
                fields.forEach(field => {
                    $(`#${field}Box`).hide();
                    $(`#${field}_id`).val('').prop('required', false);
                });
            }

            function resetDownstreamFields(fields) {
                fields.forEach(field => {
                    $(`#${field}Box`).hide();
                    $(`#${field}_id`).val('').prop('required', false);
                });
                $diseCodeInput.val('');
            }

            function setFieldRequirements(locations) {
                locations.forEach(field => {
                    $(`#${field}_id`).prop('required', true);
                });
            }

            function showLoading($element, text) {
                $element.prop('disabled', true)
                    .html(`<option value="">${text}...</option>`);
            }

            function hideLoading($element) {
                $element.prop('disabled', false);
            }

            function showError(message) {
                alert(message);
            }

            function getSelectedRole() {
                return $('input[name="role"]:checked').val();
            }

            function updateRoleSelectionUI() {
                $('.card-role').removeClass('selected');
                $('input[name="role"]:checked').closest('.card-role').addClass('selected');
            }

            function initializeRoleSelection() {
                $roleRadios.each(function() {
                    if ($(this).is(':checked')) {
                        $(this).closest('.card-role').addClass('selected');
                    }
                });
            }

            function initializeExistingData() {
                // Store initial values for dropdowns
                const initialValues = {
                    district: $districtSelect.val(),
                    circle: $circleSelect.val(),
                    si: $siSelect.val(),
                    management: $managementSelect.val(),
                    school: $schoolSelect.val()
                };

                // Set data attributes
                Object.keys(initialValues).forEach(field => {
                    if (initialValues[field]) {
                        $(`#${field}_id`).data('initial-value', initialValues[field]);
                    }
                });

                // Trigger changes based on existing data
                if (initialValues.district && currentRole && ROLE_CONFIG[currentRole].locations.includes(
                        'district')) {
                    $districtSelect.trigger('change');
                }
            }

            // Form Validation
            function validateForm() {
                const role = getSelectedRole();
                const config = ROLE_CONFIG[role];

                if (!role) {
                    alert('Please select a role for the user.');
                    return false;
                }

                // Validate location fields based on role
                if (config.locations.length > 0) {
                    for (const field of config.locations) {
                        const $field = $(`#${field}_id`);
                        if (!$field.val()) {
                            alert(`Please select a ${field} for this role.`);
                            $field.focus();
                            return false;
                        }
                    }
                }

                // Validate DISE code for roles that require it
                if (config.diseCode && !$diseCodeInput.val()) {
                    alert(`${$codeLabel.text()} is required for this role.`);
                    return false;
                }

                // Validate password
                const isEditMode = {{ isset($user) ? 'true' : 'false' }};
                const passwordValue = $password.val();

                if (!isEditMode && (!passwordValue || passwordValue.length < 8)) {
                    alert('Please enter a strong password with at least 8 characters');
                    $password.focus();
                    return false;
                }

                if (passwordValue && passwordValue.length > 0) {
                    if (!validatePasswordStrength(passwordValue)) {
                        alert('Please choose a stronger password.');
                        $password.focus();
                        return false;
                    }

                    if (passwordValue !== $confirmPassword.val()) {
                        alert('Passwords do not match. Please confirm your password.');
                        $confirmPassword.focus();
                        return false;
                    }
                }

                return true;
            }

            // Password validation setup
            function setupPasswordValidation() {
                $password.on('input', updatePasswordStrength);
                $confirmPassword.on('input', checkPasswordMatch);
            }

            function validatePasswordStrength(password) {
                const {
                    strength
                } = checkPasswordStrength(password);
                return strength >= 60;
            }

            function checkPasswordStrength(password) {
                let strength = 0;

                if (password.length >= 8) strength += 20;
                if (/[A-Z]/.test(password)) strength += 20;
                if (/[a-z]/.test(password)) strength += 20;
                if (/[0-9]/.test(password)) strength += 20;
                if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 20;

                return {
                    strength
                };
            }

            function updatePasswordStrength() {
                const passwordValue = $password.val();
                const {
                    strength
                } = checkPasswordStrength(passwordValue);

                const $bar = $('#password-strength-bar');
                const $text = $('#password-strength-text');

                $bar.css('width', strength + '%');

                if (strength <= 20) {
                    $bar.removeClass().addClass('progress-bar bg-danger');
                    $text.text('Password strength: Very Weak');
                } else if (strength <= 40) {
                    $bar.removeClass().addClass('progress-bar bg-warning');
                    $text.text('Password strength: Weak');
                } else if (strength <= 60) {
                    $bar.removeClass().addClass('progress-bar bg-info');
                    $text.text('Password strength: Fair');
                } else if (strength <= 80) {
                    $bar.removeClass().addClass('progress-bar bg-primary');
                    $text.text('Password strength: Good');
                } else {
                    $bar.removeClass().addClass('progress-bar bg-success');
                    $text.text('Password strength: Excellent');
                }
            }

            function checkPasswordMatch() {
                const passwordValue = $password.val();
                const confirmValue = $confirmPassword.val();
                const $text = $('#password-match-text');

                if (!confirmValue) {
                    $text.html('<i class="bx bx-info-circle me-1"></i> Passwords must match')
                        .removeClass().addClass('form-text text-muted');
                    return false;
                }

                if (passwordValue === confirmValue) {
                    $text.html('<i class="bx bx-check-circle me-1"></i> Passwords match')
                        .removeClass().addClass('form-text valid');
                    return true;
                } else {
                    $text.html('<i class="bx bx-x-circle me-1"></i> Passwords do not match')
                        .removeClass().addClass('form-text invalid');
                    return false;
                }
            }
        });
    </script>
@endpush
