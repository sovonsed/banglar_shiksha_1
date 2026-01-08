@extends('layouts.app')

@section('title', 'Student List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
@endpush

@section('content')
    <!-- container -->
    <div class="container-fluid px-4">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-blue">
                            <i class="bx bx-group text-primary"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-primary">{{ number_format($data['total_students']) }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">Total Students</h3>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-green">
                            <i class="bx bx-user text-success"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-success">{{ number_format($data['male_students']) }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">Male</h3>
                </div>
              </div>
            </div> 

            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-yellow">
                            <i class="bx bx-user text-warning"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-warning">{{ number_format($data['female_students']) }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">Female</h3>
                </div>
              </div>
            </div>
            
            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-red">
                            <i class="bx bx-user text-danger"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-danger">{{ number_format($data['bpl_students']) }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">BPL Students</h3>
                </div>
              </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-purple">
                            <i class="bx bx-accessibility text-purple"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-purple">{{ number_format($data['bpl_students']) }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">CWSN Students</h3>
                </div>
              </div>
            </div>

            <div class="col-md-2 mb-4">
                <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar lt-seegreen">
                            <i class='bx bxs-graduation text-info'></i> 
                        </div>
                    </div>
                    <h2 class="fw-bold mb-2 text-info">{{ $data['class_distribution'] ?? 0 }}</h2>
                    <h3 class="card-title mb-0 fw-semibold">Classes</h3>
                </div>
              </div>
            </div>
       
        </div>      
            
        
        
        <div class="card mb-4 mt-2">
                <h5 class="card-header bg-primary text-white fw-semibold ">Advanced Student Filters</h5>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- District -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">District</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-map"></i></span>
                            <select class="form-select" name="district_id" id="districtSelect">
                                <option value="">All Districts</option>
                                @foreach ($data['districts'] as $district)
                                    <option value="{{ Crypt::encrypt($district->id) }}"
                                        {{ $selected_district_id == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                        <!-- Block -->
                        <!-- Circle -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Circle</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-data"></i></span>
                            <select class="form-select" name="circle_id" id="circleSelect"
                                {{ !$selected_district_id ? 'disabled' : '' }}>
                                <option value="">All Circles</option>
                                @if ($selected_district_id && isset($data['circles']))
                                    @foreach ($data['circles']->where('district_id', $selected_district_id) as $circle)
                                        <option value="{{ Crypt::encrypt($circle->id) }}"
                                            {{ $selected_circle_id == $circle->id ? 'selected' : '' }}>
                                            {{ $circle->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            </div>
                        </div>

                        <!-- Management -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Management</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-list-ul"></i></span>
                            <select class="form-select" name="management_id">
                                <option value="">All Management</option>
                                @foreach ($data['managements'] as $management)
                                    <option value="{{ Crypt::encrypt($management->id) }}"
                                        {{ $selected_management_id == $management->id ? 'selected' : '' }}>
                                        {{ $management->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <!-- School -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">School</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-building"></i></span>
                            <select class="form-select" name="school_id" id="schoolSelect"
                                {{ !$selected_district_id ? 'disabled' : '' }}>
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
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Gender</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-male-sign"></i></span>
                            <select class="form-select" name="gender">
                                <option value="">All Gender</option>
                                @foreach ($data['genders'] ?? [] as $gender)
                                    <option value="{{ $gender->id }}"
                                        {{ $gender_param == $gender->id ? 'selected' : '' }}>
                                        {{ $gender->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <!-- Class -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Class</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bxs-graduation"></i></span>
                            <select class="form-select" name="class">
                                <option value="">All Classes</option>
                                @foreach ($data['classes'] ?? [] as $class)
                                    <option value="{{ $class->id }}"
                                        {{ $class_param == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <!-- Social Category -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">School Category</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-sitemap"></i></span>
                            <select class="form-select" name="category">
                                <option value="">All Categories</option>
                                @foreach ($data['categories'] as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $category_param == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <!-- Academic Year -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">Academic Year</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                            <select class="form-select" name="academic_year">
                                <option value="">All Years</option>
                                @foreach ($data['academic_years'] as $year)
                                    <option value="{{ $year }}"
                                        {{ $academic_year_param == $year ? 'selected' : '' }}>
                                        {{ $year }}-{{ $year + 1 }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <!-- BPL Status -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">BPL Status</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user-check"></i></span>
                            <select class="form-select" name="bpl">
                                <option value="">All</option>
                                <option value="1" {{ $bpl_param == '1' ? 'selected' : '' }}>BPL</option>
                                <option value="0" {{ $bpl_param == '0' ? 'selected' : '' }}>Non-BPL</option>
                            </select>
                            </div>
                        </div>

                        <!-- CWSN Status -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted mb-2">CWSN Status</label>
                            <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-user-check"></i></span>
                            <select class="form-select" name="cwsn">
                                <option value="">All</option>
                                <option value="2" {{ $cwsn_param == '2' ? 'selected' : '' }}>CWSN</option>
                                <option value="0" {{ $cwsn_param == '0' ? 'selected' : '' }}>Non-CWSN</option>
                            </select>
                            </div>
                        </div>
			        </div>
                    
                    <div class="row">
                        <div class="col-md-12 text-center mt-4">
                        <button type="submit" class="btn btn-success"><i class="bx bx-filter"></i> Apply Filters</button>
                        <a href="{{ route('students.list') }}" class="btn btn-danger"><i class='bx bx-arrow-back'></i>  Clear</a>
                    	</div>
                   </div> 
                </div>
           </div>
           
           <!-- Search Box -->
           <div class="card mb-4 mt-2">
                <h5 class="card-header bg-primary text-white fw-semibold ">Advanced Search</h5>
                <div class="card-body">
                <form method="GET" action="{{ route('students.list') }}">
                    <div class="row g-3">
                         <div class="col-md-8">
                                <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text" name="search" class="form-control search-input" placeholder="Search by student name, admission number, Aadhaar, father name, school..." value="{{ $search_param }}">
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
                         
                         <div class="col-md-2">
                            <div class="input-group">
                                <button type="submit" class="btn btn-success"><i class="bx bx-search"></i> Search</button>
                            </div>
                         </div>
                          
                         <div class="col-md-2">
                           <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-filter"></i></span>
                            <select class="form-select per-page-select" id="perPageSelect" name="per_page"
    >
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
           </div>
           
          <div class="card mb-4 mt-2">
            <h5 class="card-header bg-primary text-white fw-semibold ">Search Details</h5>
            <div class="card-body">
              dddddddd
            </div>
          </div> 
           
        </div>  
       <!-- container -->
        
        
        
        
        
        
        
        
        
        
        
        
        

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
                                                <i class="fas fa-id-card me-1"></i> {{ $student->admission_no ?? 'N/A' }}
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
                                                {{ $student->block->name ?? 'N/A' }}
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
                                            @if ($student->cwsn_y_n == 2)
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
