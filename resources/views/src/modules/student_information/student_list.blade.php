@extends('layouts.app')

@section('title', 'Student List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #0d6efd 0%, #0d6efd 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--card-shadow);
            border-radius: 20px;
            transition: var(--transition);
        }

        .glass-card:hover {
            box-shadow: var(--card-hover-shadow);
            transform: translateY(-5px);
        }

        .gradient-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px 25px;
            margin: -1rem -1rem 1.5rem -1rem;
        }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 15px;
        }

        .stats-icon.total {
            background: var(--primary-gradient);
        }

        .stats-icon.male {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
        }

        .stats-icon.female {
            background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%);
        }

        .stats-icon.bpl {
            background: var(--warning-gradient);
        }

        .stats-icon.cwsn {
            background: var(--danger-gradient);
        }

        .stats-icon.class {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .stats-number {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stats-label {
            font-size: 14px;
            color: #718096;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .search-container {
            position: relative;
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
        }

        .search-icon {
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 20px;
            z-index: 10;
        }

        .search-input {
            padding-left: 50px !important;
            border-radius: 12px !important;
            border: 2px solid #e2e8f0 !important;
            height: 52px;
            font-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            color: #667eea;
            padding: 8px 15px;
            border-radius: 50px;
            margin: 5px;
            font-size: 14px;
            font-weight: 500;
        }

        .filter-badge i {
            margin-right: 8px;
            font-size: 12px;
        }

        .filter-badge .close-filter {
            margin-left: 10px;
            cursor: pointer;
            opacity: 0.7;
            transition: var(--transition);
        }

        .filter-badge .close-filter:hover {
            opacity: 1;
            transform: scale(1.2);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-title {
            font-size: 18px;
            font-weight: 700;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-title i {
            color: #667eea;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            margin: 0 3px;
            transition: var(--transition);
            border: none;
        }

        .action-btn.view {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .action-btn.edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .action-btn.profile {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .student-name {
            font-weight: 600;
            color: #2d3748;
            transition: var(--transition);
        }

        .student-name:hover {
            color: #667eea;
        }

        .student-code {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            padding: 4px 12px;
            border-radius: 20px;
            font-family: monospace;
            font-size: 12px;
            color: #667eea;
            font-weight: 500;
        }

        .gender-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .gender-badge.male {
            background: linear-gradient(135deg, #36d1dc20 0%, #5b86e520 100%);
            color: #5b86e5;
        }

        .gender-badge.female {
            background: linear-gradient(135deg, #ff9a9e20 0%, #fad0c420 100%);
            color: #ff6b6b;
        }

        .class-badge {
            background: linear-gradient(135deg, #a8edea20 0%, #fed6e320 100%);
            color: #ff6b9d;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .pagination-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-top: 20px;
            box-shadow: var(--card-shadow);
        }

        .pagination .page-link {
            border: none;
            border-radius: 10px !important;
            margin: 0 5px;
            color: #4a5568;
            font-weight: 500;
            min-width: 40px;
            text-align: center;
            transition: var(--transition);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .pagination .page-link:hover {
            background: #667eea20;
            color: #667eea;
            transform: translateY(-2px);
        }

        .no-data {
            text-align: center;
            padding: 50px 20px;
        }

        .no-data-icon {
            font-size: 60px;
            color: #e2e8f0;
            margin-bottom: 20px;
        }

        .no-data h5 {
            color: #718096;
            font-weight: 500;
        }

        .export-dropdown .dropdown-menu {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 10px;
            min-width: 180px;
        }

        .export-dropdown .dropdown-item {
            padding: 10px 15px;
            border-radius: 10px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .export-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            transform: translateX(5px);
        }

        .record-count {
            background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            color: #667eea;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .record-count i {
            font-size: 12px;
        }

        .per-page-select {
            border-radius: 12px !important;
            border: 2px solid #e2e8f0 !important;
            height: 42px;
            font-size: 14px;
            transition: var(--transition);
            min-width: 100px;
        }

        .per-page-select:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
        }

        .filter-card {
            margin-bottom: 20px;
        }

        .filter-card .card-body {
            padding: 25px;
        }

        .filter-group {
            margin-bottom: 15px;
        }

        .filter-label {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-indicator.active {
            background: #38ef7d;
        }

        .status-indicator.inactive {
            background: #f56565;
        }

        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 15px;
            }

            .search-input {
                height: 48px;
            }

            .table-responsive {
                border-radius: 15px;
            }


            .filter-card .card-body {
                padding: 15px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid full-width-content py-4">

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-2 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['total_students']) }}</div>
                    <div class="stats-label">Total Students</div>
                </div>
            </div>

            <div class="col-md-2 col-sm-6">
                <div class="stats-card" style="background: linear-gradient(135deg, #ff996620 0%, #ff5e6220 100%);">
                    <div class="stats-icon" style="background: linear-gradient(135deg, #ff9966 0%, #ff5e62 100%);">
                        <i class="fas fa-user-slash"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['deactivated_students'] ?? 0) }}</div>
                    <div class="stats-label">Deactivated</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon male">
                        <i class="fas fa-male"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['male_students']) }}</div>
                    <div class="stats-label">Male</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon female">
                        <i class="fas fa-female"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['female_students']) }}</div>
                    <div class="stats-label">Female</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon bpl">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['bpl_students']) }}</div>
                    <div class="stats-label">BPL Students</div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon cwsn">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <div class="stats-number">{{ number_format($data['cwsn_students']) }}</div>
                    <div class="stats-label">CWSN Students</div>
                </div>
            </div>

        </div>

        <!-- Advanced Filters Card -->
        <div class="glass-card mb-4">
            <div class="gradient-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 fw-bold" style="font-size: 22px; font-weight: 700; color: white;">
                            <i class="fas fa-filter me-2"></i> Advanced Student Filters
                        </p>

                        @if ($user_school)
                            <small class="text-white-50">
                                <i class="fas fa-school me-1"></i> Viewing students from:
                                <strong>{{ $user_school->school_name }}</strong> ({{ $user_school->schcd }})
                                @if (!$selected_school_id)
                                    <span class="badge bg-warning ms-2">Default School</span>
                                @endif
                            </small>
                        @endif
                    </div>
                    <i class="fas fa-sliders-h fa-2x opacity-50"></i>
                </div>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route('students.list') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- District -->
                        <!-- District -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-map-marked-alt me-2"></i>District
                            </label>
                            <select class="form-select form-select-lg" name="district_id" id="districtSelect"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;"
                                {{ $user_role_info['is_district_officer'] || $user_role_info['is_si_officer'] || $user_role_info['is_school_user'] ? 'disabled' : '' }}>
                                <option value="">All Districts</option>
                                @foreach ($data['districts'] as $district)
                                    <option value="{{ Crypt::encrypt($district->id) }}"
                                        {{ $selected_district_id == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                           @if ($user_role_info['is_district_officer'])
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Restricted to your assigned district
                                </small>
                            @elseif ($user_role_info['is_si_officer'])
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Restricted to your assigned circle
                                </small>
                            @endif
                        </div>

                        <!-- Circle -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-circle me-2"></i>Circle
                            </label>
                            <select class="form-select form-select-lg" name="circle_id" id="circleSelect"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;"
                                {{ $user_role_info['is_si_officer'] || $user_role_info['is_school_user'] ? 'disabled' : (!$selected_district_id ? 'disabled' : '') }}>
                                <option value="">All Circles</option>
                                @if ($selected_district_id && isset($data['circles']))
                                    @foreach ($data['circles'] as $circle)
                                        <option value="{{ Crypt::encrypt($circle->id) }}"
                                            {{ $selected_circle_id == $circle->id ? 'selected' : '' }}>
                                            {{ $circle->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($user_role_info['is_school_user'])
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Restricted to your school's circle
                                </small>
                            @endif
                        </div>

                        <!-- Management -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-university me-2"></i>Management
                            </label>
                            <select class="form-select form-select-lg" name="management_id"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;"
                                {{ $user_role_info['is_school_user'] ? 'disabled' : (!$selected_district_id ? 'disabled' : '') }}>
                                <option value="">All Management</option>
                                @foreach ($data['managements'] as $management)
                                    <option value="{{ Crypt::encrypt($management->id) }}"
                                        {{ $selected_management_id == $management->id ? 'selected' : '' }}>
                                        {{ $management->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if ($user_role_info['is_school_user'])
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Restricted to your school's management
                                </small>
                            @endif
                        </div>

                        <!-- School -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-school me-2"></i>School
                            </label>
                            <select class="form-select form-select-lg" name="school_id" id="schoolSelect"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;"
                                 {{ $user_role_info['is_school_user'] ? 'disabled' : (!$selected_district_id ? 'disabled' : '') }}>
                                <option value="">All Schools</option>
                                @if ($selected_district_id && $data['schools']->count() > 0)
                                    @foreach ($data['schools'] as $school)
                                        <option value="{{ Crypt::encrypt($school->id) }}"
                                            {{ $selected_school_id == $school->id ? 'selected' : '' }}>
                                            {{ $school->school_name }} ({{ $school->schcd }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @if ($user_role_info['is_school_user'])
                                <small class="text-muted mt-1 d-block">
                                    <i class="fas fa-info-circle"></i> Restricted to your school only
                                </small>
                            @endif
                        </div>

                        <!-- Gender -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-venus-mars me-2"></i>Gender
                            </label>
                            <select class="form-select form-select-lg" name="gender"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All Gender</option>
                                @foreach ($data['genders'] ?? [] as $gender)
                                    <option value="{{ $gender->id }}"
                                        {{ $gender_param == $gender->id ? 'selected' : '' }}>
                                        {{ $gender->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Class -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-graduation-cap me-2"></i>Class
                            </label>
                            <select class="form-select form-select-lg" name="class"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All Classes</option>
                                @foreach ($data['classes'] ?? [] as $class)
                                    <option value="{{ $class->id }}"
                                        {{ $class_param == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Social Category -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-layer-group me-2"></i> School Category
                            </label>
                            <select class="form-select form-select-lg" name="category"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All Categories</option>
                                @foreach ($data['categories'] as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category_param == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Academic Year -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>Academic Year
                            </label>
                            <select class="form-select form-select-lg" name="academic_year"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All Years</option>
                                @foreach ($data['academic_years'] as $year)
                                    <option value="{{ $year }}"
                                        {{ $academic_year_param == $year ? 'selected' : '' }}>
                                        {{ $year }}-{{ $year + 1 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BPL Status -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-heart me-2"></i>BPL Status
                            </label>
                            <select class="form-select form-select-lg" name="bpl"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All</option>
                                <option value="1" {{ $bpl_param == '1' ? 'selected' : '' }}>BPL</option>
                                <option value="0" {{ $bpl_param == '0' ? 'selected' : '' }}>Non-BPL</option>
                            </select>
                        </div>

                        <!-- CWSN Status -->
                        <div class="col-md-2">
                            <label class="form-label fw-semibold text-muted mb-2">
                                <i class="fas fa-wheelchair me-2"></i>CWSN Status
                            </label>
                            <select class="form-select form-select-lg" name="cwsn"
                                style="border-radius: 12px; height: 52px; border: 2px solid #e2e8f0;">
                                <option value="">All</option>
                                <option value="1" {{ $cwsn_param == '1' ? 'selected' : '' }}>CWSN</option>
                                <option value="2" {{ $cwsn_param == '2' ? 'selected' : '' }}>Non-CWSN</option>
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-4 d-flex align-items-end gap-3">
                            <button type="submit" class="btn btn-lg flex-grow-1"
                                style="
                            background: var(--primary-gradient);
                            color: white;
                            border: none;
                            border-radius: 12px;
                            height: 52px;
                            font-weight: 600;
                            transition: var(--transition);
                        ">
                                <i class="fas fa-filter me-2"></i> Apply Filters
                            </button>
                            <a href="{{ route('students.list') }}" class="btn btn-lg"
                                style="
                            background: linear-gradient(135deg, #f5656520 0%, #ed893620 100%);
                            color: #f56565;
                            border: 2px solid #f56565;
                            border-radius: 12px;
                            height: 52px;
                            font-weight: 600;
                            transition: var(--transition);
                            min-width: 120px;
                        ">
                                <i class="fas fa-times me-2"></i> Clear All
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Active Filters -->
                @if (
                    $selected_district_id ||
                        $selected_management_id ||
                        $selected_school_id ||
                        $gender_param ||
                        $class_param ||
                        $category_param ||
                        $bpl_param !== '' ||
                        $cwsn_param !== '' ||
                        $academic_year_param ||
                        $search_param)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-semibold text-muted mb-3"><i class="fas fa-filter me-2"></i>Active Filters</h6>
                        <div class="d-flex flex-wrap">
                            @if ($selected_district_id)
                                <div class="filter-badge">
                                    <i class="fas fa-map-marker-alt"></i>
                                    District:
                                    {{ $data['districts']->where('id', $selected_district_id)->first()->name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('district_id'), ['district_id' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif





                            @if ($selected_management_id)
                                <div class="filter-badge">
                                    <i class="fas fa-university"></i>
                                    Management:
                                    {{ $data['managements']->where('id', $selected_management_id)->first()->name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('management_id'), ['management_id' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($selected_school_id)
                                <div class="filter-badge">
                                    <i class="fas fa-school"></i>
                                    School:
                                    {{ $data['schools']->where('id', $selected_school_id)->first()->school_name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('school_id'), ['school_id' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($gender_param)
                                <div class="filter-badge">
                                    <i class="fas fa-venus-mars"></i>
                                    Gender: {{ $data['genders']->where('id', $gender_param)->first()->name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('gender'), ['gender' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($class_param)
                                <div class="filter-badge">
                                    <i class="fas fa-graduation-cap"></i>
                                    Class: {{ $data['classes']->where('id', $class_param)->first()->name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('class'), ['class' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($category_param)
                                <div class="filter-badge">
                                    <i class="fas fa-layer-group"></i>
                                    Category:
                                    {{ $data['categories']->where('id', $category_param)->first()->name ?? 'N/A' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('category'), ['category' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($academic_year_param)
                                <div class="filter-badge">
                                    <i class="fas fa-calendar-alt"></i>
                                    Year: {{ $academic_year_param }}-{{ $academic_year_param + 1 }}
                                    <a href="{{ route('students.list', array_merge(request()->except('academic_year'), ['academic_year' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($bpl_param !== '')
                                <div class="filter-badge">
                                    <i class="fas fa-heart"></i>
                                    BPL: {{ $bpl_param == '1' ? 'Yes' : 'No' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('bpl'), ['bpl' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($cwsn_param !== '')
                                <div class="filter-badge">
                                    <i class="fas fa-wheelchair"></i>
                                    CWSN: {{ $cwsn_param == '2' ? 'Yes' : 'No' }}
                                    <a href="{{ route('students.list', array_merge(request()->except('cwsn'), ['cwsn' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if ($search_param)
                                <div class="filter-badge">
                                    <i class="fas fa-search"></i>
                                    Search: "{{ $search_param }}"
                                    <a href="{{ route('students.list', array_merge(request()->except('search'), ['search' => ''])) }}"
                                        class="close-filter">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            @endif

                            @if (
                                $selected_district_id ||
                                    $selected_management_id ||
                                    $selected_school_id ||
                                    $gender_param ||
                                    $class_param ||
                                    $category_param ||
                                    $bpl_param !== '' ||
                                    $cwsn_param !== '' ||
                                    $academic_year_param ||
                                    $search_param)
                                <div class="filter-badge"
                                    style="background: linear-gradient(135deg, #f5656520 0%, #ed893620 100%); color: #f56565;">
                                    <a href="{{ route('students.list') }}" class="text-decoration-none"
                                        style="color: inherit;">
                                        <i class="fas fa-times-circle"></i> Clear All Filters
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Search Box -->
        <div class="search-container">
            <form method="GET" action="{{ route('students.list') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="position-relative">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" name="search" class="form-control form-control-lg search-input"
                                placeholder="Search by student name, admission number, Aadhaar, father name, school..."
                                value="{{ $search_param }}">
                            <!-- Hidden fields to preserve filters -->
                            @if ($selected_district_id)
                                <input type="hidden" name="district_id"
                                    value="{{ $encrypted_params['district_id'] ?? '' }}">
                            @endif

                            @if ($selected_management_id)
                                <input type="hidden" name="management_id"
                                    value="{{ $encrypted_params['management_id'] ?? '' }}">
                            @endif
                            @if ($selected_school_id)
                                <input type="hidden" name="school_id"
                                    value="{{ $encrypted_params['school_id'] ?? '' }}">
                            @endif
                            @if ($gender_param)
                                <input type="hidden" name="gender" value="{{ $gender_param }}">
                            @endif
                            @if ($class_param)
                                <input type="hidden" name="class" value="{{ $class_param }}">
                            @endif
                            @if ($category_param)
                                <input type="hidden" name="category" value="{{ $category_param }}">
                            @endif
                            @if ($academic_year_param)
                                <input type="hidden" name="academic_year" value="{{ $academic_year_param }}">
                            @endif
                            @if ($bpl_param !== '')
                                <input type="hidden" name="bpl" value="{{ $bpl_param }}">
                            @endif
                            @if ($cwsn_param !== '')
                                <input type="hidden" name="cwsn" value="{{ $cwsn_param }}">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-3">
                        <button type="submit" class="btn btn-lg flex-grow-1"
                            style="
                        background: var(--success-gradient);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 600;
                    ">
                            <i class="fas fa-search me-2"></i> Search
                        </button>

                        <div class="input-group" style="width: 180px;">
                            <span class="input-group-text bg-transparent border-end-0"
                                style="border-radius: 12px 0 0 12px; border: 2px solid #e2e8f0;">
                                <i class="fas fa-list-ol"></i>
                            </span>
                            <select class="form-select per-page-select" id="perPageSelect" name="per_page"
                                style="border-radius: 0 12px 12px 0; border-left: 0;">
                                <option value="10" {{ $per_page == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="20" {{ $per_page == 20 ? 'selected' : '' }}>20 per page</option>
                                <option value="50" {{ $per_page == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ $per_page == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Students Table Card -->
        <div class="table-container">
            <div class="table-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="table-title">
                            <i class="fas fa-user-graduate"></i> Student Directory
                            <span class="record-count ms-3">
                                <i class="fas fa-database"></i>
                                {{ number_format($data['students']->total()) }} Total Students
                            </span>
                        </h5>
                    </div>
                    <div class="btn-group export-dropdown">
                        <button type="button" class="btn btn-lg"
                            style="
                        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                        color: white;
                        border: none;
                        border-radius: 12px;
                        font-weight: 600;
                        padding: 10px 20px;
                    "
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-2"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item export-print" href="#">
                                    <i class="fas fa-print text-primary"></i> Print
                                </a></li>
                            <li><a class="dropdown-item export-csv" href="#">
                                    <i class="fas fa-file-csv text-info"></i> CSV
                                </a></li>
                            <li><a class="dropdown-item export-excel" href="#">
                                    <i class="fas fa-file-excel text-success"></i> Excel
                                </a></li>
                            <li><a class="dropdown-item export-pdf" href="#">
                                    <i class="fas fa-file-pdf text-danger"></i> PDF
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if ($data['students']->count() > 0)
                    <div class="table-responsive">
                        <table id="studentTable" class="table table-hover align-middle">
                            <thead>
                                <tr style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                    <th class="text-center" style="border-radius: 10px 0 0 10px;">#</th>
                                    <th>Student Details</th>
                                    <th>Academic Info</th>
                                    <th>School Details</th>
                                    <th>Contact Info</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center no-export" style="border-radius: 0 10px 10px 0;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['students'] as $key => $student)
                                    <tr style="border-bottom: 1px solid #f1f1f1;">
                                        <td class="text-center fw-bold" style="color: #667eea;">
                                            {{ ($data['students']->currentPage() - 1) * $data['students']->perPage() + $loop->iteration }}
                                        </td>
                                        <td>
                                            <div class="student-name">{{ $student->studentname ?? 'N/A' }}</div>
                                            <div class="mt-1">
                                                <span class="student-code">{{ $student->student_code ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mt-1">
                                                <span
                                                    class="gender-badge {{ $student->gender_code_fk == 1 ? 'male' : 'female' }}">
                                                    <i
                                                        class="fas {{ $student->gender_code_fk == 1 ? 'fa-male' : 'fa-female' }} me-1"></i>
                                                    {{ $student->gender->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                {{-- <i class="fas fa-id-card me-1"></i> {{ $student->admission_no ?? 'N/A' }} --}}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="class-badge">
                                                <i class="fas fa-graduation-cap me-1"></i>
                                                {{ $student->currentClass->name ?? 'N/A' }}
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Year: {{ $student->academic_year ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-layer-group me-1"></i>
                                                    Category: {{ $student->category->name ?? 'N/A' }}
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $student->school->school_name ?? 'N/A' }}</div>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-code me-1"></i> {{ $student->school->schcd ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                {{ $student->district->name ?? 'N/A' }},
                                                {{ $student->circle->name ?? 'N/A' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if ($student->fathername)
                                                <small class="d-block">
                                                    <i class="fas fa-user-friends me-1"></i> {{ $student->fathername }}
                                                </small>
                                            @endif
                                            @if ($student->mothername)
                                                <small class="d-block mt-1">
                                                    <i class="fas fa-user-friends me-1"></i> {{ $student->mothername }}
                                                </small>
                                            @endif
                                            @if ($student->stu_mobile_no)
                                                <small class="d-block mt-1">
                                                    <i class="fas fa-phone me-1"></i> {{ $student->stu_mobile_no }}
                                                </small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill"
                                                style="
                                    background: linear-gradient(135deg, #38ef7d20 0%, #11998e20 100%);
                                    color: #11998e;
                                    padding: 8px 15px;
                                    font-weight: 500;
                                ">
                                                <i class="fas fa-circle status-indicator active"></i>
                                                Active
                                            </span>
                                            @if ($student->bpl_y_n == 1)
                                                <span class="badge rounded-pill mt-1"
                                                    style="
                                    background: linear-gradient(135deg, #f6d36520 0%, #fda08520 100%);
                                    color: #fda085;
                                    padding: 6px 12px;
                                    font-size: 11px;
                                ">
                                                    <i class="fas fa-heart"></i> BPL
                                                </span>
                                            @endif
                                            @if ($student->cwsn_y_n == 1)
                                                <span class="badge rounded-pill mt-1"
                                                    style="
                                    background: linear-gradient(135deg, #f093fb20 0%, #f5576c20 100%);
                                    color: #f5576c;
                                    padding: 6px 12px;
                                    font-size: 11px;
                                ">
                                                    <i class="fas fa-wheelchair"></i> CWSN
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <a href="{{ route('students.view', encrypt($student->id)) }}"
                                                    class="action-btn view" title="View Details"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', encrypt($student->id)) }}"
                                                    class="action-btn edit" title="Edit Student"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ url('students.profile', encrypt($student->id)) }}"
                                                    class="action-btn profile" title="Student Profile"
                                                    data-bs-toggle="tooltip">
                                                    <i class="fas fa-user"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="no-data">
                        <div class="no-data-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5>No students found</h5>
                        <p class="text-muted">Try adjusting your search or filters</p>
                        <a href="{{ route('students.list') }}" class="btn mt-3"
                            style="
                    background: var(--primary-gradient);
                    color: white;
                    border: none;
                    border-radius: 12px;
                    padding: 10px 30px;
                    font-weight: 600;
                ">
                            <i class="fas fa-redo me-2"></i> Reset Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if ($data['students']->hasPages())
            <div class="pagination-container">
                <div class="row align-items-center">
                    <div class="col-md-12 d-flex justify-content-center">
                        <nav aria-label="Page navigation" class="float-md-end">
                            {{ $data['students']->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/jszip.min.js') }}"></script>
    <script src="{{ asset('js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('js/buttons.print.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            let table = $('#studentTable').DataTable({
                ordering: true,
                searching: true,
                paging: false,
                info: false,
                lengthChange: false,
                dom: '<"row"<"col-sm-12"f>>' +
                    'rt' +
                    '<"d-none"B>',
                language: {
                    search: "<i class='fas fa-search'></i>",
                    searchPlaceholder: "Search in current page..."
                },
                buttons: [{
                        extend: 'print',
                        title: 'Student Directory - Complete List',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        },
                        customize: function(win) {
                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('table').addClass('compact').css('font-size',
                                '12px');
                        }
                    },
                    {
                        extend: 'csv',
                        title: 'student_directory',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'excel',
                        title: 'student_directory',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    },
                    {
                        extend: 'pdf',
                        title: 'student_directory',
                        exportOptions: {
                            columns: ':not(.no-export)'
                        }
                    }
                ]
            });

            // Dynamic loading of blocks based on district
            $('#districtSelect').change(function() {
                let districtId = $(this).val();
                let circleSelect = $('#circleSelect');
                let schoolSelect = $('#schoolSelect');

                // Reset dependent fields
                circleSelect.prop('disabled', true).empty().append('<option value="">All Circles</option>');
                schoolSelect.prop('disabled', true).empty().append('<option value="">All Schools</option>');

                if (districtId) {
                    // Load circles via AJAX
                    $.ajax({
                        url: '{{ route('get.circles') }}',
                        type: 'GET',
                        data: {
                            district_id: districtId
                        },
                        beforeSend: function() {
                            circleSelect.prop('disabled', true);
                            circleSelect.html('<option value="">Loading...</option>');
                        },
                        success: function(response) {
                            circleSelect.prop('disabled', false);
                            circleSelect.empty().append(
                                '<option value="">All Circles</option>');

                            if (response && response.length > 0) {
                                $.each(response, function(key, circle) {
                                    circleSelect.append('<option value="' + circle
                                        .encrypted_id + '">' + circle.name +
                                        '</option>');
                                });
                            }

                            // Load schools for this district
                            loadSchools(districtId, null);
                        },
                        error: function() {
                            circleSelect.prop('disabled', false);
                            circleSelect.empty().append(
                                '<option value="">All Circles</option>');
                        }
                    });
                }
            });

            // Dynamic loading of schools based on district and circle
            $('#circleSelect').change(function() {
                let districtId = $('#districtSelect').val();
                let circleId = $(this).val();
                let schoolSelect = $('#schoolSelect');

                if (districtId && circleId) {
                    loadSchools(districtId, circleId);
                } else if (districtId) {
                    loadSchools(districtId, null);
                } else {
                    schoolSelect.prop('disabled', true).empty().append(
                        '<option value="">All Schools</option>');
                }
            });

            // Function to load schools
            function loadSchools(districtId, circleId) {
                let schoolSelect = $('#schoolSelect');

                $.ajax({
                    url: '{{ route('get.schools') }}',
                    type: 'GET',
                    data: {
                        district_id: districtId,
                        circle_id: circleId
                    },
                    beforeSend: function() {
                        schoolSelect.prop('disabled', true);
                        schoolSelect.html('<option value="">Loading...</option>');
                    },
                    success: function(response) {
                        schoolSelect.prop('disabled', false);
                        schoolSelect.empty().append('<option value="">All Schools</option>');

                        if (response && response.length > 0) {
                            $.each(response, function(key, school) {
                                schoolSelect.append('<option value="' + school.encrypted_id +
                                    '">' + school.name + '</option>');
                            });
                        }
                    },
                    error: function() {
                        schoolSelect.prop('disabled', false);
                        schoolSelect.empty().append('<option value="">All Schools</option>');
                    }
                });
            }

            // Auto-submit form logic
            $('#filterForm select').not('#perPageSelect').change(function() {
                let currentId = $(this).attr('id');


            });

            // Records per page change handler
            $('#perPageSelect').change(function() {
                let perPage = $(this).val();
                let url = new URL(window.location.href);
                url.searchParams.set('per_page', perPage);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });



            // Export buttons
            $(document).on('click', '.export-print', function(e) {
                e.preventDefault();
                table.button('.buttons-print').trigger();
            });

            $(document).on('click', '.export-csv', function(e) {
                e.preventDefault();
                table.button('.buttons-csv').trigger();
            });

            $(document).on('click', '.export-excel', function(e) {
                e.preventDefault();
                table.button('.buttons-excel').trigger();
            });

            $(document).on('click', '.export-pdf', function(e) {
                e.preventDefault();
                table.button('.buttons-pdf').trigger();
            });

            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                placement: 'top'
            });

            // Smooth scroll to table on search
            $('form[method="GET"]').on('submit', function() {
                $('html, body').animate({
                    scrollTop: $('.table-container').offset().top - 100
                }, 500);
            });

            // Auto-submit form on filter changes
            $('#filterForm select').change(function() {
                if ($(this).attr('id') !== 'perPageSelect') {
                    // $('#filterForm').submit();
                }
            });
        });
    </script>
@endpush
