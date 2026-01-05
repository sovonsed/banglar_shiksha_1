<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\AccessControl\ModuleController;
use App\Http\Controllers\student_info\StudentInfoController;

use App\Http\Controllers\student_delete_deactivate\StudentDeleteDeacivateController;

// use App\Http\Controllers\AccessControl\PermissionController;
// use App\Http\Controllers\AccessControl\RoleController;
use App\Http\Controllers\{
    SchoolManagementController,
    StudentManagementController,
    TeacherManagementController,
    SiController,
};

// SSO Authentication Routes
Route::middleware(['prevent.back'])->group(function () {
    Route::get('/sso/callback', [SSOController::class, 'callback'])->name('sso.callback');
    Route::get('/sso/login', [SSOController::class, 'login'])->name('sso.login');
    Route::post('/sso/logout-callback', [SSOController::class, 'logoutCallback'])->name('sso.logout.callback');
    Route::post('/sso/logout', [SSOController::class, 'logout'])->name('sso.logout');
});

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/health', [SSOController::class, 'health'])->name('health');

// Protected routes (require SSO authentication - both web and API)
Route::middleware(['sso.auth', 'prevent.back'])->group(function () {

    // For change password
    Route::post('/change-password', [SSOController::class, 'changePassword'])->name('change.password');
    // For updating phone to UDIN
    Route::post('/api/update-phone-udin', [SSOController::class, 'updatePhoneUdin'])->middleware('auth');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin routes
    Route::prefix('admin')->name('admin.')->group(function () {
        // Impersonation routes
        Route::get('/users/{user}/impersonate', [UserController::class, 'impersonate'])->name('users.impersonate');
        Route::post('/users/stop-impersonate', [UserController::class, 'stopImpersonate'])->name('users.stop-impersonate');

        // User Management
        Route::resource('users', UserController::class);
        Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');

        // Role Management
        Route::resource('roles', RoleController::class);

        // Permission Management
        Route::resource('permissions', PermissionController::class);
        Route::post('permissions/bulk-action', [PermissionController::class, 'bulkAction'])->name('permissions.bulk-action');
    });

    // User Profile routes
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DashboardController::class, 'profileUpdate'])->name('profile.update');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [DashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/settings/password', [DashboardController::class, 'changePassword'])->name('settings.password');

    // marge route



    Route::post('/student/store', [StudentInfoController::class, 'store'])
        ->name('student.store');


    Route::prefix('hoi')->group(function () {
        // ===================Student Edit Start==========================================
        Route::match(['get','post'], '/student-edit',
        [StudentInfoController::class, 'getStudentEditDetailsByStudentCode']
        )->name('student.edit');

        Route::post(
        '/student/edit-basic-details',
        [StudentInfoController::class, 'editStudentBasicDetails']
        )->name('student.edit_student_basic_details');

        Route::post(
        '/student/store-enrollment-details-edit',
        [StudentInfoController::class, 'storeEnrollmentDetailsEdit']
        )->name('student.store_enrollment_details_edit');




        // ===================Student Edit End==========================================
        Route::get('/student-entry', [StudentInfoController::class, 'getStudentEntry'])->name('student.entry');
        // Route::get('/student-edit', [StudentInfoController::class, 'getStudentEditDetailsByStudentCode'])->name('student.edit');
        Route::post('/save-student-facility-and-other-details', [StudentInfoController::class, 'storeStudentFacilityAndOtherDetails'])->name('hoi.student.facility');
        Route::post('/save-student-vocational-details', [StudentInfoController::class, 'saveVocationalDetails'])->name('save.vocational.details');
        Route::delete('/student-entry/reset', [StudentInfoController::class, 'resetEntry'])->name('student.entry.reset');
        Route::get('/get-branches', [StudentInfoController::class, 'getBranches']);
        Route::get('/get-ifsc', [StudentInfoController::class, 'getIfsc']);
        Route::post('/student/store_student_entry_basic_details', [StudentInfoController::class, 'StoreStudentEntryStoreBasicDetails'])->name('student.store_student_entry_basic_details');
        Route::post('/student/store_enrollment_details',[StudentInfoController::class, 'storeEnrollmentDetails'])->name('student.store_enrollment_details');
        Route::post('/student/store_student_entry_contact_details',[StudentInfoController::class, 'storeStudentContactDetails'])->name('student.store_student_entry_contact_details');
        Route::post( '/student/bank_details_of_student', [StudentInfoController::class, 'bankDetailsOfStudent'])->name('student.bank_details_of_student');
        Route::post( '/student/student_additional_details', [StudentInfoController::class, 'StudentAdditionalDetails'])->name('student.student_additional_details');



    });




    // Route::view('/student-edit', 'src.modules.student_entry_update.Student_edit')
    //     ->name('student.edit');

    Route::view('/student-basic-details-update', 'src.modules.student_entry_update.Student_update_basic_details')
        ->name('student.update_basic_details');

    Route::view(
        '/update-aadhar',
        'src.modules.student_entry_update.update_student_aadhar'
    )->name('student.update_aadhar');



    // routes/web.php
    // routes/web.php
    Route::view('/student-list', 'src.modules.student_information.student_list')
        ->name('student_list.list');

    // Route::get('/student-list', [StudentInfoController::class, 'studentList'])->name('student_list.list');


    Route::group(['prefix' => 'students', 'as' => 'students.'], function () {
        // Main student list with filters
        Route::get('/list', [StudentInfoController::class, 'studentList'])->name('list');

        // AJAX routes for dynamic dropdowns
        Route::get('/blocks-by-district', [StudentInfoController::class, 'getBlocksByDistrict'])->name('blocks.by.district');
        Route::get('/schools-by-filters', [StudentInfoController::class, 'getSchoolsByFilters'])->name('schools.by.filters');

        // Individual student operations
        Route::get('/{id}/view', [StudentInfoController::class, 'view'])->name('view');
        Route::get('/{id}/edit', [StudentInfoController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [StudentInfoController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [StudentInfoController::class, 'destroy'])->name('delete');
        Route::post('/bulk-actions', [StudentInfoController::class, 'bulkActions'])->name('bulk.actions');
    });

    // AJAX routes for dynamic loading
    Route::get('/get-subdivisions', [StudentInfoController::class, 'getSubDivisionByDistrict'])->name('get.subdivisions');
    Route::get('/get-circles', [StudentInfoController::class, 'getCirclesByDistrict'])->name('get.circles');
    Route::get('/get-schools', [StudentInfoController::class, 'getSchoolsByFilters'])->name('get.schools');

    Route::view('/enrollment-report', 'src.modules.student_information.enrollment_report')
        ->name('enrollment_report.report');

    Route::view('/uniform-scms/dashboard', 'src.modules.uniform_scms.dashboard_uniform_scms')
        ->name('uniform_scms.dashboard');


    Route::view(
        '/uniform-scms/uniform-delivery-status-1st-set',
        'src.modules.uniform_scms.uniform_delivery_status_1st_set'
    )->name('uniform_scms.uniform_delivery_status_1st_set');

    // Route for the Enrollment Deactivate page
    // (If the blade file is different for this view, change the view string accordingly)


    Route::view(
        '/employee-management/employee-list',
        'src.modules.employee_management.employee_list'
    )->name('employee_list');

    Route::view(
        '/school-management/bs-udise',
        'src.modules.school_management.bs_udise'
    )->name('bs_udise');


    Route::view(
        '/reports/beneficiary_verification_status_report',
        'src.modules.reports.beneficiary_verification_status_report'
    )->name('beneficiary_verification_status_report');



    Route::get('/student-bulk-upload', function () {
        return view('src.modules.student_entry_update.student_bulk_upload');
    })->name('student.bulk.upload');



    /* Protected Routes */



    /* Protected Routes */
    // Route::resource('roles', RoleController::class);
    // Route::resource('modules', ModuleController::class);
    // Route::resource('permissions', PermissionController::class);
    // Route::get('/submodules/{module}', [ModuleController::class, 'submodules'])->name('submodules');
    // Route::get('/submodules/{module}/edit', [ModuleController::class, 'submoduleEdit'])->name('submodules.edit');
    // Route::get('/submodules/{module}/create', [ModuleController::class, 'submoduleCreate'])->name('submodules.create');


    Route::match(['get', 'post'], '/student-list/deactive', [StudentManagementController::class, 'studentDeactiveList'])->name('student.deactivelist');
    //Route::post('/student-list/deactive', [StudentManagementController::class, 'studentDeactiveSearchList'])->name('student.deactiveSearchList');

    Route::get('/add-school', [SchoolManagementController::class, 'schoolAddFrm'])->name('school.addfrm');
    Route::get('/school-add', [SchoolManagementController::class, 'schoolAddFrm'])->name('school.addform');
    Route::post('/school-add', [SchoolManagementController::class, 'schoolAdd'])->name('school.add');
    Route::post('/school-update', [SchoolManagementController::class, 'schoolUpdate'])->name('school.update');
    Route::get('/school-list/{district?}/{management?}', [SchoolManagementController::class, 'schoolList'])->name('school.list');
    Route::get('/school-list-result', [SchoolManagementController::class, 'schoolSearchRedirect'])
        ->name('school.search.redirect');
    Route::get('/school-search/{schcd?}', [SchoolManagementController::class, 'schoolSearch'])->name('school.search');
    Route::post('/school-search', [SchoolManagementController::class, 'schoolSearch'])->name('school.search1');
    Route::get('/school-details/{id}', [SchoolManagementController::class, 'schoolDetailsBySchoolId'])->name('school.details');

    Route::controller(SchoolManagementController::class)
        ->prefix('school-infrastructure-survey')
        ->name('school.infrastructureSurvey.')
        ->group(function () {
            Route::get('/', 'schoolInfrastructureSurvey')->name('index');
            Route::get('/district/{districtid}', 'schoolInfrastructureSurvey')->name('district');
            Route::get('/circle/{circleid}', 'schoolInfrastructureSurveyCircle')->name('circle');
        });

    Route::controller(SchoolManagementController::class)
        ->prefix('school-contact-details')
        ->name('school.contactDetails.')
        ->group(function () {
            Route::get('/', 'schoolContactData')->name('index');
            Route::get('/district/{districtid}', 'schoolContactData')->name('district');
            Route::get('/circle/{circleid}', 'schoolContactDataCircle')->name('circle');
        });

    Route::get('/teacher-add', [TeacherManagementController::class, 'teacherAddFrm'])->name('teacher.addfrm');
    Route::post('/teacher-add', [TeacherManagementController::class, 'teacherAddFrm'])->name('teacher.addData');

    /* UnProtected Routes */
    Route::get('/modules/{moduleId}/submodules', [ModuleController::class, 'submoduleListData'])->name('modules.submodules');
    Route::get('/dynamic/menu', [PermissionController::class, 'dynamicMenu'])->name('dynamic.menu');

    Route::prefix('api')->group(function () {
        Route::get('/district', [CommonController::class, 'getDistrict'])->name('api.get.district');
        Route::get('/educational_district', [CommonController::class, 'educationalDistrict'])->name('api.get.educational_district');
        Route::get('/blocks/{district_id}', [CommonController::class, 'getBlocksByDistrict'])->name('api.get.blocks_by_district');
        Route::get('/circles/{district_id}', [CommonController::class, 'getCircleByDistrict'])->name('api.get.circles_by_district');
        Route::get('/clusters/{district_id}', [CommonController::class, 'getClusterByDistrict'])->name('api.get.cluster_by_district');
        Route::get('/wards/{block_id}', [CommonController::class, 'getWardsByBlock'])->name('api.get.wards_by_block');

        Route::get('/subdivisions/{district_id}', [CommonController::class, 'getSubdivisions'])->name('api.get.subdivisions');
        Route::get('/management-school-category-types/{management_id}', [CommonController::class, 'getSchoolCategoryTypesByManagement'])->name('api.get.management_school_category_types');
        Route::get('/getdisecode/{ward_id}', [CommonController::class, 'getDisecode'])->name('api.get.disecode');

        Route::get('/school/block/{block_id}', [CommonController::class, 'getSchoolByBlock'])->name('api.get.schoolByBlock');

        Route::get('/get-vocational-trade-sector', [CommonController::class, 'getVocationalTradeSector'])->name('get.vocational.trade.sector');
        Route::post('/get-vocational-job-role-by-trade-sector', [CommonController::class, 'getJobRoleByVocationalTradeSector'])->name('get.jobrole.by_vocational_trade.sector');
        Route::get('/get-reason-for-deactivation', [CommonController::class, 'getReasonForDeactivation'])->name('get.reason.for.deactivation');
        Route::get('/get-reason-for-deletion', [CommonController::class, 'getReasonForDeletion'])->name('get.reason.for.deletion');
    });





    //SI Routes by Aziza Parvin Start
    Route::prefix('si')->group(function () {
        Route::get('/dashboard', [SiController::class, 'dashboard'])->name('si.dashboard');
        Route::get('/total-madrasah-school-recognized', [SiController::class, 'totalMadrasahSchoolRecognized'])->name('si.total_madrasah_school_recognized');
        Route::get('/total-madrasah-shiksha-kendra', [SiController::class, 'totalMadrasahShikshaKendra'])->name('si.total_madrasah_shiksha_kendra');
        Route::get('/total-school', [SiController::class, 'totalSchool'])->name('si.total_school');
        Route::get('/total-ssk-and-msk-school', [SiController::class, 'totalSskAndMskSchool'])->name('si.total_sssk_and_msk_school');
        Route::get('/total-students', [SiController::class, 'totalStudents'])->name('si.total_students');
        Route::get('/total-teacher', [SiController::class, 'totalTeacher'])->name('si.total_teacher');
        Route::get('/school-class-gender-wise-enrollment', [SiController::class, 'schoolClassGenderWiseEnrollmentReport'])->name('si.school_class_gender_wise_enrollment_report');
    });
    Route::prefix('student')->group(function () {
        Route::get('/student-deactivate', [StudentDeleteDeacivateController::class, 'deactivateStudentView'])->name('student.deactivate.view');
        Route::post('/student-search-by-student-code', [StudentDeleteDeacivateController::class, 'searchStudentByStudentCode'])->name('search.student.by.student_code');
        Route::post('/deactivate', [StudentDeleteDeacivateController::class, 'deactivateStudent'])->name('student.deactivate');
        Route::get('/delete/list', [StudentDeleteDeacivateController::class, 'deletedStudentView'])->name('student.delete.view');
        Route::post('/delete', [StudentDeleteDeacivateController::class, 'deleteStudent'])->name('student.delete');
        Route::post('/activate', [StudentDeleteDeacivateController::class, 'activateStudent'])->name('student.activate');

    });


    // Route::get('/test-error', function () {
    //     // wrong SQL to trigger QueryException
    //     DB::select("SELECT * FORM schools");
    // });

    //SI Routes by Aziza Parvin End





    // ============ NEW ROUTES ADDED FOR MISSING MENU ITEMS ============

    // Uniform-SCMS - Additional Routes
    Route::view(
        '/uniform-scms/uniform-delivery-status-2nd-set',
        'working-in-progress'
    )->name('uniform_scms.uniform_delivery_status_2nd_set');

    // Student Entry / Update - Additional Routes
    Route::view('/student/spa-error', 'working-in-progress')->name('student.spa_error');
    Route::view('/student/student-mapping-with-avic', 'working-in-progress')->name('student.student_mapping_with_avic');
    Route::view('/student/update-identity', 'working-in-progress')->name('student.update_identity');
    Route::view('/student/additional-info', 'working-in-progress')->name('student.additional_info');
    Route::view('/student/update-section', 'working-in-progress')->name('student.update_section');
    Route::view('/student/update-polling', 'working-in-progress')->name('student.update_polling');
    Route::view('/student/bulk-upload', 'working-in-progress')->name('student.bulk_upload');

    // Student Information - All Missing Routes
    Route::prefix('student-info')->name('student_info.')->group(function () {
        Route::view('/list-view', 'working-in-progress')->name('list_view');
        Route::view('/enrollment-report', 'working-in-progress')->name('enrollment_report');
        Route::view('/medium-wise-report', 'working-in-progress')->name('medium_wise_report');
        Route::view('/download-info', 'working-in-progress')->name('download_info');
        Route::view('/incomplete-profile', 'working-in-progress')->name('incomplete_profile');
        Route::view('/aadhaar-blank', 'working-in-progress')->name('aadhaar_blank');
        Route::view('/image-upload-status', 'working-in-progress')->name('image_upload_status');
        Route::view('/graduation-ceremony', 'working-in-progress')->name('graduation_ceremony');
        Route::view('/download-aadhaar-blank', 'working-in-progress')->name('download_aadhaar_blank');
        Route::view('/download-bank-details', 'working-in-progress')->name('download_bank_details');
        Route::view('/bulk-uploaded-list', 'working-in-progress')->name('bulk_uploaded_list');
    });

    // Student Promotion & Transfer - All Routes
    Route::prefix('student-promotion')->name('student_promotion.')->group(function () {
        Route::view('/class-wise-additional-info', 'working-in-progress')->name('class_wise_additional_info');
        Route::view('/promotion-detention', 'working-in-progress')->name('promotion_detention');
        Route::view('/promotion-detention-2022-23', 'working-in-progress')->name('promotion_detention_2022_23');
        Route::view('/update-board-exam-result', 'working-in-progress')->name('update_board_exam_result');
        Route::view('/senior-students', 'working-in-progress')->name('senior_students');
        Route::view('/transfer-out', 'working-in-progress')->name('transfer_out');
        Route::view('/transfer-in', 'working-in-progress')->name('transfer_in');
        Route::view('/transferred-list', 'working-in-progress')->name('transferred_list');
    });

    // Student Delete / Deactivate - Additional Routes
    Route::prefix('student-delete')->name('student_delete.')->group(function () {
        Route::view('/ex-students', 'working-in-progress')->name('ex_students');
        Route::view('/deactivation', 'working-in-progress')->name('deactivation');
        Route::view('/delete', 'working-in-progress')->name('delete');
    });

    // School Management - Additional Route


    // Employee Management - Additional Route
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::view('/list', 'working-in-progress')->name('list');
    });

    // Mission Banglar Shiksha - All Routes
    Route::prefix('mission')->name('mission.')->group(function () {
        Route::view('/download-basic-details', 'working-in-progress')->name('download_basic_details');
        Route::view('/enrolment-certificate', 'working-in-progress')->name('enrolment_certificate');
        Route::view('/school-basic-details', 'working-in-progress')->name('school_basic_details');
    });

    // Incentives - All Routes
    Route::prefix('incentives')->name('incentives.')->group(function () {
        Route::view('/challan-wise-stock', 'working-in-progress')->name('challan_wise_stock');
        Route::view('/student-wise-distribution', 'working-in-progress')->name('student_wise_distribution');
        Route::view('/available-stock', 'working-in-progress')->name('available_stock');
        Route::view('/stock-received-history', 'working-in-progress')->name('stock_received_history');
        Route::view('/stock-distribution-history', 'working-in-progress')->name('stock_distribution_history');
        Route::view('/opening-stock-entry', 'working-in-progress')->name('opening_stock_entry');
        Route::view('/stock-return-history', 'working-in-progress')->name('stock_return_history');
    });

    // Download - All Routes
    Route::prefix('downloads')->name('downloads.')->group(function () {
        Route::view('/presentation', 'working-in-progress')->name('presentation');
        Route::view('/training-schedule', 'working-in-progress')->name('training_schedule');
        Route::view('/school-user-manual', 'working-in-progress')->name('school_user_manual');
        Route::view('/student-doe', 'working-in-progress')->name('student_doe');
        Route::view('/student-doe-instruction', 'working-in-progress')->name('student_doe_instruction');
    });

    // Account Management - All Routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::view('/update-contact', 'working-in-progress')->name('update_contact');
        Route::view('/change-password', 'working-in-progress')->name('change_password');
    });

    // ALP - Route
    Route::view('/alp/download', 'working-in-progress')->name('alp.download');
    // ============ END OF NEW ROUTES ============




    // ===========Password Reset HOI====================

    // example

});

// Fallback route
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
