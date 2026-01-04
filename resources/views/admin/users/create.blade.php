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
                                                    <div class="col-md-4 col-lg-3 mb-3">
                                                        <div class="form-check card-role">
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
                                                                @if ($roleName == 'Super Admin')
                                                                    <small class="text-muted d-block mt-1">Full system
                                                                        access</small>
                                                                @elseif($roleName == 'State Admin')
                                                                    <small class="text-muted d-block mt-1">State level
                                                                        access</small>
                                                                @elseif($roleName == 'District Admin')
                                                                    <small class="text-muted d-block mt-1">District level
                                                                        access</small>
                                                                @elseif($roleName == 'Block Admin')
                                                                    <small class="text-muted d-block mt-1">Block level
                                                                        access</small>
                                                                @elseif($roleName == 'School Admin')
                                                                    <small class="text-muted d-block mt-1">School level
                                                                        access</small>
                                                                @elseif($roleName == 'Circle')
                                                                    <small class="text-muted d-block mt-1">Circle level
                                                                        access</small>
                                                                @elseif($roleName == 'Hoi Primary')
                                                                    <small class="text-muted d-block mt-1">Head of
                                                                        Institution (Primary)</small>
                                                                @elseif($roleName == 'School')
                                                                    <small class="text-muted d-block mt-1">School level
                                                                        user</small>
                                                                @endif
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
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label">District <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="district_id" name="district_id">
                                                        <option value="">Select District</option>
                                                        @foreach ($districts ?? [] as $district)
                                                            <option value="{{ $district->encrypted_id }}"
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
                                        <label for="dise_code" class="form-label">
                                            <i class="bx bx-id-card me-1"></i>
                                            DISE Code
                                        </label>
                                        <input type="text"
                                            class="form-control @error('dise_code') is-invalid @enderror" id="dise_code"
                                            name="dise_code" value="{{ old('dise_code', $user->dise_code ?? '') }}"
                                            placeholder="Enter 2-11 digit DISE code" minlength="2" maxlength="11"
                                            pattern="[0-9]+"
                                            title="DISE code must be 2-11 digits and contain only numbers">
                                        @error('dise_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="bx bx-info-circle me-1"></i>
                                            2-11 digit DISE code (numbers only)
                                            @if (isset($user) && $user->dise_code)
                                                <span class="d-block text-success small mt-1">
                                                    <i class="bx bx-check-circle"></i> Current: {{ $user->dise_code }}
                                                </span>
                                            @endif
                                        </div>
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

                            @if (isset($user))
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="status"
                                                    name="status" value="1"
                                                    {{ old('status', $user->status ?? true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-medium" for="status">
                                                    <i class="bx bx-check-circle me-1"></i>
                                                    Active User Account
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="status"
                                                    name="status" value="1" checked>
                                                <label class="form-check-label fw-medium" for="status">
                                                    <i class="bx bx-check-circle me-1"></i>
                                                    Active User Account
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
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



@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const locationSection = document.getElementById('locationSection');
            const districtSelect = document.getElementById('district_id');
            const circleBox = document.getElementById('circleBox');
            const circleSelect = document.getElementById('circle_id');
            const managementBox = document.getElementById('managementBox');
            const managementSelect = document.getElementById('management_id');
            const schoolBox = document.getElementById('schoolBox');
            const schoolSelect = document.getElementById('school_id');
            const roleRadios = document.querySelectorAll('input[name="role"]');

            // Password elements
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('password_confirmation');
            const passwordStrengthBar = document.getElementById('password-strength-bar');
            const passwordStrengthText = document.getElementById('password-strength-text');
            const passwordMatchText = document.getElementById('password-match-text');
            const submitBtn = document.getElementById('submitBtn');
            const form = document.getElementById('userForm');

            // Password strength requirements
            const requirements = {
                length: document.getElementById('req-length'),
                uppercase: document.getElementById('req-uppercase'),
                lowercase: document.getElementById('req-lowercase'),
                number: document.getElementById('req-number'),
                special: document.getElementById('req-special')
            };

            // Role Configuration
            const roleConfig = {
                'Super Admin': {
                    locations: []
                },
                'State Admin': {
                    locations: []
                },
                'District Admin': {
                    locations: ['district']
                },
                'Block Admin': {
                    locations: ['district', 'circle']
                },
                'SI': {
                    locations: ['district', 'circle']
                },
                'School Admin': {
                    locations: ['district', 'circle', 'school']
                },
                'HOI Primary': {
                    locations: ['district', 'circle', 'school']
                },
                'School': {
                    locations: ['district', 'circle', 'management', 'school']
                }
            };

            // Role change handler
            function handleRoleChange(role) {
                console.log('Selected role:', role);

                // Reset all location fields
                resetLocationFields();

                // Hide location section by default
                locationSection.style.display = 'none';

                // Get configuration for selected role
                const config = roleConfig[role];
                console.log('config', config);
                if (!config) return;

                // Show/hide location section
                if (config.locations.length > 0) {
                    locationSection.style.display = 'block';

                    // Show required fields based on role
                    config.locations.forEach(field => {
                        switch (field) {
                            case 'district':
                                // District is always shown when location section is visible
                                break;
                            case 'si':
                                circleBox.style.display = 'block';
                                circleSelect.required = true;
                                break;
                            case 'management':
                                managementBox.style.display = 'block';
                                managementSelect.required = true;
                                break;
                            case 'school':
                                schoolBox.style.display = 'block';
                                schoolSelect.required = true;
                                break;
                        }
                    });

                    // Initialize AJAX chains based on existing selections
                    initializeLocationChains();
                }
            }

            // Reset location fields
            function resetLocationFields() {
                circleBox.style.display = 'none';
                managementBox.style.display = 'none';
                schoolBox.style.display = 'none';

                circleSelect.innerHTML = '<option value="">Select Circle</option>';
                managementSelect.innerHTML = '<option value="">Select Management</option>';
                schoolSelect.innerHTML = '<option value="">Select School</option>';

                circleSelect.required = false;
                managementSelect.required = false;
                schoolSelect.required = false;
            }

            // Initialize location chains
            function initializeLocationChains() {
                // If district is already selected, load circles
                if (districtSelect.value) {
                    loadCircles(districtSelect.value);
                }

                // If circle is already selected, load schools/managements
                if (circleSelect.value) {
                    const selectedRole = getSelectedRole();
                    if (selectedRole === 'School') {
                        loadManagements(circleSelect.value);
                    } else {
                        loadSchools(circleSelect.value);
                    }
                }

                // If management is already selected, load schools
                if (managementSelect.value) {
                    loadSchoolsByManagement(managementSelect.value);
                }
            }

            // Get selected role
            function getSelectedRole() {
                const selectedRadio = document.querySelector('input[name="role"]:checked');
                return selectedRadio ? selectedRadio.value : null;
            }

            // Load circles based on district
            function loadCircles(districtId) {
                if (!districtId) {
                    circleSelect.innerHTML = '<option value="">Select Circle</option>';
                    managementSelect.innerHTML = '<option value="">Select Management</option>';
                    schoolSelect.innerHTML = '<option value="">Select School</option>';
                    circleBox.style.display = 'none';
                    managementBox.style.display = 'none';
                    schoolBox.style.display = 'none';
                    return;
                }

                fetch(`/get-circles?district_id=${districtId}`)
                    .then(response => response.json())
                    .then(data => {
                        circleSelect.innerHTML = '<option value="">Select Circle</option>';
                        data.forEach(circle => {
                            circleSelect.innerHTML +=
                                `<option value="${circle.encrypted_id}">${circle.name}</option>`;
                        });

                        // Show circle box if it should be visible for current role
                        const selectedRole = getSelectedRole();
                        if (selectedRole && roleConfig[selectedRole]?.locations.includes('circle')) {
                            circleBox.style.display = 'block';
                        }

                        document.getElementById('dise_code').value = '';
                    })
                    .catch(error => console.error('Error loading circles:', error));
            }

            // Load managements based on circle
            function loadManagements(circleId) {
                if (!circleId) {
                    managementSelect.innerHTML = '<option value="">Select Management</option>';
                    schoolSelect.innerHTML = '<option value="">Select School</option>';
                    managementBox.style.display = 'none';
                    schoolBox.style.display = 'none';
                    return;
                }

                fetch(`/api/circles/${circleId}/managements`)
                    .then(response => response.json())
                    .then(data => {
                        managementSelect.innerHTML = '<option value="">Select Management</option>';
                        data.forEach(management => {
                            managementSelect.innerHTML +=
                                `<option value="${management.id}">${management.name}</option>`;
                        });
                        managementBox.style.display = 'block';
                    })
                    .catch(error => console.error('Error loading managements:', error));
            }

            // Load schools based on circle
            function loadSchools(circleId) {
                document.getElementById('dise_code').value = '';
                const district_id = districtSelect.value;

                if (!district_id) {
                    alert('Please select a district first.');
                    return;
                }

                if (!circleId) {
                    schoolSelect.innerHTML = '<option value="">Select School</option>';
                    schoolBox.style.display = 'none';
                    return;
                }

                // Using the API endpoint with query parameters
                fetch(`/get-schools?district_id=${district_id}&circle_id=${circleId}`)
                    .then(response => response.json())
                    .then(data => {
                        schoolSelect.innerHTML = '<option value="">Select School</option>';
                        data.forEach(school => {
                            // Store DISE code in data attribute
                            schoolSelect.innerHTML +=
                                `<option value="${school.encrypted_id}"
                                    data-dise="${school.code}">
                                    ${school.name || school.school_name}
                                    </option>`;
                        });
                        schoolBox.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error loading schools:', error);
                        alert('Failed to load schools. Please try again.');
                    });
            }
            // Load schools based on management
            function loadSchoolsByManagement(managementId) {
                if (!managementId) {
                    schoolSelect.innerHTML = '<option value="">Select School</option>';
                    schoolBox.style.display = 'none';
                    return;
                }

                fetch(`/api/managements/${managementId}/schools`)
                    .then(response => response.json())
                    .then(data => {
                        schoolSelect.innerHTML = '<option value="">Select School</option>';
                        data.forEach(school => {
                            schoolSelect.innerHTML +=
                                `<option value="${school.id}">${school.name}</option>`;
                        });
                        schoolBox.style.display = 'block';
                    })
                    .catch(error => console.error('Error loading schools by management:', error));
            }

            // Event Listeners

            // Add event listener for school selection change
            schoolSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const diseCode = selectedOption.getAttribute('data-dise');

                // Get the dise_code input element (make sure it exists)
                const diseCodeInput = document.getElementById('dise_code');

                if (diseCodeInput && diseCode) {
                    diseCodeInput.value = diseCode;
                } else if (diseCodeInput) {
                    diseCodeInput.value = ''; // Clear if no DISE code
                }
            });
            roleRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    handleRoleChange(this.value);
                    updateRoleSelectionUI();
                });
            });

            districtSelect.addEventListener('change', function() {
                loadCircles(this.value);
            });

            circleSelect.addEventListener('change', function() {
                const selectedRole = getSelectedRole();
                if (selectedRole === 'School') {
                    loadManagements(this.value);
                } else {
                    loadSchools(this.value);
                }
            });

            managementSelect.addEventListener('change', function() {
                loadSchoolsByManagement(this.value);
            });

            // Password validation functions
            function checkPasswordStrength(password) {
                let strength = 0;
                const requirementsMet = {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false,
                    special: false
                };

                if (password.length >= 8) {
                    strength += 20;
                    requirementsMet.length = true;
                }

                if (/[A-Z]/.test(password)) {
                    strength += 20;
                    requirementsMet.uppercase = true;
                }

                if (/[a-z]/.test(password)) {
                    strength += 20;
                    requirementsMet.lowercase = true;
                }

                if (/[0-9]/.test(password)) {
                    strength += 20;
                    requirementsMet.number = true;
                }

                if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                    strength += 20;
                    requirementsMet.special = true;
                }

                return {
                    strength,
                    requirementsMet
                };
            }

            function updatePasswordStrength() {
                const passwordValue = password.value;

                if (!passwordValue) {
                    passwordStrengthBar.style.width = '0%';
                    passwordStrengthBar.className = 'progress-bar';
                    passwordStrengthText.textContent = 'Password strength: Very Weak';
                    resetRequirements();
                    return;
                }

                const {
                    strength,
                    requirementsMet
                } = checkPasswordStrength(passwordValue);
                passwordStrengthBar.style.width = strength + '%';

                if (strength <= 20) {
                    passwordStrengthBar.className = 'progress-bar bg-danger';
                    passwordStrengthText.textContent = 'Password strength: Very Weak';
                } else if (strength <= 40) {
                    passwordStrengthBar.className = 'progress-bar bg-warning';
                    passwordStrengthText.textContent = 'Password strength: Weak';
                } else if (strength <= 60) {
                    passwordStrengthBar.className = 'progress-bar bg-info';
                    passwordStrengthText.textContent = 'Password strength: Fair';
                } else if (strength <= 80) {
                    passwordStrengthBar.className = 'progress-bar bg-primary';
                    passwordStrengthText.textContent = 'Password strength: Good';
                } else {
                    passwordStrengthBar.className = 'progress-bar bg-success';
                    passwordStrengthText.textContent = 'Password strength: Excellent';
                }

                updateRequirementsUI(requirementsMet);
            }

            function updateRequirementsUI(requirementsMet) {
                Object.keys(requirementsMet).forEach(key => {
                    const requirement = requirements[key];
                    const icon = requirement.querySelector('i');

                    if (requirementsMet[key]) {
                        requirement.classList.add('valid');
                        icon.className = 'bx bx-check text-success me-1';
                    } else {
                        requirement.classList.remove('valid');
                        icon.className = 'bx bx-x text-danger me-1';
                    }
                });
            }

            function resetRequirements() {
                Object.values(requirements).forEach(requirement => {
                    requirement.classList.remove('valid');
                    const icon = requirement.querySelector('i');
                    icon.className = 'bx bx-x text-danger me-1';
                });
            }

            function checkPasswordMatch() {
                const passwordValue = password.value;
                const confirmValue = confirmPassword.value;

                if (!confirmValue) {
                    passwordMatchText.innerHTML = '<i class="bx bx-info-circle me-1"></i> Passwords must match';
                    passwordMatchText.className = 'form-text text-muted';
                    return false;
                }

                if (passwordValue === confirmValue) {
                    passwordMatchText.innerHTML = '<i class="bx bx-check-circle me-1"></i> Passwords match';
                    passwordMatchText.className = 'form-text valid';
                    return true;
                } else {
                    passwordMatchText.innerHTML = '<i class="bx bx-x-circle me-1"></i> Passwords do not match';
                    passwordMatchText.className = 'form-text invalid';
                    return false;
                }
            }

            function validateDiseCode() {
                const diseCode = document.getElementById('dise_code').value.trim();

                if (!diseCode) {
                    return true;
                }

                if (!/^\d+$/.test(diseCode)) {
                    alert('DISE code must contain only numbers (0-9)');
                    return false;
                }

                if (diseCode.length < 2 || diseCode.length > 11) {
                    alert('DISE code must be between 2 and 11 digits');
                    return false;
                }

                return true;
            }

            function validateForm() {
                const selectedRole = getSelectedRole();
                const passwordValue = password.value;
                const isEditMode = {{ isset($user) ? 'true' : 'false' }};

                // Role validation
                if (!selectedRole) {
                    alert('Please select a role for the user.');
                    return false;
                }

                // Location validation based on role
                const config = roleConfig[selectedRole];
                if (config.locations.includes('district') && !districtSelect.value) {
                    alert('Please select a district for this role.');
                    districtSelect.focus();
                    return false;
                }

                if (config.locations.includes('circle') && !circleSelect.value) {
                    alert('Please select a circle for this role.');
                    circleSelect.focus();
                    return false;
                }

                if (config.locations.includes('management') && !managementSelect.value) {
                    alert('Please select a management for this role.');
                    managementSelect.focus();
                    return false;
                }

                if (config.locations.includes('school') && !schoolSelect.value) {
                    alert('Please select a school for this role.');
                    schoolSelect.focus();
                    return false;
                }

                // Password validation
                if (!isEditMode && (!passwordValue || passwordValue.length < 8)) {
                    alert('Please enter a strong password with at least 8 characters');
                    password.focus();
                    return false;
                }

                if (passwordValue && passwordValue.length > 0) {
                    const {
                        strength
                    } = checkPasswordStrength(passwordValue);
                    if (strength < 60) {
                        alert(
                            'Please choose a stronger password. Password should include uppercase, lowercase, numbers, and special characters.'
                        );
                        password.focus();
                        return false;
                    }

                    if (!checkPasswordMatch()) {
                        alert('Passwords do not match. Please confirm your password.');
                        confirmPassword.focus();
                        return false;
                    }
                }

                // DISE code validation
                if (!validateDiseCode()) {
                    return false;
                }

                return true;
            }

            function initializeRoleSelection() {
                roleRadios.forEach(radio => {
                    const card = radio.closest('.card-role');
                    card.addEventListener('click', function(e) {
                        if (!radio.disabled) {
                            radio.checked = true;
                            handleRoleChange(radio.value);
                            updateRoleSelectionUI();
                        }
                    });

                    if (radio.checked) {
                        handleRoleChange(radio.value);
                        card.classList.add('selected');
                    }
                });
            }

            function updateRoleSelectionUI() {
                document.querySelectorAll('.card-role').forEach(card => {
                    card.classList.remove('selected');
                });

                const selectedRadio = document.querySelector('input[name="role"]:checked');
                if (selectedRadio) {
                    const selectedCard = selectedRadio.closest('.card-role');
                    selectedCard.classList.add('selected');
                }
            }

            // Initialize event listeners
            password.addEventListener('input', function() {
                updatePasswordStrength();
                checkPasswordMatch();
            });

            confirmPassword.addEventListener('input', checkPasswordMatch);

            document.getElementById('dise_code').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            form.addEventListener('submit', function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="bx bx-loader bx-spin me-2"></i> Processing...';
            });

            // Initialize
            initializeRoleSelection();
            if (password.value) {
                updatePasswordStrength();
                checkPasswordMatch();
            }
        });
    </script>
@endpush
