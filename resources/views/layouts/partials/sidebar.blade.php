<!-- resources/views/layouts/partials/sidebar.blade.php -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand / Logo Section -->
    <div class="app-brand demo text-center bg-primary py-3">
        <a href="{{ route('dashboard') }}" class="d-inline-block">
            <img class="img-fluid" src="{{ asset('images/central-portal.png') }}" alt="Banglar Shiksha Logo">
        </a>
    </div>

    <ul class="menu-inner pt-3">
        <!-- Dashboard -->
        @can('view dashboard')
            <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>Dashboard</div>
                </a>
            </li>
        @endcan

        <!-- Access Control -->
        @canany(['view users', 'view roles', 'view permissions'])
            <li
                class="menu-item {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('modules.*') || request()->routeIs('permissions.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-layout"></i>
                    <div data-i18n="Layouts">Access Control</div>
                </a>

                <ul class="menu-sub">
                    @can('view users')
                        <li class="menu-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Users</div>
                            </a>
                        </li>
                    @endcan

                    @can('view roles')
                        <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Roles</div>
                            </a>
                        </li>
                    @endcan


                    @can('view permissions')
                        <li class="menu-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.permissions.index') }}" class="menu-link">
                                <div data-i18n="Without menu">Permissions</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany
        <!-- ------------ Uniform SCMS Module -------------- -->
        @canany(['view uniform', 'view scms', 'view delivery'])
            <li class="menu-item {{ request()->routeIs('uniform_scms.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Uniform-SCMS</div>
                </a>

                <ul class="menu-sub">
                    @can('view uniform')
                        <li class="menu-item {{ request()->routeIs('uniform_scms.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('uniform_scms.dashboard') }}" class="menu-link">
                                <div>Dashboard</div>
                            </a>
                        </li>
                    @endcan
                </ul>

                <ul class="menu-sub">
                    @can('view delivery')
                        <li
                            class="menu-item {{ request()->routeIs('uniform_scms.uniform_delivery_status_1st_set') ? 'active' : '' }}">
                            <a href="{{ route('uniform_scms.uniform_delivery_status_1st_set') }}" class="menu-link">
                                <div>Uniform Delivery Status for 1st set</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Student Delete / Deactivate -->
        @canany(['view delete', 'manage delete', 'view deactivate', 'manage deactivate'])
            <li
                class="menu-item {{ request()->routeIs('student.delete.view') || request()->routeIs('student.deactivate.view') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Student Delete / Deactivate</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view delete', 'manage delete'])
                        <li class="menu-item {{ request()->routeIs('student.delete.view') ? 'active' : '' }}">
                            <a href="{{ route('student.delete.view') }}" class="menu-link">
                                <div>Students Delete</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view deactivate', 'manage deactivate'])
                        <li class="menu-item {{ request()->routeIs('student.deactivate.view') ? 'active' : '' }}">
                            <a href="{{ route('student.deactivate.view') }}" class="menu-link">
                                <div>Student Deactivate</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany


        <!-- Student Promotion & Transfer -->
        @canany(['view transfer out', 'manage transfer out', 'view transfer in', 'manage transfer in'])
            <li class="menu-item {{ request()->routeIs('student.transfer*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-transfer"></i>
                    <div>Student Promotion & Transfer</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view transfer out', 'manage transfer out'])
                        <li class="menu-item {{ request()->routeIs('student.transferout*') ? 'active' : '' }}">
                            <a href="{{ route('student.transferout.index') }}" class="menu-link">
                                <div>Student's Transfer Out</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view transfer in', 'manage transfer in'])
                        <li class="menu-item {{ request()->routeIs('student.transferin*') ? 'active' : '' }}">
                            <a href="{{ route('student.transferin.index') }}" class="menu-link">
                                <div>Student's Transfer In</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany


        <!-- Student Entry / Update -->
        @canany([
            'view entry',
            'create entry',
            'edit entry',
            'view profile',
            'edit profile',
            'download profile',
            'update
            basic',
            'update deactivation',
            'update aadhar',
            'manage mapping',
            'update identity',
            'manage additional',
            'update section',
            'bulk
            upload',
            'update polling',
            ])
            <li
                class="menu-item {{ request()->routeIs('student.entry*') ||
                request()->routeIs('student.edit*') ||
                request()->routeIs('student.update*') ||
                request()->routeIs('student.bulk*')
                    ? 'active open'
                    : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>Student Entry / Update</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view entry', 'create entry'])
                        <li class="menu-item {{ request()->routeIs('student.entry') ? 'active' : '' }}">
                            <a href="{{ route('student.entry') }}" class="menu-link">
                                <div>Student Entry</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view profile', 'edit profile', 'download profile'])
                        <li class="menu-item {{ request()->routeIs('student.edit') ? 'active' : '' }}">
                            <a href="{{ route('student.edit') }}" class="menu-link">
                                <div>Student Profile Edit / Download</div>
                            </a>
                        </li>
                    @endcanany

                    @can('update basic')
                        <li class="menu-item {{ request()->routeIs('student.update_basic_details') ? 'active' : '' }}">
                            <a href="{{ route('student.update_basic_details') }}" class="menu-link">
                                <div>Update Student Basic Details</div>
                            </a>
                        </li>
                    @endcan

                    @can('update deactivation')
                        <li class="menu-item {{ request()->routeIs('student.deactivelist') ? 'active' : '' }}">
                            <a href="{{ route('student.deactivelist') }}" class="menu-link">
                                <div>Student for Deactivation</div>
                            </a>
                        </li>
                    @endcan

                    @can('update aadhar')
                        <li class="menu-item {{ request()->routeIs('student.update_aadhar') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Update Aadhar</div>
                            </a>
                        </li>
                    @endcan

                    @can('manage mapping')
                        <li class="menu-item {{ request()->routeIs('student.student_mapping_with_awc') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Student Mapping with AWC</div>
                            </a>
                        </li>
                    @endcan

                    @can('update identity')
                        <li class="menu-item {{ request()->routeIs('student.update_identity') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Update Student Identity</div>
                            </a>
                        </li>
                    @endcan

                    @can('manage additional')
                        <li class="menu-item {{ request()->routeIs('student.additional_info') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Addl. Information UDISE+ 2023–24</div>
                            </a>
                        </li>
                    @endcan

                    @can('update section')
                        <li class="menu-item {{ request()->routeIs('student.update_section') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Update Section / Roll No</div>
                            </a>
                        </li>
                    @endcan

                    @can('bulk upload')
                        <li class="menu-item {{ request()->routeIs('student.bulk_upload') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Student Entry Bulk Upload</div>
                            </a>
                        </li>
                    @endcan

                    @can('update polling')
                        <li class="menu-item {{ request()->routeIs('student.update_polling') ? 'active' : '' }}">
                            <a href="{{ route('student.update_aadhar') }}" class="menu-link">
                                <div>Updating Polling Station & Disability Certificate Reference</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- School Information -->
        @canany([
            'view school',
            'create school',
            'edit school',
            'delete school',
            'list schools',
            'view survey',
            'manage
            survey',
            'view contacts',
            'update contacts',
            ])
            <li class="menu-item {{ request()->routeIs('school.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>School Information</div>
                </a>

                <ul class="menu-sub">
                    @can('create school')
                        <li class="menu-item {{ request()->routeIs('school.addfrm') ? 'active' : '' }}">
                            <a href="{{ route('school.addfrm') }}" class="menu-link">
                                <div>School Add</div>
                            </a>
                        </li>
                    @endcan

                    @can('edit school')
                        <li class="menu-item {{ request()->routeIs('school.search') ? 'active' : '' }}">
                            <a href="{{ route('school.search') }}" class="menu-link">
                                <div>School Update</div>
                            </a>
                        </li>
                    @endcan

                    @can('list schools')
                        <li class="menu-item {{ request()->routeIs('school.list') ? 'active' : '' }}">
                            <a href="{{ route('school.list') }}" class="menu-link">
                                <div>School List</div>
                            </a>
                        </li>
                    @endcan

                    @canany(['view survey', 'manage survey'])
                        <li class="menu-item {{ request()->routeIs('school.infrastructureSurvey.*') ? 'active' : '' }}">
                            <a href="{{ route('school.infrastructureSurvey.index') }}" class="menu-link">
                                <div>Infrastructure Survey Progress monitoring report (District Wise)</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view contacts', 'update contacts'])
                        <li class="menu-item {{ request()->routeIs('school.contactDetails.*') ? 'active' : '' }}">
                            <a href="{{ route('school.contactDetails.index') }}" class="menu-link">
                                <div>District wise Primary & Secondary School Contact Details Update Report</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        <!-- Student Information -->
        @canany(['view students', 'export students', 'view enrollment', 'generate enrollment', 'export enrollment'])
            <li
                class="menu-item {{ request()->routeIs('student_list.*') || request()->routeIs('enrollment_report.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Student Information</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view students', 'export students'])
                        <li class="menu-item {{ request()->routeIs('students.list') ? 'active' : '' }}">
                            <a href="{{ route('students.list') }}" class="menu-link">
                                <div>Students' List View</div>
                            </a>
                        </li>
                    @endcanany

                    @canany(['view enrollment', 'generate enrollment', 'export enrollment'])
                        <li class="menu-item {{ request()->routeIs('enrollment_report.report') ? 'active' : '' }}">
                            <a href="{{ route('enrollment_report.report') }}" class="menu-link">
                                <div>Enrollment Report</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        <!-- Employee Management -->
        @canany(['view employees', 'create employees', 'edit employees', 'delete employees', 'export employees'])
            <li class="menu-item {{ request()->routeIs('employee_list') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Employee Management</div>
                </a>

                <ul class="menu-sub">
                    @canany([
                        'view employees',
                        'create employees',
                        'edit employees',
                        'delete employees',
                        'export
                        employees',
                        ])
                        <li class="menu-item {{ request()->routeIs('employee_list') ? 'active' : '' }}">
                            <a href="{{ route('employee_list') }}" class="menu-link">
                                <div>Employee List</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        <!-- School Management -->
        @canany(['view management', 'manage management', 'view udise'])
            <li class="menu-item {{ request()->routeIs('bs_udise') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>School Management</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view management', 'manage management', 'view udise'])
                        <li class="menu-item {{ request()->routeIs('bs_udise') ? 'active' : '' }}">
                            <a href="{{ route('bs_udise') }}" class="menu-link">
                                <div>School Information</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        <!-- Reports -->
        @canany(['view reports', 'generate reports', 'export reports', 'view beneficiary', 'export beneficiary'])
            <li class="menu-item {{ request()->routeIs('beneficiary_verification_status_report') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div>Reports</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view beneficiary', 'export beneficiary'])
                        <li
                            class="menu-item {{ request()->routeIs('beneficiary_verification_status_report') ? 'active' : '' }}">
                            <a href="{{ route('beneficiary_verification_status_report') }}" class="menu-link">
                                <div>Student wise Beneficiary Verification Status Report</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany




        <!-- Employee Management -->
        @canany(['view employees', 'create employees', 'edit employees', 'delete employees', 'export employees'])
            <li class="menu-item {{ request()->routeIs('employee.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-group"></i>
                    <div>Employee Management</div>
                </a>

                <ul class="menu-sub">
                    @canany(['view employees', 'create employees', 'edit employees', 'delete employees', 'export
                        employees'])
                        <li class="menu-item {{ request()->routeIs('employee.list') ? 'active' : '' }}">
                            <a href="{{ route('employee.list') }}" class="menu-link">
                                <div>Employee List</div>
                            </a>
                        </li>
                    @endcanany
                </ul>
            </li>
        @endcanany

        <!-- Mission Banglar Shiksha -->
        @canany(['view mission', 'manage mission', 'download school_details', 'view school_basic', 'download
            enrolment_certificate'])
            <li class="menu-item {{ request()->routeIs('mission.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-book-alt"></i>
                    <div>Mission Banglar Shiksha</div>
                </a>

                <ul class="menu-sub">
                    @can('download school_details')
                        <li class="menu-item {{ request()->routeIs('mission.download_basic_details') ? 'active' : '' }}">
                            <a href="{{ route('mission.download_basic_details') }}" class="menu-link">
                                <div>Download School Basic Details</div>
                            </a>
                        </li>
                    @endcan

                    @can('download enrolment_certificate')
                        <li class="menu-item {{ request()->routeIs('mission.enrolment_certificate') ? 'active' : '' }}">
                            <a href="{{ route('mission.enrolment_certificate') }}" class="menu-link">
                                <div>Enrolment Certificate</div>
                            </a>
                        </li>
                    @endcan

                    @can('view school_basic')
                        <li class="menu-item {{ request()->routeIs('mission.school_basic_details') ? 'active' : '' }}">
                            <a href="{{ route('mission.school_basic_details') }}" class="menu-link">
                                <div>School Basic Details View</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        <!-- Incentives -->
        @canany(['view incentives', 'manage incentives', 'view stock', 'manage stock', 'view distribution', 'manage
            distribution'])
            <li class="menu-item {{ request()->routeIs('incentives.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-gift"></i>
                    <div>Incentives</div>
                </a>

                <ul class="menu-sub">
                    @can('view stock')
                        <li class="menu-item {{ request()->routeIs('incentives.challan_wise_stock') ? 'active' : '' }}">
                            <a href="{{ route('incentives.challan_wise_stock') }}" class="menu-link">
                                <div>Challan wise stock received details</div>
                            </a>
                        </li>
                    @endcan

                    @can('view distribution')
                        <li
                            class="menu-item {{ request()->routeIs('incentives.student_wise_distribution') ? 'active' : '' }}">
                            <a href="{{ route('incentives.student_wise_distribution') }}" class="menu-link">
                                <div>Student wise Stock Distribution</div>
                            </a>
                        </li>
                    @endcan

                    @can('view stock')
                        <li class="menu-item {{ request()->routeIs('incentives.available_stock') ? 'active' : '' }}">
                            <a href="{{ route('incentives.available_stock') }}" class="menu-link">
                                <div>Available Stock</div>
                            </a>
                        </li>
                    @endcan

                    @can('view stock')
                        <li class="menu-item {{ request()->routeIs('incentives.stock_received_history') ? 'active' : '' }}">
                            <a href="{{ route('incentives.stock_received_history') }}" class="menu-link">
                                <div>Stock Received History</div>
                            </a>
                        </li>
                    @endcan

                    @can('view distribution')
                        <li
                            class="menu-item {{ request()->routeIs('incentives.stock_distribution_history') ? 'active' : '' }}">
                            <a href="{{ route('incentives.stock_distribution_history') }}" class="menu-link">
                                <div>Stock Distribution History</div>
                            </a>
                        </li>
                    @endcan

                    @can('manage stock')
                        <li class="menu-item {{ request()->routeIs('incentives.opening_stock_entry') ? 'active' : '' }}">
                            <a href="{{ route('incentives.opening_stock_entry') }}" class="menu-link">
                                <div>Opening Stock Entry</div>
                            </a>
                        </li>
                    @endcan

                    @can('view stock')
                        <li class="menu-item {{ request()->routeIs('incentives.stock_return_history') ? 'active' : '' }}">
                            <a href="{{ route('incentives.stock_return_history') }}" class="menu-link">
                                <div>Stock Return History</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Download -->
        @canany(['view downloads', 'download materials', 'view presentations', 'view training', 'view manuals'])
            <li class="menu-item {{ request()->routeIs('downloads.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-download"></i>
                    <div>Download</div>
                </a>

                <ul class="menu-sub">
                    @can('view presentations')
                        <li class="menu-item {{ request()->routeIs('downloads.presentation') ? 'active' : '' }}">
                            <a href="{{ route('downloads.presentation') }}" class="menu-link">
                                <div>Presentation of Central Portal</div>
                            </a>
                        </li>
                    @endcan

                    @can('view training')
                        <li class="menu-item {{ request()->routeIs('downloads.training_schedule') ? 'active' : '' }}">
                            <a href="{{ route('downloads.training_schedule') }}" class="menu-link">
                                <div>Training Time Schedule</div>
                            </a>
                        </li>
                    @endcan

                    @can('view manuals')
                        <li class="menu-item {{ request()->routeIs('downloads.school_user_manual') ? 'active' : '' }}">
                            <a href="{{ route('downloads.school_user_manual') }}" class="menu-link">
                                <div>School Level User Manual</div>
                            </a>
                        </li>
                    @endcan

                    @can('download materials')
                        <li class="menu-item {{ request()->routeIs('downloads.student_doe') ? 'active' : '' }}">
                            <a href="{{ route('downloads.student_doe') }}" class="menu-link">
                                <div>Student DOE ( )</div>
                            </a>
                        </li>
                    @endcan

                    @can('download materials')
                        <li class="menu-item {{ request()->routeIs('downloads.student_doe_instruction') ? 'active' : '' }}">
                            <a href="{{ route('downloads.student_doe_instruction') }}" class="menu-link">
                                <div>Student DOE Instruction</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- Account Management -->
        @canany(['view account', 'manage account', 'update contact', 'change password'])
            <li class="menu-item {{ request()->routeIs('account.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-cog"></i>
                    <div>Account Management</div>
                </a>

                <ul class="menu-sub">
                    @can('update contact')
                        <li class="menu-item {{ request()->routeIs('account.update_contact') ? 'active' : '' }}">
                            <a href="{{ route('account.update_contact') }}" class="menu-link">
                                <div>Update Contact No. in UDISE+</div>
                            </a>
                        </li>
                    @endcan

                    @can('change password')
                        <li class="menu-item {{ request()->routeIs('account.change_password') ? 'active' : '' }}">
                            <a href="{{ route('account.change_password') }}" class="menu-link">
                                <div>Change Password</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        <!-- ALP -->
        @canany(['view alp', 'download alp'])
            <li class="menu-item {{ request()->routeIs('alp.*') ? 'active' : '' }}">
                <a href="{{ route('alp.download') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-file"></i>
                    <div>ALP</div>
                </a>
            </li>
        @endcanany
    </ul>
</aside>
