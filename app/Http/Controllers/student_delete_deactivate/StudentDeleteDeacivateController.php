<?php

namespace App\Http\Controllers\student_delete_deactivate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    StudentMaster};
use App\Models\student_delete_deactivate\StudentDeactivateModel;
use App\Models\student_delete_deactivate\StudentDeleteTrackModel;
use Illuminate\Support\Facades\DB;

class StudentDeleteDeacivateController extends Controller
{
    // =========================Deactivated Students View & Deactivate Student Start==========================
    public function deactivateStudentView()
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            // dd($roleName);
            $query = StudentDeactivateModel::query()
                ->with([
                    'studentInfo:student_code,studentname,dob,guardian_name,cur_roll_number',
                    'deleteReason:id,name',
                    'currentClass:id,name',
                    'currentSection:id,name',
                    'schoolInfo:id,school_name'
                ])
                ->where('status', 3);;
            if (in_array($roleName, ['School Admin', 'HOI Primary'])) {
                // School user → only their school
                $query->where('school_code_fk', $school_id)
                    ->where('status', 3);
            } elseif ($roleName === 'SI') {
                // SI officer → district + circle
                $query->where('district_code_fk', $district_id)
                    ->where('circle_code_fk', $circle_id);
            } elseif ($roleName === 'District Officer') {
                // District officer → district only
                $query->where('district_code_fk', $district_id);
            }
            // dd($query);
            $deactive_students = $query->get();

            return view(
                'src.modules.student_delete_deactivate.student_deactivated',
                compact('deactive_students', 'user')
            );
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred',
                false   => $e->getMessage(),
            ], 500);
        }
    }

    public function searchStudentByStudentCode(Request $request)
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            dd($school_id);

            // -------------------------------
            // Validation
            // -------------------------------
            $request->validate([
                'student_code'    => ['required', 'digits:14'],
                'search_purpose'  => ['required'], // 1=Deactivate, 2=Delete
            ]);

            $student_code   = $request->student_code;
            $searchPurpose  = (int) $request->search_purpose;

            // -------------------------------
            // Base query
            // -------------------------------
            $query = StudentMaster::with([
                'currentClass:id,name',
                'currentSection:id,name'
            ])
                ->select([
                    'student_code',
                    'studentname',
                    'dob',
                    'guardian_name',
                    'cur_class_code_fk',
                    'cur_section_code_fk',
                    'cur_roll_number',
                    'status'
                ])
                ->where('student_code', $student_code);

            // =================================================
            // ROLE BASED FILTERING
            // =================================================

            // -------------------------------
            // HOI / SCHOOL ADMIN
            // -------------------------------
            if (in_array($roleName, ['HOI Primary'])) {
                $query->where('school_code_fk', $school_id)
                    ->where('status', 1);

                // -------------------------------
                // SI (CIRCLE OFFICER)
                // -------------------------------
            } elseif ($roleName === 'SI') {
                $query->where('district_code_fk', $district_id)
                    ->where('circle_code_fk', $circle_id);
                    if($searchPurpose === 2)
                    {
                        $query->where('status', 2);
                    }
                    else if($searchPurpose === 1)
                    {
                        $query->where('status', 3);
                    }
                // -------------------------------
                // DISTRICT OFFICER
                // -------------------------------
            } elseif ($roleName === 'District Officer') {

                $query->where('district_code_fk', $district_id)
                    ->whereIn('status', [1,2,3]);
            }
            dd($query);
            $student = $query->first();
            // dd($student);


            if (!$student) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Student not found or not pending for selected action'
                ], 200);
            }

            // -------------------------------
            // Response
            // -------------------------------
            return response()->json([
                'status' => true,
                'data'   => [
                    'student_code'    => $student->student_code,
                    'studentname'     => $student->getAttributes()['studentname'],
                    'dob'             => $student->dob,
                    'guardian_name'   => $student->guardian_name,
                    'current_class'   => $student->currentClass?->name,
                    'current_section' => $student->currentSection?->name,
                    'cur_roll_number' => $student->cur_roll_number,
                    'status'          => $student->status,
                    'search_purpose'  => $searchPurpose,
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {

            return response()->json([
                'status'  => false,
                'message' => 'An error occurred',
                false   => $e->getMessage(),
            ], 500);
        }
    }

    public function deactivateStudent(Request $request)
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;

            // -------------------------------
            // 1. Validate input
            // -------------------------------
            $data = $request->validate([
                'student_code' => 'required|string|size:14',
                'deactivate_reason_code_fk' => 'required|integer|exists:bs_reason_student_deactivation_master,id',
            ]);

            // -------------------------------
            // 2. Build student query (ROLE BASED)
            // -------------------------------            
            DB::beginTransaction();
            $studentQuery = DB::table('bs_student_master')
                ->where('student_code', $data['student_code'])
                ->lockForUpdate();

            // SCHOOL USER
            if (in_array($roleName, ['HOI Primary'])) {
                $studentQuery->where('school_code_fk', $school_id);

                // CIRCLE OFFICER
            } elseif ($roleName === 'SI') {
                $studentQuery->where('district_code_fk', $district_id)
                    ->where('circle_code_fk', $circle_id);

                // DISTRICT OFFICER
            } elseif ($roleName === 'District Officer') {
                $studentQuery->where('district_code_fk', $district_id);
            }

            $student = $studentQuery->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Student not found or access denied',
                ], 404);
            }

            // -------------------------------
            // 3. Insert into deactivate tracking table
            // -------------------------------
            StudentDeactivateModel::create([
                'student_code'              => $student->student_code,
                'school_code_fk'            => $student->school_code_fk,
                'district_code_fk'          => $student->district_code_fk,
                'circle_code_fk'            => $student->circle_code_fk,
                'cur_class_code_fk'         => $student->cur_class_code_fk,
                'cur_section_code_fk'       => $student->cur_section_code_fk,
                'deactivate_reason_code_fk' => $data['deactivate_reason_code_fk'],
                'operation_by'              => $user->id,
                'operation_by_stake_cd'     => $user->stake_cd ?? null,
                'operation_time'            => now(),
                'operation_ip'              => $request->ip(),
                'prev_status'               => $student->status,
                'status'                    => 3 //add from config
            ]);

            // -------------------------------
            // 4. Update student master
            // -------------------------------
            DB::table('bs_student_master')
                ->where('student_code', $student->student_code)
                ->update([
                    'status'       => 3, // Deactivated
                    'updated_at'   => now(),
                    'update_ip'    => $request->ip(),
                    'updated_by'   => $user->id,
                ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Student deactivated successfully',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                false   => $e->getMessage(),
            ], 500);
        }
    }

    // =========================Deactivated Students View & Deactivate Student End==========================
    // =========================Deleted Students View & Delete Student Start================================
    public function deletedStudentView()
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            // dd($roleName);
            // dd($userSchool);

            // ----------------------------------
            // Base query
            // ----------------------------------
            $query = StudentDeleteTrackModel::query()
                ->with([
                    'studentInfo:student_code,studentname,dob,guardian_name,cur_roll_number',
                    'schoolInfo:id,school_name'
                ])
                ->whereIn('status', [1,2,3]);

            // ----------------------------------
            // ROLE BASED FILTERINGKJ
            // ----------------------------------

            // SCHOOL USER (HOI / School Admin)
            if (in_array($roleName, ['HOI Primary'])) {
                $query->where('school_code_fk', $school_id);

                // CIRCLE OFFICER
            } elseif ($roleName === 'SI') { // keep typo if role name exists like this

                $query->where('district_code_fk', $district_id)
                    ->where('circle_code_fk', $circle_id);

                // DISTRICT OFFICER
            } elseif ($roleName === 'District Officer') {

                $query->where('district_code_fk', $district_id);
            }

            // ----------------------------------
            // Execute
            // ----------------------------------
            $deleted_students = $query->get();

            return view(
                'src.modules.student_delete_deactivate.student_delete',
                compact('deleted_students','user')
            );
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred',
                false   => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteStudent(Request $request)
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            // ---------------------------------
            // 1. Validate input
            // ---------------------------------
            $data = $request->validate([
                'student_code'          => 'required|string|size:14',
                'delete_reason_code_fk' => 'nullable|integer|exists:bs_student_delete_reason,id',
                'status'  => 'nullable|integer|in:1,2,3',
            ]);
            DB::beginTransaction();

            // ---------------------------------
            // 2. Fetch student (ROLE BASED)
            // ---------------------------------
            $studentQuery = DB::table('bs_student_master')
                ->where('student_code', $data['student_code'])
                ->lockForUpdate();

            if ($roleName === 'HOI Primary') {
                $studentQuery->where('school_code_fk', $school_id);
            } elseif ($roleName === 'SI') {
                    $studentQuery->where('district_code_fk', $district_id)
                    ->where('circle_code_fk', $circle_id);
            }

            $student = $studentQuery->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Student not found or access denied',
                ], 404);
            }

            // =================================================
            // CASE 1️⃣ : HOI → SEND DELETE REQUEST TO SI
            // =================================================
            if ($roleName === 'HOI Primary') {

                DB::table('bs_student_master')
                    ->where('student_code', $student->student_code)
                    ->update([
                        'status'      => 2, // Sent to SI
                        'updated_at'  => now(),
                        'update_ip'   => $request->ip(),
                        'updated_by'  => $user->id,
                    ]);

                StudentDeleteTrackModel::create([
                    'district_code_fk'      => $student->district_code_fk,
                    'circle_code_fk'        => $student->circle_code_fk,
                    'school_code_fk'        => $student->school_code_fk,
                    'student_code'          => $student->student_code,
                    'student_name'          => $student->studentname,
                    'delete_reason_code_fk' => $data['delete_reason_code_fk'],
                    'prev_status'    => $student->status,
                    'status'  => 1, // Sent to SI
                    'entry_ip'              => $request->ip(),
                    'enter_by'              => $user->id,
                    'enter_by_stake_cd'     => $user->stake_cd ?? null,
                    'created_at'            => now(),
                ]);

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Delete request sent to SI',
                ]);
            }

            // =================================================
            // CASE 2️⃣ : SI REJECTS DELETE
            // =================================================
            if ($roleName === 'SI' && $data['status'] == 3) {

                DB::table('bs_student_master')
                    ->where('student_code', $student->student_code)
                    ->update([
                        'status'      => 1, // Active
                        'updated_at'  => now(),
                        'update_ip'   => $request->ip(),
                        'updated_by'  => $user->id,
                    ]);

               
            StudentDeleteTrackModel::where('student_code', $student->student_code)
                ->where('district_code_fk', $student->district_code_fk)
                ->update([
                    
                    'prev_delete_status'           => $student->status,
                    'status'                => $data['status'], //3 Rejected

                    'entry_ip'              => $request->ip(),
                    'enter_by'              => $user->id,
                    'enter_by_stake_cd'     => $user->stake_cd ?? null,

                    'updated_at'            => now(), // 
                ]);


                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Delete request rejected and student restored',
                ]);
            }

            // =================================================
            // CASE 3️⃣ : SI APPROVES DELETE
            // =================================================
            if ($roleName === 'SI' && $data['status'] == 2) {

                // -------- ARCHIVE
                DB::statement("
                INSERT INTO bs_student_delete_archive (
                    district_code_fk,
                    subdivision_code_fk,
                    circle_code_fk,
                    gs_ward_code_fk,
                    academic_year,
                    student_code,
                    studentname,
                    studentname_as_per_aadhaar,
                    school_code_fk,
                    gender_code_fk,
                    dob,
                    fathername,
                    mothername,
                    guardian_name,
                    aadhaar_number,
                    mothertonge_code_fk,
                    social_category_code_fk,
                    religion_code_fk,
                    bpl_y_n,
                    bpl_no,
                    bpl_aay_beneficiary_y_n,
                    disadvantaged_group_y_n,
                    cwsn_y_n,
                    cwsn_disability_type_code_fk,
                    disability_percentage,
                    nationality_code_fk,
                    out_of_sch_child_y_n,
                    child_mainstreamed,
                    blood_group_code_fk,
                    birth_registration_number,
                    identification_mark,
                    health_id,
                    stu_guardian_relationship,
                    guardian_family_income,
                    stu_height_in_cms,
                    stu_weight_in_kgs,
                    guardian_qualification,
                    stu_country_code_fk,
                    stu_contact_address,
                    stu_contact_district,
                    stu_contact_panchayat,
                    stu_police_station,
                    stu_mobile_no,
                    stu_state_code_fk,
                    stu_contact_habitation,
                    stu_contact_block,
                    stu_post_office,
                    stu_pin_code,
                    stu_email,
                    address_equal,
                    guardian_country_code_fk,
                    guardian_state_code_fk,
                    guardian_contact_address,
                    guardian_contact_habitation,
                    guardian_contact_district,
                    guardian_contact_block,
                    guardian_contact_panchayat,
                    guardian_post_office,
                    guardian_police_station,
                    guardian_pin_code,
                    guardian_mobile_no,
                    guardian_email,
                    admission_no,
                    admission_date,
                    status_pre_year,
                    prev_class_appeared_exam,
                    prev_class_exam_result,
                    prev_class_marks_percent,
                    attendention_pre_year,
                    pre_roll_number,
                    pre_class_code_fk,
                    pre_section_code_fk,
                    pre_stream_code_fk,
                    cur_class_code_fk,
                    cur_section_code_fk,
                    cur_stream_code_fk,
                    cur_roll_number,
                    medium_code_fk,
                    admission_type_code_fk,
                    bank_ifsc,
                    stu_bank_acc_no,
                    status,
                    entry_ip,
                    update_ip,
                    created_by,
                    updated_by,
                    deleted_at,
                    created_at,
                    updated_at
                )
                SELECT
                    district_code_fk,
                    subdivision_code_fk,
                    circle_code_fk,
                    gs_ward_code_fk,
                    academic_year,
                    student_code,
                    studentname,
                    studentname_as_per_aadhaar,
                    school_code_fk,
                    gender_code_fk,
                    dob,
                    fathername,
                    mothername,
                    guardian_name,
                    aadhaar_number,
                    mothertonge_code_fk,
                    social_category_code_fk,
                    religion_code_fk,
                    bpl_y_n,
                    bpl_no,
                    bpl_aay_beneficiary_y_n,
                    disadvantaged_group_y_n,
                    cwsn_y_n,
                    cwsn_disability_type_code_fk,
                    disability_percentage,
                    nationality_code_fk,
                    out_of_sch_child_y_n,
                    child_mainstreamed,
                    blood_group_code_fk,
                    birth_registration_number,
                    identification_mark,
                    health_id,
                    stu_guardian_relationship,
                    guardian_family_income,
                    stu_height_in_cms,
                    stu_weight_in_kgs,
                    guardian_qualification,
                    stu_country_code_fk,
                    stu_contact_address,
                    stu_contact_district,
                    stu_contact_panchayat,
                    stu_police_station,
                    stu_mobile_no,
                    stu_state_code_fk,
                    stu_contact_habitation,
                    stu_contact_block,
                    stu_post_office,
                    stu_pin_code,
                    stu_email,
                    address_equal,
                    guardian_country_code_fk,
                    guardian_state_code_fk,
                    guardian_contact_address,
                    guardian_contact_habitation,
                    guardian_contact_district,
                    guardian_contact_block,
                    guardian_contact_panchayat,
                    guardian_post_office,
                    guardian_police_station,
                    guardian_pin_code,
                    guardian_mobile_no,
                    guardian_email,
                    admission_no,
                    admission_date,
                    status_pre_year,
                    prev_class_appeared_exam,
                    prev_class_exam_result,
                    prev_class_marks_percent,
                    attendention_pre_year,
                    pre_roll_number,
                    pre_class_code_fk,
                    pre_section_code_fk,
                    pre_stream_code_fk,
                    cur_class_code_fk,
                    cur_section_code_fk,
                    cur_stream_code_fk,
                    cur_roll_number,
                    medium_code_fk,
                    admission_type_code_fk,
                    bank_ifsc,
                    stu_bank_acc_no,
                    status,
                    entry_ip,
                    update_ip,
                    ?,              -- created_by
                    ?,              -- updated_by
                    NOW(),          -- deleted_at (FIXED)
                    created_at,
                    updated_at
                FROM bs_student_master
                WHERE student_code = ?
                AND district_code_fk = ?
            ", [
                    Auth::id(),
                    Auth::id(),
                    $student->student_code,
                    $student->district_code_fk,
                ]);

                // -------- TRACK

            StudentDeleteTrackModel::where('student_code', $student->student_code)
                ->where('district_code_fk', $student->district_code_fk)
                ->update([
                    
                    'prev_delete_status'           => $student->status,
                    'status'                => $data['status'], //2  Rejected

                    'entry_ip'              => $request->ip(),
                    'enter_by'              => $user->id,
                    'enter_by_stake_cd'     => $user->stake_cd ?? null,

                    'updated_at'            => now(), // 
                ]);
                // -------- HARD DELETE
                DB::table('bs_student_master')
                    ->where('student_code', $student->student_code)
                    ->delete();

                DB::commit();

                return response()->json([
                    'status'  => true,
                    'message' => 'Student permanently deleted',
                ]);
            }

            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Invalid operation',
            ], 400);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                false   => $e->getMessage(),
            ], 500);
        }
    }
    public function activateStudent(Request $request)
    {
        try {
            $user = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            $data = $request->validate([
                'student_code'          => 'required|string|size:14'
            ]);
            $studentCode = $data['student_code'];

            DB::beginTransaction();

            /* -------------------------------------------------
         * 1. Search student where status = 'D'
         * ------------------------------------------------- */
            $deletedStudent = DB::table('bs_student_master')
                ->where('student_code', $studentCode)
                ->where('district_code_fk', $district_id)
                ->where('status', 3)
                ->first();

            if (!$deletedStudent) {
                return response()->json([
                    'status' => false,
                    'message' => 'Deactivated student not found'
                ], 404);
            }

            /* -------------------------------------------------
         * 2. Check if already exists with status = 'N'
         * ------------------------------------------------- */
            $activeExists = DB::table('bs_student_master')
                ->where('student_code', $studentCode)
                ->where('district_code_fk', $district_id)
                ->where('status', 1)
                ->exists();

            if ($activeExists) {
                return response()->json([
                    'status' => false,
                    'message' => 'Student already exists as active'
                ], 409);
            }

            /* -------------------------------------------------
         * 3. Check activate/deactivate track
         * ------------------------------------------------- */
            $track = DB::table('bs_student_activate_deactivate_track')
                ->where('student_code', $studentCode)
                ->where('district_code_fk', $district_id)
                ->whereIn('status', [1, 2])
                ->first();

            if ($track && $track->status == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Student already activated'
                ], 409);
            }

            /* -------------------------------------------------
         * 4. Check promotion status
         * ------------------------------------------------- */
            // if (!DB::table('bs_student_promotion_status')
            //     ->where('student_code', $studentCode)
            //     ->exists()) {

            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Promotion status record not found'
            //     ], 404);
            // }

            /* -------------------------------------------------
         * 5. Check promotion sent-up status
         * ------------------------------------------------- */
            // if (!DB::table('bs_student_sentup_status')
            //     ->where('student_code', $studentCode)
            //     ->exists()) {

            //     return response()->json([
            //         'status' => false,
            //         'message' => 'Promotion sent-up status not found'
            //     ], 404);
            // }

            /* -------------------------------------------------
         * 6. Get student details from district API track
         * ------------------------------------------------- */
            // $district = $deletedStudent->district_code_fk;
            // $apiTable = "bs_student_api_track_{$district}";

            // $apiStudent = DB::table($apiTable)
            //     ->where('student_code', $studentCode)
            //     ->first();

            // if ($apiStudent) {
            //     /* -------------------------------------------------
            //     * 11. Delete record from API track
            //     * ------------------------------------------------- */
            //         DB::table($apiTable)
            //         ->where('student_code', $studentCode)
            //         ->delete();
            // }
            // else{
            //     /* -------------------------------------------------
            //     * 12. Insert new record into API track
            //     * ------------------------------------------------- */
            //         DB::table($apiTable)->insert([
            //             (array) $apiStudent
            //         ]);
            // }

            /* -------------------------------------------------
         * 7. Update bs_student_master → status = N
         * ------------------------------------------------- */
            DB::table('bs_student_master')
                ->where('student_code', $studentCode)
                ->where('district_code_fk', $district_id)
                ->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);

            /* -------------------------------------------------
         * 8. Update bs_student_history
         * ------------------------------------------------- */
            DB::table('bs_student_history')
                ->where('student_code', $studentCode)
                ->where('district_code_fk', $district_id)
                ->update([
                    'status' => 1,
                    'updated_at' => now()
                ]);

            /* -------------------------------------------------
         * 9. Update or insert activate/deactivate track
         * ------------------------------------------------- */
            DB::table('bs_student_activate_deactivate_track')
                ->updateOrInsert(
                    ['student_code' => $studentCode],
                    [
                        'status' => 1,
                        'status' => 1,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );

            /* -------------------------------------------------
         * 10. Update promotion status → status = O
         * ------------------------------------------------- */
            // DB::table('bs_student_promotion_status')
            //     ->where('student_code', $studentCode)
            //     ->update([
            //         'status' => 1,
            //         'updated_at' => now()
            //     ]);



            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Student activated successfully'
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Activation failed',
                false => $e->getMessage()
            ], 500);
        }
    }




    // =========================Deleted Students View & Delete Student Ens==================================
}
