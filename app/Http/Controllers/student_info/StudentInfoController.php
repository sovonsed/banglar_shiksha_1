<?php

namespace App\Http\Controllers\student_info;

use Exception;
use App\Models\BlockMaster;
use App\Models\CircleMaster;
use App\Models\SchoolMaster;
use Illuminate\Http\Request;
use App\Models\StudentMaster;
use App\Models\DistrictMaster;
use App\Models\ManagementMaster;
use App\Models\SubdivisionMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\student_info\BankList;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use App\Models\student_info\BranchList;
use App\Models\student_info\StudentInfo;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreEnrollmentRequest;
use Illuminate\Validation\ValidationException;
use App\Models\student_info\StudentContactInfo;
use App\Models\student_info\StudentEntryMaster;
use App\Models\student_info\EntryStudentBankInfo;
use App\Models\student_info\StudentAdditionalInfo;
use App\Models\student_info\StudentEnrollmentInfo;
use App\Http\Requests\StoreUserRequestStudentEntry;
use App\Models\student_info\StudentBankDetailsTemp;
use App\Models\student_info\StudentEntryDraftTracker;
use App\Models\student_info\StudentVocationalDetails;
use App\Http\Requests\StoreUserRequestStudentContactInfo;
use App\Models\student_info\StudentFacilityAndOtherDetails;
use App\Models\{CategoryMaster, GenderMaster, ClassMaster};
use App\Http\Requests\StoreUserRequestStudentVocationalDetails;
use App\Http\Requests\StoreUserRequestStudentFacilityAndOtherDetails;

class StudentInfoController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view students', ['only' => ['studentList']]);
    }

    // 1 . ======================Store Student Basic Details============================
    public function StoreStudentEntryStoreBasicDetails(StoreUserRequestStudentEntry $request)
    {
        // dd(request()->all());
        try {
            DB::beginTransaction();
            $userId = Auth::user()->id ?? 1;

            $inputMeta = [
                'school_id_fk' => 1,
                // 'entry_ip'     => request()->ip(),
                // 'update_ip'    => request()->ip(),
                'created_by'   => $userId,
                'updated_by'   => $userId,
            ];

            //   ['school_id_fk' => $inputMeta['school_id_fk']];

            $studentAttrs = [
                'studentname'                          => $request->student_name,
                'studentname_as_per_aadhaar'           => $request->student_name_as_per_aadhaar,
                'gender_code_fk'                       => $request->gender,
                'dob'                                  => $request->dob,
                'fathername'                           => $request->father_name,
                'mothername'                           => $request->mother_name,
                'guardian_name'                        => $request->guardian_name,
                'aadhaar_number'                       => $request->aadhaar_child,
                'mothertonge_code_fk'                  => $request->mother_tongue,
                'social_category_code_fk'              => $request->social_category,
                'religion_code_fk'                     => $request->religion,
                'nationality_code_fk'                  => $request->nationality,
                'blood_group_code_fk'                  => $request->blood_group,
                'bpl_y_n'                              => $request->bpl_beneficiary,
                'bpl_aay_beneficiary_y_n'              => $request->antyodaya_anna_yojana,
                'bpl_no'                               => $request->bpl_number,
                'disadvantaged_group_y_n'              => $request->disadvantaged_group,
                'cwsn_y_n'                             => $request->cwsn,
                'cwsn_disability_type_code_fk'         => $request->type_of_impairment,
                'disability_percentage'                => $request->disability_percentage,
                'out_of_sch_child_y_n'                 => $request->out_of_school,
                'child_mainstreamed'                   => $request->mainstreamed,
                'birth_registration_number'            => $request->birth_reg_no,
                'identification_mark'                  => $request->identification_mark,
                'health_id'                            => $request->health_id,
                'stu_guardian_relationship'            => $request->relationship_with_guardian,
                'guardian_family_income'               => $request->family_income,
                'guardian_qualification'               => $request->guardian_qualifications,
                'stu_height_in_cms'                    => $request->student_height,
                'stu_weight_in_kgs'                    => $request->student_weight,

                // metadata
                'school_id_fk'                         => $inputMeta['school_id_fk'],
                // 'entry_ip'                             => $inputMeta['entry_ip'],
                // 'update_ip'                            => $inputMeta['update_ip'],
                'created_by'                           => $inputMeta['created_by'],
                'updated_by'                           => $inputMeta['updated_by'],
            ];




            $studentInfoData = array_merge($studentAttrs, $inputMeta);


            $basic_info_of_student = StudentInfo::updateOrCreate(
                ['school_id_fk' => $inputMeta['school_id_fk']],
                $studentInfoData
            );

            // dd($basic_info_of_student->toArray());

            if ($basic_info_of_student) {
                StudentEntryDraftTracker::updateOrCreate(
                    [
                        'school_id_fk' => $inputMeta['school_id_fk'],
                        'step_number'  => 1,
                    ],
                    [
                        'created_by' => $inputMeta['created_by'],
                        'updated_by' => $inputMeta['updated_by'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Student saved successfully',
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error saving student', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while saving student',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // 2. =======================Store Student Enrollment Data==============
    public function storeEnrollmentDetails(StoreEnrollmentRequest $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            $userId = Auth::user()->id ?? 1;

            $inputMeta = [
                'school_id_fk' => 1,
                'entry_ip'     => request()->ip(),
                'update_ip'    => request()->ip(),
                'created_by'   => $userId,
                'updated_by'   => $userId,
            ];
            // ['school_id_fk' => $inputMeta['school_id_fk']];
            $enrollAttrs = [

                'admission_no'              => $request->admission_number,
                'status_pre_year'           => $request->admission_status_prev,
                'prev_class_appeared_exam'  => $request->prev_class_appeared_exam,
                'prev_class_exam_result'    => $request->previous_class_result_examination,
                'prev_class_marks_percent'  => $request->percentage_of_overall_marks,
                'attendention_pre_year'     => $request->no_of_days_attended,

                'pre_class_code_fk'         => $request->previous_class,
                'pre_section_code_fk'       => $request->class_section,
                'pre_stream_code_fk'        => $request->student_stream,
                'pre_roll_number'           => $request->previous_student_roll_no,

                'cur_class_code_fk'         => $request->present_class,
                'academic_year'             => $request->accademic_year,
                'cur_section_code_fk'       => $request->present_section,
                'medium_code_fk'            => $request->school_medium,
                'cur_roll_number'           => $request->present_roll_no,
                'admission_date'            => $request->admission_date_present,

                'admission_type_code_fk'    => $request->admission_type,
                'cur_stream_code_fk' => $request->cur_stream_code,

                // meta
                'school_id_fk'              => $inputMeta['school_id_fk'],
                // 'entry_ip'                  => $inputMeta['entry_ip'],
                // 'update_ip'                 => $inputMeta['update_ip'],
                // 'created_by'                => $inputMeta['created_by'],
                // 'updated_by'                => $inputMeta['updated_by'],
            ];


            $studentEnrollmentInfoData = array_merge($enrollAttrs, $inputMeta);
            // dd($studentEnrollmentInfoData);

            $enroll = StudentEnrollmentInfo::updateOrCreate(
                ['school_id_fk' => $inputMeta['school_id_fk']],
                $studentEnrollmentInfoData
            );


            if ($enroll) {
                StudentEntryDraftTracker::updateOrCreate(
                    [
                        'school_id_fk' => $inputMeta['school_id_fk'],
                        'step_number'  => 2
                    ],
                    [
                        'created_by' => Auth::user()->id ?? 1,
                        'updated_by' => Auth::user()->id ?? 1
                    ]
                );
            }
            DB::commit();
            return response()->json([
                'success'       => true,
                'message'       => 'Enrollment saved successfully',
                // 'enrollment_id' => $enroll->id,
            ], 201);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();

            Log::error('SQL error saving enrollment', [
                'error'   => $ex->getMessage(),
                'trace'   => $ex->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Database error while saving enrollment',
                'error'   => $ex->getMessage(),
            ], 500);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error saving enrollment', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while saving enrollment',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // 3. ===============================Student Facility and Others Details==============================
    public function storeStudentFacilityAndOtherDetails(StoreUserRequestStudentFacilityAndOtherDetails $request)
    {
        try {
            // ----------------------------------------------
            // Pre-set system fields
            // ----------------------------------------------
            $input = [
                'school_id_fk'       => 1,
                'district_code_fk'   => 4,
                'subdivision_code_fk' => 45,
                'block_munc_code_fk' => 329,
                'circle_code_fk'     => 115,
                'gs_ward_code_fk'    => 11026,
                'entry_ip'           => request()->ip(),
                'update_ip'          => request()->ip(),
                'created_by'         => Auth::user()->id ?? 1,
                'updated_by'         => Auth::user()->id ?? 1,
            ];

            // ----------------------------------------------
            // Validated form fields from Form Request
            // ----------------------------------------------
            $validated = $request->validated();
            $saveData = [
                // Facilities
                'facilities_provided_y_n'              => $validated['facilities_provided_for_the_yeear'],
                'free_uniform_y_n'                     => $validated['free_uniforms'],
                'free_transport_facility_y_n'          => $validated['free_transport_facility'],
                'free_escort_y_n'                      => $validated['free_escort'],
                'free_hostel_y_n'                      => $validated['free_host_facility'],
                'free_cycle_y_n'                       => $validated['free_bicycle'],
                'free_shoe_y_n'                        => $validated['free_shoe'],
                'free_exercise_book_y_n'               => $validated['free_exercise_book'],
                'complete_set_of_free_books_y_n'       => $validated['complete_free_books'],

                // Scholarships
                'central_scholarship_rcv_y_n'          => $validated['central_scholarship'],
                'central_scholarship_code_fk'          => $validated['central_scholarship_name'] ?? null,
                'central_scholarship_amount'           => $validated['central_scholarship_amount'] ?? null,

                'state_scholarship_rcv_y_n'            => $validated['state_scholarship'],
                'state_scholarship_code_fk'            => $validated['state_scholarship_name'] ?? null,
                'state_scholarship_amount'             => $validated['state_scholarship_amount'] ?? null,

                'other_scholarship_rcv_y_n'            => $validated['other_scholarship'],
                'other_scholarship_amount'             => $validated['other_scholarship_amount'] ?? null,

                // Extracurricular
                'screened_for_attention_deficit_hyperactive_disorder_y_n' => $validated['child_hyperactive_disorder'] ?? null,
                'extracurricular_activity_involved_y_n' => $validated['stu_extracurricular_activity'],
                'gifted_talented_child_in_mathematics' => $validated['gifted_math'] ?? null,
                'gifted_talented_child_in_language'    => $validated['gifted_language'] ?? null,
                'gifted_talented_child_in_science'     => $validated['gifted_science'] ?? null,
                'gifted_talented_child_in_technical'   => $validated['gifted_technical'] ?? null,
                'gifted_talented_child_in_sports'      => $validated['gifted_sports'] ?? null,
                'gifted_talented_child_in_art'         => $validated['gifted_art'] ?? null,

                // Other Details
                'provided_mentors_y_n'                 => $validated['provided_mentors'],
                'participated_in_nurturance_camps_y_n' => $validated['whether_participated_nurturance_camp'],
                'state_level_y_n'                      => $validated['state_nurturance'] ?? null,
                'national_level_y_n'                   => $validated['national_nurturance'] ?? null,

                'appeared_state_olympiads_national_level_competition_y_n'
                => $validated['participated_competitions'],
                'participate_in_ncc_nss_scouts_guides_y_n'
                => $validated['ncc_nss_guides'],
                'free_education_as_per_rte_act_y_n'    => $validated['rte_free_education'],

                'child_homeless'                       => $validated['homeless'],
                'special_training_facility_y_n'        => $validated['special_training'],

                // Digital
                'digital_device_inc_internet_yn'       => $validated['able_to_handle_devices'],
                'digital_device_inc_internet_fk'       => $validated['internet_access'],
            ];
            // ----------------------------------------------
            // Merge additional fields
            // ----------------------------------------------
            $data = array_merge($saveData, $input);

            // ----------------------------------------------
            // Save data
            // ----------------------------------------------
            $facility = StudentFacilityAndOtherDetails::updateOrCreate(
                ['school_id_fk' => $input['school_id_fk']],
                $data  // or $data based on your variable
            );

            // ONLY IF the above is successful
            if ($facility) {

                // -------------------------------
                // 2) Update draft tracker table
                // -------------------------------
                StudentEntryDraftTracker::updateOrCreate(
                    [
                        'school_id_fk' => $input['school_id_fk'],    // match school
                        'step_number'  => 3
                    ],
                    [
                        'created_by' => Auth::user()->id ?? 1,
                        'updated_by' => Auth::user()->id ?? 1
                    ]
                );
            }

            return response()->json([
                'status'  => true,
                'message' => 'Student facilities & other details saved successfully!',
            ], 200);
        } catch (Exception $ex) {
            return response()->json([
                'status' => false,
                'message' => $ex->getMessage()
            ], 500);
        }
    }

    // 4. ==========================Store Student Vocational Data=============================
    public function saveVocationalDetails(StoreUserRequestStudentVocationalDetails $request)
    {
        try {
            $data = $request->validated();
            $authUserId = Auth::user()->id ?? 1;

            $save = StudentVocationalDetails::updateOrCreate(
                [
                    'school_id_fk'        => 1,
                    'district_code_fk'    => 4,
                    'subdivision_code_fk' => 45,
                    'block_munc_code_fk'  => 329,
                    'circle_code_fk'      => 115,
                    'gs_ward_code_fk'     => 11026,

                    // YES / NO FLAGS
                    'exposure_vocational_activities_y_n'  => $data['exposure_vocational_activities_y_n'] ?? null,
                    'undertake_vocational_course_y_n'     => $data['undertook_vocational_course'],

                    // FK VALUES
                    'vocational_course_code_fk'           => $data['vocational_course_code_fk'] ?? null,
                    'vocational_trade_sector_code_fk'     => $data['trade_sector'] ?? null,
                    'vocational_job_role_code_fk'         => $data['job_role'] ?? null,

                    // CLASS HOURS
                    'vocational_class_attended_theory'            => $data['theory_hours'] ?? null,
                    'vocational_class_attended_practical'         => $data['practical_hours'] ?? null,
                    'vocational_class_attended_industry_training' => $data['industry_hours'] ?? null,
                    'vocational_class_attended_field_visit'       => $data['field_visit_hours'] ?? null,

                    // EXAM DETAILS
                    'prev_class_exam_appeared_fk'  => $data['appeared_exam'] ?? null,
                    'prev_class_marks_percent_voc' => $data['marks_obtained'] ?? null,

                    // PLACEMENT / APPRENTICESHIP
                    'applied_for_placement_code_fk'      => $data['placement_applied'] ?? null,
                    'applied_for_apprenticeship_code_fk' => $data['apprenticeship_applied'] ?? null,

                    // NSQF / EMPLOYMENT
                    'vocational_nsqf_level_code_fk'      => $data['nsqf_level'] ?? null,
                    'vocational_placement_status_code_fk' => $data['employment_status'] ?? null,
                    'vocational_salary_offered'          => $data['salary_offered'] ?? null,

                    // SYSTEM FIELDS
                    'entry_ip'            => request()->ip(),
                    'update_ip'           => request()->ip(),
                    'created_by'          => $authUserId ?? 1,
                    'updated_by'          => $authUserId ?? 1,
                ]
            );
            // ONLY IF the above is successful
            if ($save) {

                // -------------------------------
                // 2) Update draft tracker table
                // -------------------------------
                StudentEntryDraftTracker::updateOrCreate(
                    [
                        'school_id_fk' => 1,    // match school
                        'step_number'  => 4
                    ],
                    [
                        'created_by' => Auth::user()->id ?? 1,
                        'updated_by' => Auth::user()->id ?? 1
                    ]
                );
            }
            return response()->json([
                'status'  => true,
                'message' => 'Vocational details saved successfully!',
                'data'    => $save
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'status'  => false,
                'message' => 'Server error while saving vocational details',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // 5. ===========================Store Student Contact Details============================
    public function storeStudentContactDetails(StoreUserRequestStudentContactInfo $request)
    {
        DB::beginTransaction();
        // dd(request()->all());

        $authUserId = Auth::user()->id ?? 1;
        try {
            $userId = Auth::user()->id ?? 1;

            $inputMeta = [
                'school_id_fk' => 1,
                'entry_ip'            => request()->ip(),
                'update_ip'           => request()->ip(),
                'created_by'          => $authUserId ?? 1,
                'updated_by'          => $authUserId ?? 1,

            ];

            $data = [
                // ---- Student contact fields ----
                'stu_country_code_fk'     => $request->student_country,
                'stu_contact_address'     => $request->student_address,
                'stu_contact_district'    => $request->student_district,
                'stu_contact_panchayat'   => $request->student_panchayat,
                'stu_police_station'      => $request->student_police_station,
                'stu_mobile_no'           => $request->student_mobile,
                'stu_state_code_fk'       => $request->student_state,
                'stu_contact_habitation'  => $request->student_locality,
                'stu_contact_block'       => $request->student_block,
                'stu_post_office'         => $request->student_post_office,
                'stu_pin_code'            => $request->student_pincode,
                'stu_email'               => $request->student_email,

                // ---- Guardian contact fields ----
                'guardian_country_code_fk'    => $request->guardian_country,
                'guardian_contact_address'    => $request->guardian_address,
                'guardian_contact_district'   => $request->guardian_district,
                'guardian_contact_panchayat'  => $request->guardian_panchayat,
                'guardian_police_station'     => $request->guardian_police_station,
                'guardian_mobile_no'          => $request->guardian_mobile,
                'guardian_state_code_fk'      => $request->guardian_state,
                'guardian_contact_habitation' => $request->guardian_locality,
                'guardian_contact_block'      => $request->guardian_block,
                'guardian_post_office'        => $request->guardian_post_office,
                'guardian_pin_code'           => $request->guardian_pincode,
                'guardian_email'              => $request->guardian_email,


                // System fields
                'school_id_fk'              => $inputMeta['school_id_fk'],
                'entry_ip'                  => $inputMeta['entry_ip'],
                'update_ip'                 => $inputMeta['update_ip'],
                'created_by'   => $inputMeta['created_by'],
                'updated_by'   => $inputMeta['updated_by'],

            ];

            $contact_info_of_student = StudentContactInfo::updateOrCreate(
                ['school_id_fk' => $inputMeta['school_id_fk']],
                $data
            );

            if ($contact_info_of_student) {
                StudentEntryDraftTracker::updateOrCreate(
                    [
                        'school_id_fk' => $inputMeta['school_id_fk'],
                        'step_number'  => 5,
                    ],
                    [
                        'created_by' => $inputMeta['created_by'],
                        'updated_by' => $inputMeta['updated_by'],
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => 'Student saved successfully',
                'contact_id' => $contact_info_of_student->id ?? null,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Error saving student', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while saving student',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // 6. ==============Store Student Bank Details======================
    public function bankDetailsOfStudent(Request $request)
    {
        $userId   = Auth::user()->id ?? 1;
        $schoolId = $request->input('school_id_fk', 1);
        // or use auth()->user()->school_id_fk depending on your flow

        $validated = $request->validate([
            'bank_name'              => 'required|integer|exists:bs_bank_code_name_master,id',
            'branch_name'            => 'required|integer|exists:bs_bank_branch_master,id',
            'ifsc'                   => 'required|string|max:20',
            'account_number'         => 'required|string|max:50',
            'confirm_account_number' => 'required|same:account_number',
        ]);

        // -----------------------------
        // Prepare data for updateOrCreate
        // -----------------------------

        $data = [
            'bank_id_fk'      => $validated['bank_name'],
            'branch_id_fk'    => $validated['branch_name'],
            'bank_ifsc'       => $validated['ifsc'],
            'stu_bank_acc_no' => $validated['account_number'],

            'status'          => 1,
            'entry_ip'        => $request->ip(),
            'update_ip'       => $request->ip(),
            'created_by'      => $userId,
            'updated_by'      => $userId,
        ];

        // -----------------------------
        // CREATE or UPDATE (By school_id_fk)
        // -----------------------------

        $bank_info = EntryStudentBankInfo::updateOrCreate(
            ['school_id_fk' => $schoolId],  // MATCH condition
            $data                               // INSERT or UPDATE values
        );

        // -----------------------------
        // Update Step Tracker
        // -----------------------------
        // $this->finalizeStudentEntry($schoolId, $userId);
        StudentEntryDraftTracker::updateOrCreate(
            [
                'school_id_fk' => $schoolId,
                'step_number'  => 6,
            ],
            [
                'created_by' => $userId,
                'updated_by' => $userId,
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Bank details saved successfully.',
            'data'    => $bank_info
        ]);
    }

    // 7.=============================Additional Details ========================================

    public function StudentAdditionalDetails(Request $request)
    {
        $validated = $request->validate([
            'rte_entitlement_claimed_amount' => 'required|integer',
            'stu_residance_sch_distance_code_fk' => 'required|integer',
            'cur_class_appeared_exam' => 'required|integer',
            'cur_class_marks_percent' => 'required|numeric|min:0|max:100',
            'attendention_cur_year' => 'required|integer|min:0|max:100',
            'status' => 'nullable|integer',
        ]);


        $schoolId   = 1;   // STATIC
        $districtId = 10;  // STATIC

        $validated['school_id_fk'] = $schoolId;
        $validated['district_code_fk'] = $districtId;
        $validated['disabality_certificate_y_n'] = 0;


        $validated['update_ip']  = $request->ip();
        $validated['updated_by'] = 1;


        $data = StudentAdditionalInfo::updateOrCreate(
            ['school_id_fk' => $schoolId],
            array_merge($validated, [
                'entry_ip'   => $request->ip(),
                'created_by' => 1,
            ])
        );

        StudentEntryDraftTracker::updateOrCreate(
            [
                'school_id_fk' => $schoolId,
                'step_number'  => 7,
            ],
            [
                'created_by' => 1,
                'updated_by' => 1,
            ]
        );

        return response()->json([
            'message' => 'Student addon data saved successfully',
            'data' => $data
        ], 200);
    }

    // =======================Fetch Student Data===========================
    public function getStudentEntry()
    {
        try {
            $data = [];
            $schoolId = 1;  // <-- get from login or session or URL

            // ---------------------------------------------
            // 1. Load master data (Dropdowns)
            // ---------------------------------------------
            $draft = DB::table('bs_student_entry_draft_tracker')
                ->where('status', 1)
                ->where('school_id_fk', $schoolId)
                ->orderByDesc('step_number')
                ->first();

            // Default step = 1 if no record found
            $data['current_step'] = $draft ? $draft->step_number : 0;
            $data['stateScholarships'] = DB::table('bs_name_and_code_of_state_scholarships_master')
                ->where('status', 1)
                ->orderBy('id')
                ->get();

            $data['centralScholarships'] = DB::table('bs_name_and_code_of_central_scholarships_master')
                ->where('status', 1)
                ->orderBy('id')
                ->get();




            // 1.============================StudentInfo=======================================
            $student_basic_info = StudentInfo::where('school_id_fk', $schoolId)->first();
            // dd($student_basic_info);

            if ($student_basic_info) {
                $data['basic_info'] = [
                    'student_name'                => $student_basic_info->studentname,
                    'student_name_as_per_aadhaar' => $student_basic_info->studentname_as_per_aadhaar,
                    'gender'                      => $student_basic_info->gender_code_fk,
                    'dob'                         => $student_basic_info->dob,
                    'father_name'                 => $student_basic_info->fathername,
                    'mother_name'                 => $student_basic_info->mothername,
                    'guardian_name'               => $student_basic_info->guardian_name,
                    'aadhaar_child'               => $student_basic_info->aadhaar_number,
                    'mother_tongue'               => $student_basic_info->mothertonge_code_fk,
                    'social_category'             => $student_basic_info->social_category_code_fk,
                    'religion'                    => $student_basic_info->religion_code_fk,
                    'nationality'                 => $student_basic_info->nationality_code_fk,
                    'blood_group'                 => $student_basic_info->blood_group_code_fk,
                    'bpl_beneficiary'             => $student_basic_info->bpl_y_n,
                    'antyodaya_anna_yojana'       => $student_basic_info->bpl_aay_beneficiary_y_n,
                    'bpl_number'                  => $student_basic_info->bpl_no,
                    'disadvantaged_group'         => $student_basic_info->disadvantaged_group_y_n,
                    'cwsn'                        => $student_basic_info->cwsn_y_n,
                    'type_of_impairment'          => $student_basic_info->cwsn_disability_type_code_fk,
                    'disability_percentage'       => $student_basic_info->disability_percentage,
                    'out_of_school'               => $student_basic_info->out_of_sch_child_y_n,
                    'mainstreamed'                => $student_basic_info->child_mainstreamed,
                    'birth_reg_no'                => $student_basic_info->birth_registration_number,
                    'identification_mark'         => $student_basic_info->identification_mark,
                    'health_id'                   => $student_basic_info->health_id,
                    'relationship_with_guardian'  => $student_basic_info->stu_guardian_relationship,
                    'family_income'               => $student_basic_info->guardian_family_income,
                    'guardian_qualifications'     => $student_basic_info->guardian_qualification,
                    'student_height'              => $student_basic_info->stu_height_in_cms,
                    'student_weight'              => $student_basic_info->stu_weight_in_kgs,
                ];
            }
            // 2 . ====================Enrollment========================================

            $student_enrollment_info = StudentEnrollmentInfo::where('school_id_fk', $schoolId)->first();
            // dd($student_enrollment_info);
            if ($student_enrollment_info) {

                $data['enrollment_info'] = [
                    'admission_no'              => $student_enrollment_info->admission_no,
                    'status_pre_year'           => $student_enrollment_info->status_pre_year,
                    'prev_class_appeared_exam'  => $student_enrollment_info->prev_class_appeared_exam,
                    'prev_class_exam_result'    => $student_enrollment_info->prev_class_exam_result,
                    'prev_class_marks_percent'  => $student_enrollment_info->prev_class_marks_percent,
                    'attendention_pre_year'     => $student_enrollment_info->attendention_pre_year,

                    'pre_class_code_fk'         => $student_enrollment_info->pre_class_code_fk,
                    'pre_section_code_fk'       => $student_enrollment_info->pre_section_code_fk,
                    'pre_stream_code_fk'        => $student_enrollment_info->pre_stream_code_fk,
                    'pre_roll_number'           => $student_enrollment_info->pre_roll_number,

                    'cur_class_code_fk'         => $student_enrollment_info->cur_class_code_fk,
                    'academic_year'             => $student_enrollment_info->academic_year,
                    'cur_section_code_fk'       => $student_enrollment_info->cur_section_code_fk,
                    'medium_code_fk'            => $student_enrollment_info->medium_code_fk,
                    'cur_roll_number'           => $student_enrollment_info->cur_roll_number,
                    'admission_date'            => $student_enrollment_info->admission_date,
                    'cur_stream_code'            => $student_enrollment_info->cur_stream_code_fk,

                    'admission_type_code_fk'    => $student_enrollment_info->admission_type_code_fk,
                ];
            }

            // 3 . ==================== Facility & Others Detail========================================


            $facility = StudentFacilityAndOtherDetails::where('school_id_fk', $schoolId)->first();


            if ($facility) {

                $data['facility'] = [
                    // Facilities Provided
                    'facilities_provided_for_the_yeear' => $facility->facilities_provided_y_n,
                    'free_uniforms'                     => $facility->free_uniform_y_n,
                    'free_transport_facility'           => $facility->free_transport_facility_y_n,
                    'free_escort'                       => $facility->free_escort_y_n,
                    'free_host_facility'                => $facility->free_hostel_y_n,
                    'free_bicycle'                      => $facility->free_cycle_y_n,
                    'free_shoe'                         => $facility->free_shoe_y_n,
                    'free_exercise_book'                => $facility->free_exercise_book_y_n,
                    'complete_free_books'               => $facility->complete_set_of_free_books_y_n,

                    // Scholarships
                    'central_scholarship'               => $facility->central_scholarship_rcv_y_n,
                    'central_scholarship_name'          => $facility->central_scholarship_code_fk,
                    'central_scholarship_amount'        => $facility->central_scholarship_amount,

                    'state_scholarship'                 => $facility->state_scholarship_rcv_y_n,
                    'state_scholarship_name'            => $facility->state_scholarship_code_fk,
                    'state_scholarship_amount'          => $facility->state_scholarship_amount,

                    'other_scholarship'                 => $facility->other_scholarship_rcv_y_n,
                    'other_scholarship_amount'          => $facility->other_scholarship_amount,

                    // Gifted fields
                    'child_hyperactive_disorder'        => $facility->screened_for_attention_deficit_hyperactive_disorder_y_n,
                    'stu_extracurricular_activity'      => $facility->extracurricular_activity_involved_y_n,
                    'gifted_math'                       => $facility->gifted_talented_child_in_mathematics,
                    'gifted_language'                   => $facility->gifted_talented_child_in_language,
                    'gifted_science'                    => $facility->gifted_talented_child_in_science,
                    'gifted_technical'                  => $facility->gifted_talented_child_in_technical,
                    'gifted_sports'                     => $facility->gifted_talented_child_in_sports,
                    'gifted_art'                        => $facility->gifted_talented_child_in_art,

                    // Other details
                    'provided_mentors'                  => $facility->provided_mentors_y_n,
                    'whether_participated_nurturance_camp' => $facility->participated_in_nurturance_camps_y_n,
                    'state_nurturance'                  => $facility->state_level_y_n,
                    'national_nurturance'               => $facility->national_level_y_n,
                    'participated_competitions'         => $facility->appeared_state_olympiads_national_level_competition_y_n,
                    'ncc_nss_guides'                    => $facility->participate_in_ncc_nss_scouts_guides_y_n,
                    'rte_free_education'                => $facility->free_education_as_per_rte_act_y_n,
                    'homeless'                          => $facility->child_homeless,
                    'special_training'                  => $facility->special_training_facility_y_n,

                    // Digital
                    'able_to_handle_devices'            => $facility->digital_device_inc_internet_yn,
                    'internet_access'                   => $facility->digital_device_inc_internet_fk,
                ];
            } else {
                $data['facility'] = null;   // No saved data
            }


            // 4 . ==================== Vocational Details ========================================

            $vocational = StudentVocationalDetails::where('school_id_fk', $schoolId)->first();

            if ($vocational) {
                $data['vocational'] = [
                    'exposure'               => $vocational->exposure_vocational_activities_y_n,
                    'undertook'              => $vocational->undertake_vocational_course_y_n,

                    'trade_sector'           => $vocational->vocational_trade_sector_code_fk,
                    'job_role'               => $vocational->vocational_job_role_code_fk,

                    'theory_hours'           => $vocational->vocational_class_attended_theory,
                    'practical_hours'        => $vocational->vocational_class_attended_practical,
                    'industry_hours'         => $vocational->vocational_class_attended_industry_training,
                    'field_visit_hours'      => $vocational->vocational_class_attended_field_visit,

                    'appeared_exam'          => $vocational->prev_class_exam_appeared_fk,
                    'marks_obtained'         => $vocational->prev_class_marks_percent_voc,

                    'placement_applied'      => $vocational->applied_for_placement_code_fk,
                    'apprenticeship_applied' => $vocational->applied_for_apprenticeship_code_fk,

                    'nsqf_level'             => $vocational->vocational_nsqf_level_code_fk,
                    'employment_status'      => $vocational->vocational_placement_status_code_fk,
                    'salary_offered'         => $vocational->vocational_salary_offered,
                ];
            } else {
                $data['vocational'] = null;
            }




            // 5 . ==================== Contact Details ========================================

            $student_contact = StudentContactInfo::where('school_id_fk', $schoolId)->first();

            if ($student_contact) {
                $data['student_contact'] = [
                    'stu_country_code'     => $student_contact->stu_country_code_fk,
                    'stu_contact_address'     => $student_contact->stu_contact_address,
                    'stu_contact_district'    => $student_contact->stu_contact_district,
                    'stu_contact_panchayat'   => $student_contact->stu_contact_panchayat,
                    'stu_police_station'      => $student_contact->stu_police_station,
                    'stu_mobile_no'           => $student_contact->stu_mobile_no,
                    'stu_state_code'       => $student_contact->stu_state_code_fk,
                    'stu_contact_habitation'  => $student_contact->stu_contact_habitation,
                    'stu_contact_block'       => $student_contact->stu_contact_block,
                    'stu_post_office'         => $student_contact->stu_post_office,
                    'stu_pin_code'            => $student_contact->stu_pin_code,
                    'stu_email'               => $student_contact->stu_email,

                    // ---- Guardian contact fields ----
                    'guardian_country_code'    => $student_contact->guardian_country_code_fk,
                    'guardian_contact_address'    => $student_contact->guardian_contact_address,
                    'guardian_contact_district'   => $student_contact->guardian_contact_district,
                    'guardian_contact_panchayat'  => $student_contact->guardian_contact_panchayat,
                    'guardian_police_station'     => $student_contact->guardian_police_station,
                    'guardian_mobile_no'          => $student_contact->guardian_mobile_no,
                    'guardian_state_code'      => $student_contact->guardian_state_code_fk,
                    'guardian_contact_habitation' => $student_contact->guardian_contact_habitation,
                    'guardian_contact_block'      => $student_contact->guardian_contact_block,
                    'guardian_post_office'        => $student_contact->guardian_post_office,
                    'guardian_pin_code'           => $student_contact->guardian_pin_code,
                    'guardian_email'              => $student_contact->guardian_email,
                ];
            } else {
                $data['student_contact'] = null;
            }



            // 6 . ==================== Bank Details ========================================

            $student_bank_details = EntryStudentBankInfo::where('school_id_fk', $schoolId)->first();

            if ($student_bank_details) {
                $data['student_bank_details'] = [
                    'bank_id_fk'      => $student_bank_details->bank_id_fk,
                    'branch_id_fk'    => $student_bank_details->branch_id_fk,
                    'bank_ifsc'       => $student_bank_details->bank_ifsc,
                    'stu_bank_acc_no' => $student_bank_details->stu_bank_acc_no,

                ];
            } else {
                $data['student_bank_details'] = null;
            }



            // 7 . ==================== Addi Details ========================================

            $student_additional_details = StudentAdditionalInfo::where('school_id_fk', $schoolId)->first();

            if ($student_additional_details) {
                $data['student_additional_details'] = [
                    'rte_entitlement_claimed_amount'      => $student_additional_details->rte_entitlement_claimed_amount,
                    'stu_residance_sch_distance_code_fk'    => $student_additional_details->stu_residance_sch_distance_code_fk,
                    'cur_class_appeared_exam'       => $student_additional_details->cur_class_appeared_exam,
                    'cur_class_marks_percent' => $student_additional_details->cur_class_marks_percent,
                    'attendention_cur_year' => $student_additional_details->attendention_cur_year,
                ];
            } else {
                $data['student_additional_details'] = null;
            }


            return view('src.modules.student_entry_update.student_entry', compact('data'));
        } catch (\Exception $e) {

            Log::error('Error in getStudentEntry: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    // ======================Delete Previous Student Entry================================
    public function resetEntry()
    {
        try {
            $schoolId = 1; // Should come from session/login

            $updateData = [
                'status' => 0,
                'deleted_at' => now()
            ];

            StudentInfo::where('school_id_fk', $schoolId)
                ->update($updateData);

            StudentEnrollmentInfo::where('school_id_fk', $schoolId)
                ->update($updateData);

            StudentFacilityAndOtherDetails::where('school_id_fk', $schoolId)
                ->update($updateData);

            StudentVocationalDetails::where('school_id_fk', $schoolId)
                ->update($updateData);


            StudentContactInfo::where('school_id_fk', $schoolId)
                ->update($updateData);


            EntryStudentBankInfo::where('school_id_fk', $schoolId)
                ->update($updateData);

            StudentAdditionalInfo::where('school_id_fk', $schoolId)
                ->update($updateData);




            StudentEntryDraftTracker::where('school_id_fk', $schoolId)
                ->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Previous entry has been soft deleted and marked inactive.'
            ]);
        } catch (\Exception $e) {

            Log::error("Reset Entry Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Unable to reset entry.',
            ], 500);
        }
    }



    //  =========================Fetch Bank Branch========================
    public function getBranches(Request $request)
    {
        $bankId = $request->query('bank_id');

        if (empty($bankId)) {
            return response()->json(['branches' => []]);
        }

        $branches = \App\Models\student_info\BranchList::where('bank_id_fk', $bankId)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'branch_ifsc']); // use 'name' not 'branch_name'

        return response()->json(['branches' => $branches]);
    }

    // ======================Fetch Bank IFSC=============================
    public function getIfsc(Request $request)
    {
        $branchId = $request->query('branch_id');

        if (empty($branchId)) {
            return response()->json(['ifsc' => null]);
        }

        $branch = \App\Models\student_info\BranchList::where('id', $branchId)
            ->where('status', 1)
            ->first(['branch_ifsc']);

        return response()->json(['ifsc' => $branch ? trim($branch->branch_ifsc) : null]);
    }

    // ====================Final Student Details Submit========================================
    protected function finalizeStudentEntry(int $schoolId, int $userId)
    {

        // dd( 'Finalizing student entry for school_id_fk: ' . $userId );
        DB::transaction(function () use ($schoolId, $userId) {

            // ✅ fetch ONLY by school_id_fk
            $basic   = StudentInfo::where('school_id_fk', $schoolId)->first();
            $enroll  = StudentEnrollmentInfo::where('school_id_fk', $schoolId)->first();
            $contact = StudentContactInfo::where('school_id_fk', $schoolId)->first();
            $bank    = EntryStudentBankInfo::where('school_id_fk', $schoolId)->first();

            if (!$basic || !$enroll) {
                throw new \RuntimeException('Basic or enrollment info missing for final submission');
            }

            // You may already have these in basic; otherwise map from school
            $districtCode = $basic->district_code_fk   ?? 4;
            $subdivision  = $basic->subdivision_code_fk ?? 45;
            $circle       = $basic->circle_code_fk      ?? 115;
            $gsWard       = $basic->gs_ward_code_fk     ?? 11026;

            // use any student_code logic you have (or keep null if not used)
            $studentCode  = $basic->student_code ?? null;

            $masterData = [
                // -------- keys / location ----------
                // 'school_id_fk'        => $schoolId,
                'district_code_fk'    => 4,
                'subdivision_code_fk' => $subdivision,
                'circle_code_fk'      => $circle,
                'gs_ward_code_fk'     => $gsWard,
                'academic_year'       => $enroll->academic_year,
                'student_code'        => 1,

                'state_code_fk'      => 1,


                // -------- BASIC INFO ---------------
                'studentname'                         => $basic->studentname,
                'studentname_as_per_aadhaar'          => $basic->studentname_as_per_aadhaar,
                'gender_code_fk'                      => $basic->gender_code_fk,
                'dob'                                 => $basic->dob,
                'fathername'                          => $basic->fathername,
                'mothername'                          => $basic->mothername,
                'guardian_name'                       => $basic->guardian_name,
                'aadhaar_number'                      => $basic->aadhaar_number,
                'mothertonge_code_fk'                 => $basic->mothertonge_code_fk,
                'social_category_code_fk'             => $basic->social_category_code_fk,
                'religion_code_fk'                    => $basic->religion_code_fk,
                'bpl_y_n'                             => $basic->bpl_y_n,
                'bpl_no'                              => $basic->bpl_no,
                'bpl_aay_beneficiary_y_n'             => $basic->bpl_aay_beneficiary_y_n,
                'disadvantaged_group_y_n'             => $basic->disadvantaged_group_y_n,
                'cwsn_y_n'                            => $basic->cwsn_y_n,
                'cwsn_disability_type_code_fk'        => $basic->cwsn_disability_type_code_fk,
                'disability_percentage'               => $basic->disability_percentage,
                'nationality_code_fk'                 => $basic->nationality_code_fk,
                'out_of_sch_child_y_n'                => $basic->out_of_sch_child_y_n,
                'child_mainstreamed'                  => $basic->child_mainstreamed,
                'blood_group_code_fk'                 => $basic->blood_group_code_fk,
                'birth_registration_number'           => $basic->birth_registration_number,
                'identification_mark'                 => $basic->identification_mark,
                'health_id'                           => $basic->health_id,
                'stu_guardian_relationship'           => $basic->stu_guardian_relationship,
                'guardian_family_income'              => $basic->guardian_family_income,
                'stu_height_in_cms'                   => $basic->stu_height_in_cms,
                'stu_weight_in_kgs'                   => $basic->stu_weight_in_kgs,
                'guardian_qualification'              => $basic->guardian_qualification,

                // -------- ENROLLMENT ---------------
                'admission_no'                        => $enroll->admission_no,
                'admission_date'                      => $enroll->admission_date,
                'status_pre_year'                     => $enroll->status_pre_year,
                'prev_class_appeared_exam'            => $enroll->prev_class_appeared_exam,
                'prev_class_exam_result'              => $enroll->prev_class_exam_result,
                'prev_class_marks_percent'            => $enroll->prev_class_marks_percent,
                'attendention_pre_year'               => $enroll->attendention_pre_year,
                'pre_roll_number'                     => $enroll->pre_roll_number,
                'pre_class_code_fk'                   => $enroll->pre_class_code_fk,
                'pre_section_code_fk'                 => $enroll->pre_section_code_fk,
                'pre_stream_code_fk'                  => $enroll->pre_stream_code_fk,
                'cur_class_code_fk'                   => $enroll->cur_class_code_fk,
                'cur_section_code_fk'                 => $enroll->cur_section_code_fk,
                'cur_stream_code_fk'                  => $enroll->cur_stream_code_fk,
                'cur_roll_number'                     => $enroll->cur_roll_number,
                'medium_code_fk'                      => $enroll->medium_code_fk,
                'admission_type_code_fk'              => $enroll->admission_type_code_fk,

                // -------- CONTACT ------------------
                'stu_country_code_fk'                 => $contact->stu_country_code_fk    ?? null,
                'stu_contact_address'                 => $contact->stu_contact_address    ?? null,
                'stu_contact_district'                => $contact->stu_contact_district   ?? null,
                'stu_contact_panchayat'               => $contact->stu_contact_panchayat  ?? null,
                'stu_police_station'                  => $contact->stu_police_station     ?? null,
                'stu_mobile_no'                       => $contact->stu_mobile_no          ?? null,
                'stu_state_code_fk'                   => $contact->stu_state_code_fk      ?? null,
                'stu_contact_habitation'              => $contact->stu_contact_habitation ?? null,
                'stu_contact_block'                   => $contact->stu_contact_block      ?? null,
                'stu_post_office'                     => $contact->stu_post_office        ?? null,
                'stu_pin_code'                        => $contact->stu_pin_code           ?? null,
                'stu_email'                           => $contact->stu_email              ?? null,

                'address_equal'                       => $contact->address_equal ?? 0,
                'guardian_country_code_fk'            => $contact->guardian_country_code_fk    ?? null,
                'guardian_state_code_fk'              => $contact->guardian_state_code_fk      ?? null,
                'guardian_contact_address'            => $contact->guardian_contact_address    ?? null,
                'guardian_contact_habitation'         => $contact->guardian_contact_habitation ?? null,
                'guardian_contact_district'           => $contact->guardian_contact_district   ?? null,
                'guardian_contact_block'              => $contact->guardian_contact_block      ?? null,
                'guardian_contact_panchayat'          => $contact->guardian_contact_panchayat  ?? null,
                'guardian_post_office'                => $contact->guardian_post_office        ?? null,
                'guardian_police_station'             => $contact->guardian_police_station     ?? null,
                'guardian_pin_code'                   => $contact->guardian_pin_code           ?? null,
                'guardian_mobile_no'                  => $contact->guardian_mobile_no          ?? null,
                'guardian_email'                      => $contact->guardian_email              ?? null,

                // -------- BANK ---------------------
                'bank_ifsc'                           => $bank->bank_ifsc       ?? null,
                'stu_bank_acc_no'                     => $bank->stu_bank_acc_no ?? null,

                // -------- SYSTEM -------------------
                'status'                              => 1,
                'created_by'                          => $userId,
                'updated_by'                          => $userId,
            ];

            // 🔑 STRICTLY only by school_id_fk (as you asked)
            StudentEntryMaster::Create($masterData);
        });
    }


    public function StudentDetailsByStudentCode(Request $request)
    {
        $student = StudentInfo::where('student_code', $request->student_code)->first();

        if (!$student) {
            return response()->json([
                'status' => false,
                'message' => 'Student not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'student' => $student
        ]);
    }



    // =========================


    public function getStudentEditDetailsByStudentCode()
    {
        try {
            $data = [];
            $schoolId = 1;  // <-- get from login or session or URL

            // ---------------------------------------------
            // 1. Load master data (Dropdowns)
            // ---------------------------------------------
            $draft = DB::table('bs_student_entry_draft_tracker')
                ->where('status', 1)
                ->where('school_id_fk', $schoolId)
                ->orderByDesc('step_number')
                ->first();

            // Default step = 1 if no record found
            $data['current_step'] = $draft ? $draft->step_number : 0;
            $data['stateScholarships'] = DB::table('bs_name_and_code_of_state_scholarships_master')
                ->where('status', 1)
                ->orderBy('id')
                ->get();

            $data['centralScholarships'] = DB::table('bs_name_and_code_of_central_scholarships_master')
                ->where('status', 1)
                ->orderBy('id')
                ->get();




            // 1.============================StudentInfo=======================================
            $student_basic_info = StudentInfo::where('school_id_fk', $schoolId)->first();
            // dd($student_basic_info);

            if ($student_basic_info) {
                $data['basic_info'] = [
                    'student_name'                => $student_basic_info->studentname,
                    'student_name_as_per_aadhaar' => $student_basic_info->studentname_as_per_aadhaar,
                    'gender'                      => $student_basic_info->gender_code_fk,
                    'dob'                         => $student_basic_info->dob,
                    'father_name'                 => $student_basic_info->fathername,
                    'mother_name'                 => $student_basic_info->mothername,
                    'guardian_name'               => $student_basic_info->guardian_name,
                    'aadhaar_child'               => $student_basic_info->aadhaar_number,
                    'mother_tongue'               => $student_basic_info->mothertonge_code_fk,
                    'social_category'             => $student_basic_info->social_category_code_fk,
                    'religion'                    => $student_basic_info->religion_code_fk,
                    'nationality'                 => $student_basic_info->nationality_code_fk,
                    'blood_group'                 => $student_basic_info->blood_group_code_fk,
                    'bpl_beneficiary'             => $student_basic_info->bpl_y_n,
                    'antyodaya_anna_yojana'       => $student_basic_info->bpl_aay_beneficiary_y_n,
                    'bpl_number'                  => $student_basic_info->bpl_no,
                    'disadvantaged_group'         => $student_basic_info->disadvantaged_group_y_n,
                    'cwsn'                        => $student_basic_info->cwsn_y_n,
                    'type_of_impairment'          => $student_basic_info->cwsn_disability_type_code_fk,
                    'disability_percentage'       => $student_basic_info->disability_percentage,
                    'out_of_school'               => $student_basic_info->out_of_sch_child_y_n,
                    'mainstreamed'                => $student_basic_info->child_mainstreamed,
                    'birth_reg_no'                => $student_basic_info->birth_registration_number,
                    'identification_mark'         => $student_basic_info->identification_mark,
                    'health_id'                   => $student_basic_info->health_id,
                    'relationship_with_guardian'  => $student_basic_info->stu_guardian_relationship,
                    'family_income'               => $student_basic_info->guardian_family_income,
                    'guardian_qualifications'     => $student_basic_info->guardian_qualification,
                    'student_height'              => $student_basic_info->stu_height_in_cms,
                    'student_weight'              => $student_basic_info->stu_weight_in_kgs,
                ];
            }
            // 2 . ====================Enrollment========================================

            $student_enrollment_info = StudentEnrollmentInfo::where('school_id_fk', $schoolId)->first();
            // dd($student_enrollment_info);
            if ($student_enrollment_info) {

                $data['enrollment_info'] = [
                    'admission_no'              => $student_enrollment_info->admission_no,
                    'status_pre_year'           => $student_enrollment_info->status_pre_year,
                    'prev_class_appeared_exam'  => $student_enrollment_info->prev_class_appeared_exam,
                    'prev_class_exam_result'    => $student_enrollment_info->prev_class_exam_result,
                    'prev_class_marks_percent'  => $student_enrollment_info->prev_class_marks_percent,
                    'attendention_pre_year'     => $student_enrollment_info->attendention_pre_year,

                    'pre_class_code_fk'         => $student_enrollment_info->pre_class_code_fk,
                    'pre_section_code_fk'       => $student_enrollment_info->pre_section_code_fk,
                    'pre_stream_code_fk'        => $student_enrollment_info->pre_stream_code_fk,
                    'pre_roll_number'           => $student_enrollment_info->pre_roll_number,

                    'cur_class_code_fk'         => $student_enrollment_info->cur_class_code_fk,
                    'academic_year'             => $student_enrollment_info->academic_year,
                    'cur_section_code_fk'       => $student_enrollment_info->cur_section_code_fk,
                    'medium_code_fk'            => $student_enrollment_info->medium_code_fk,
                    'cur_roll_number'           => $student_enrollment_info->cur_roll_number,
                    'admission_date'            => $student_enrollment_info->admission_date,
                    'cur_stream_code'            => $student_enrollment_info->cur_stream_code_fk,

                    'admission_type_code_fk'    => $student_enrollment_info->admission_type_code_fk,
                ];
            }

            // 3 . ==================== Facility & Others Detail========================================


            $facility = StudentFacilityAndOtherDetails::where('school_id_fk', $schoolId)->first();


            if ($facility) {

                $data['facility'] = [
                    // Facilities Provided
                    'facilities_provided_for_the_yeear' => $facility->facilities_provided_y_n,
                    'free_uniforms'                     => $facility->free_uniform_y_n,
                    'free_transport_facility'           => $facility->free_transport_facility_y_n,
                    'free_escort'                       => $facility->free_escort_y_n,
                    'free_host_facility'                => $facility->free_hostel_y_n,
                    'free_bicycle'                      => $facility->free_cycle_y_n,
                    'free_shoe'                         => $facility->free_shoe_y_n,
                    'free_exercise_book'                => $facility->free_exercise_book_y_n,
                    'complete_free_books'               => $facility->complete_set_of_free_books_y_n,

                    // Scholarships
                    'central_scholarship'               => $facility->central_scholarship_rcv_y_n,
                    'central_scholarship_name'          => $facility->central_scholarship_code_fk,
                    'central_scholarship_amount'        => $facility->central_scholarship_amount,

                    'state_scholarship'                 => $facility->state_scholarship_rcv_y_n,
                    'state_scholarship_name'            => $facility->state_scholarship_code_fk,
                    'state_scholarship_amount'          => $facility->state_scholarship_amount,

                    'other_scholarship'                 => $facility->other_scholarship_rcv_y_n,
                    'other_scholarship_amount'          => $facility->other_scholarship_amount,

                    // Gifted fields
                    'child_hyperactive_disorder'        => $facility->screened_for_attention_deficit_hyperactive_disorder_y_n,
                    'stu_extracurricular_activity'      => $facility->extracurricular_activity_involved_y_n,
                    'gifted_math'                       => $facility->gifted_talented_child_in_mathematics,
                    'gifted_language'                   => $facility->gifted_talented_child_in_language,
                    'gifted_science'                    => $facility->gifted_talented_child_in_science,
                    'gifted_technical'                  => $facility->gifted_talented_child_in_technical,
                    'gifted_sports'                     => $facility->gifted_talented_child_in_sports,
                    'gifted_art'                        => $facility->gifted_talented_child_in_art,

                    // Other details
                    'provided_mentors'                  => $facility->provided_mentors_y_n,
                    'whether_participated_nurturance_camp' => $facility->participated_in_nurturance_camps_y_n,
                    'state_nurturance'                  => $facility->state_level_y_n,
                    'national_nurturance'               => $facility->national_level_y_n,
                    'participated_competitions'         => $facility->appeared_state_olympiads_national_level_competition_y_n,
                    'ncc_nss_guides'                    => $facility->participate_in_ncc_nss_scouts_guides_y_n,
                    'rte_free_education'                => $facility->free_education_as_per_rte_act_y_n,
                    'homeless'                          => $facility->child_homeless,
                    'special_training'                  => $facility->special_training_facility_y_n,

                    // Digital
                    'able_to_handle_devices'            => $facility->digital_device_inc_internet_yn,
                    'internet_access'                   => $facility->digital_device_inc_internet_fk,
                ];
            } else {
                $data['facility'] = null;   // No saved data
            }


            // 4 . ==================== Vocational Details ========================================

            $vocational = StudentVocationalDetails::where('school_id_fk', $schoolId)->first();

            if ($vocational) {
                $data['vocational'] = [
                    'exposure'               => $vocational->exposure_vocational_activities_y_n,
                    'undertook'              => $vocational->undertake_vocational_course_y_n,

                    'trade_sector'           => $vocational->vocational_trade_sector_code_fk,
                    'job_role'               => $vocational->vocational_job_role_code_fk,

                    'theory_hours'           => $vocational->vocational_class_attended_theory,
                    'practical_hours'        => $vocational->vocational_class_attended_practical,
                    'industry_hours'         => $vocational->vocational_class_attended_industry_training,
                    'field_visit_hours'      => $vocational->vocational_class_attended_field_visit,

                    'appeared_exam'          => $vocational->prev_class_exam_appeared_fk,
                    'marks_obtained'         => $vocational->prev_class_marks_percent_voc,

                    'placement_applied'      => $vocational->applied_for_placement_code_fk,
                    'apprenticeship_applied' => $vocational->applied_for_apprenticeship_code_fk,

                    'nsqf_level'             => $vocational->vocational_nsqf_level_code_fk,
                    'employment_status'      => $vocational->vocational_placement_status_code_fk,
                    'salary_offered'         => $vocational->vocational_salary_offered,
                ];
            } else {
                $data['vocational'] = null;
            }




            // 5 . ==================== Contact Details ========================================

            $student_contact = StudentContactInfo::where('school_id_fk', $schoolId)->first();

            if ($student_contact) {
                $data['student_contact'] = [
                    'stu_country_code'     => $student_contact->stu_country_code_fk,
                    'stu_contact_address'     => $student_contact->stu_contact_address,
                    'stu_contact_district'    => $student_contact->stu_contact_district,
                    'stu_contact_panchayat'   => $student_contact->stu_contact_panchayat,
                    'stu_police_station'      => $student_contact->stu_police_station,
                    'stu_mobile_no'           => $student_contact->stu_mobile_no,
                    'stu_state_code'       => $student_contact->stu_state_code_fk,
                    'stu_contact_habitation'  => $student_contact->stu_contact_habitation,
                    'stu_contact_block'       => $student_contact->stu_contact_block,
                    'stu_post_office'         => $student_contact->stu_post_office,
                    'stu_pin_code'            => $student_contact->stu_pin_code,
                    'stu_email'               => $student_contact->stu_email,

                    // ---- Guardian contact fields ----
                    'guardian_country_code'    => $student_contact->guardian_country_code_fk,
                    'guardian_contact_address'    => $student_contact->guardian_contact_address,
                    'guardian_contact_district'   => $student_contact->guardian_contact_district,
                    'guardian_contact_panchayat'  => $student_contact->guardian_contact_panchayat,
                    'guardian_police_station'     => $student_contact->guardian_police_station,
                    'guardian_mobile_no'          => $student_contact->guardian_mobile_no,
                    'guardian_state_code'      => $student_contact->guardian_state_code_fk,
                    'guardian_contact_habitation' => $student_contact->guardian_contact_habitation,
                    'guardian_contact_block'      => $student_contact->guardian_contact_block,
                    'guardian_post_office'        => $student_contact->guardian_post_office,
                    'guardian_pin_code'           => $student_contact->guardian_pin_code,
                    'guardian_email'              => $student_contact->guardian_email,
                ];
            } else {
                $data['student_contact'] = null;
            }



            // 6 . ==================== Bank Details ========================================

            $student_bank_details = EntryStudentBankInfo::where('school_id_fk', $schoolId)->first();

            if ($student_bank_details) {
                $data['student_bank_details'] = [
                    'bank_id_fk'      => $student_bank_details->bank_id_fk,
                    'branch_id_fk'    => $student_bank_details->branch_id_fk,
                    'bank_ifsc'       => $student_bank_details->bank_ifsc,
                    'stu_bank_acc_no' => $student_bank_details->stu_bank_acc_no,

                ];
            } else {
                $data['student_bank_details'] = null;
            }



            // 7 . ==================== Addi Details ========================================

            $student_additional_details = StudentAdditionalInfo::where('school_id_fk', $schoolId)->first();

            if ($student_additional_details) {
                $data['student_additional_details'] = [
                    'rte_entitlement_claimed_amount'      => $student_additional_details->rte_entitlement_claimed_amount,
                    'stu_residance_sch_distance_code_fk'    => $student_additional_details->stu_residance_sch_distance_code_fk,
                    'cur_class_appeared_exam'       => $student_additional_details->cur_class_appeared_exam,
                    'cur_class_marks_percent' => $student_additional_details->cur_class_marks_percent,
                    'attendention_cur_year' => $student_additional_details->attendention_cur_year,
                ];
            } else {
                $data['student_additional_details'] = null;
            }


            return view('src.modules.student_entry_update.Student_edit', compact('data'));
        } catch (Exception $e) {

            Log::error('Error in getStudentEntry: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error while fetching data',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }



    // ======================== rakesh 16-12-2025 ============================
    /**
     * Display student list with filters
     */

    public function studentList(Request $request)
    {
        try {
            // ===============================
            // Get user's role information
            // ===============================
            $user           = Auth::user();
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];

            // ===============================
            // Get user's school information if available
            // ===============================
            $userSchool = $user->schoolMaster ?? null;
            $isSchoolUser = $user_role_info['is_school_user'] && $userSchool ? true : false;

            // For circle officers, get their circle
            $userCircle = null;
            if ($user_role_info['is_circle_officer']) {
                $userCircle = $user ?? null;
            }

            // ===============================
            // Filter parameters - Set defaults based on role
            // ===============================
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE

            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;

            // Role-based restrictions
            // if ($user_role_info['is_school_user'] && $userSchool) {
            //     // School users (HOI Primary, School Admin) - restrict to their school
            //     $district_id = $userSchool->district_code_fk;
            //     $circle_id = $userSchool->circle_code_fk;
            //     $subdivision_id = $userSchool->subdivision_code_fk;
            //     $school_id = $userSchool->id;
            //     $management_id = $userSchool->school_management_code_fk;
            // } elseif ($user_role_info['is_circle_officer'] && $userCircle) {
            //     // Circle officers - restrict to their circle
            //     $circle_id = $userCircle->id ?? 66;
            //     $circle_id = 66;
            //     $district_id = $userCircle->district_id ?? 1;
            //     $district_id = 1;
            // }
            // Super Admin - no restrictions

            // Override with request values if provided (respecting role restrictions)
            $search_param        = $request->search ?? '';
            $gender_param        = $request->gender ?? '';
            $class_param         = $request->class ?? '';
            $category_param      = $request->category ?? '';
            $bpl_param           = $request->bpl ?? '';
            $cwsn_param          = $request->cwsn ?? '';
            $academic_year_param = $request->academic_year ?? '';
            $per_page            = $request->per_page ?? 20;

            // ===============================
            // Decrypt IDs safely (respecting role restrictions)
            // ===============================
            if (!$user_role_info['is_school_user'] && !$user_role_info['is_circle_officer']) {
                // Only allow filter changes for Super Admin
                foreach (['district_id', 'subdivision_id', 'circle_id', 'management_id', 'school_id'] as $field) {
                    if ($request->filled($field)) {
                        try {
                            ${$field} = Crypt::decrypt($request->$field);
                        } catch (\Throwable $e) {
                            ${$field} = null;
                        }
                    }
                }
            }

            $data = [];

            // ===============================
            // MASTER DATA - RESTRICTED BY ROLE
            // ===============================
            if ($user_role_info['is_school_user'] && $userSchool) {
                // School users only see their own school's data
                $data['districts'] = DistrictMaster::where('id', $district_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();

                $data['circles'] = CircleMaster::where('id', $circle_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();

                $data['subdivisions'] = $subdivision_id ?
                    SubdivisionMaster::where('id', $subdivision_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get() : collect();

                $data['schools'] = SchoolMaster::where('id', $school_id)
                    ->where('status', 1)
                    ->select('id', 'school_name', 'schcd')
                    ->get();

                $data['managements'] = ManagementMaster::where('id', $management_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();
            } elseif ($user_role_info['is_circle_officer'] && $userCircle) {
                // Circle officers see data within their circle
                $data['districts'] = DistrictMaster::where('id', $district_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();

                $data['circles'] = CircleMaster::where('id', $circle_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();

                $data['subdivisions'] = SubdivisionMaster::where('district_id', $district_id)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->get();

                $data['schools'] = SchoolMaster::where('circle_code_fk', $circle_id)
                    ->where('status', 1)
                    ->select('id', 'school_name', 'schcd')
                    ->orderBy('school_name')
                    ->get();

                $data['managements'] = ManagementMaster::where('status', 1)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();
            } else {
                // Super Admin sees all data
                $data['districts'] = DistrictMaster::where('status', 1)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();

                $data['managements'] = ManagementMaster::where('status', 1)
                    ->select('id', 'name')
                    ->orderBy('name')
                    ->get();

                // Get circles based on selected district
                if ($district_id) {
                    $data['circles'] = CircleMaster::where('status', 1)
                        ->where('district_id', $district_id)
                        ->select('id', 'name', 'district_id')
                        ->orderBy('name')
                        ->get();
                } else {
                    $data['circles'] = collect();
                }

                // Get subdivisions based on selected district
                if ($district_id) {
                    $data['subdivisions'] = SubdivisionMaster::where('status', 1)
                        ->where('district_id', $district_id)
                        ->select('id', 'name', 'district_id')
                        ->orderBy('name')
                        ->get();
                } else {
                    $data['subdivisions'] = collect();
                }

                // Get schools based on selected district and circle
                if ($district_id) {
                    $schoolQuery = SchoolMaster::where('district_code_fk', $district_id)
                        ->where('status', 1);

                    if ($circle_id) {
                        $schoolQuery->where('circle_code_fk', $circle_id);
                    }

                    $data['schools'] = $schoolQuery
                        ->select('id', 'school_name', 'schcd')
                        ->orderBy('school_name')
                        ->get();
                } else {
                    $data['schools'] = collect();
                }
            }

            // These are always available to all users
            $data['categories'] = CategoryMaster::where('status', 1)
                ->select('id', 'name')
                ->orderBy('id')
                ->get();

            // Academic years (restrict based on role)
            if ($user_role_info['is_school_user'] && $school_id) {
                $data['academic_years'] = StudentMaster::where('school_code_fk', $school_id)
                    ->select('academic_year')
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');
            } elseif ($user_role_info['is_circle_officer'] && $circle_id) {
                $data['academic_years'] = StudentMaster::where('circle_code_fk', $circle_id)
                    ->select('academic_year')
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');
            } else {
                $data['academic_years'] = StudentMaster::select('academic_year')
                    ->distinct()
                    ->orderBy('academic_year', 'desc')
                    ->pluck('academic_year');
            }

            // ===============================
            // MAIN QUERY WITH ELOQUENT RELATIONSHIPS
            // ===============================
            $query = StudentMaster::with([
                'district:id,name',
                'circle:id,name',
                'school:id,school_name,schcd,school_management_code_fk',
                'school.management:id,name',
                'gender:id,name',
                'currentClass:id,name',
                'category:id,name',
            ])->where('status', 1);

            // ===============================
            // APPLY FILTERS - RESTRICTED BY ROLE
            // ===============================
            if ($user_role_info['is_school_user'] && $school_id) {
                // School users can only see their own school's students
                $query->where('school_code_fk', $school_id);
            } elseif ($user_role_info['is_circle_officer'] && $circle_id) {
                // Circle officers can only see students in their circle
                $query->where('circle_code_fk', $circle_id);

                // Apply additional filters if selected
                if ($district_id) {
                    $query->where('district_code_fk', $district_id);
                }

                if ($management_id) {
                    $query->whereHas('school', function ($q) use ($management_id) {
                        $q->where('school_management_code_fk', $management_id);
                    });
                }

                if ($school_id) {
                    $query->where('school_code_fk', $school_id);
                }
            } else {
                // Super Admin can apply all filters
                if ($district_id) {
                    $query->where('district_code_fk', $district_id);
                }

                if ($circle_id) {
                    $query->where('circle_code_fk', $circle_id);
                }

                if ($management_id) {
                    $query->whereHas('school', function ($q) use ($management_id) {
                        $q->where('school_management_code_fk', $management_id);
                    });
                }

                if ($school_id) {
                    $query->where('school_code_fk', $school_id);
                }

            }

            // Common filters for all users
            if ($gender_param) {
                $query->where('gender_code_fk', $gender_param);
            }

            if ($class_param) {
                $query->where('cur_class_code_fk', $class_param);
            }

            if ($category_param) {
                $query->where('social_category_code_fk', $category_param);
            }

            if ($academic_year_param) {
                $query->where('academic_year', $academic_year_param);
            }

            if ($bpl_param !== '') {
                $query->where('bpl_y_n', $bpl_param);
            }

            if ($cwsn_param !== '') {
                $query->where('cwsn_y_n', $cwsn_param);
            }

            // ===============================
            // SEARCH (within restrictions for each role)
            // ===============================
            if ($search_param) {
                $search = trim($search_param);
                $query->where(function ($q) use ($search) {
                    $q->where('studentname', 'ILIKE', "%{$search}%")
                        ->orWhere('studentname_as_per_aadhaar', 'ILIKE', "%{$search}%")
                        ->orWhere('admission_no', 'ILIKE', "%{$search}%")
                        ->orWhere('student_code', 'ILIKE', "%{$search}%")
                        ->orWhere('aadhaar_number', 'ILIKE', "%{$search}%")
                        ->orWhere('fathername', 'ILIKE', "%{$search}%")
                        ->orWhere('mothername', 'ILIKE', "%{$search}%")
                        ->orWhere('guardian_name', 'ILIKE', "%{$search}%")
                        ->orWhere('stu_mobile_no', 'ILIKE', "%{$search}%")
                        ->orWhere('guardian_mobile_no', 'ILIKE', "%{$search}%")
                        ->orWhereHas('school', function ($q) use ($search) {
                            $q->where('school_name', 'ILIKE', "%{$search}%")
                                ->orWhere('schcd', 'ILIKE', "%{$search}%");
                        });
                });
            }

            // ===============================
            // PAGINATION
            // ===============================
            $data['students'] = $query
                ->orderBy('student_code', 'desc')
                ->paginate($per_page)
                ->withQueryString()
                ->onEachSide(1);

            // ===============================
            // STATISTICS (restricted by role)
            // ===============================
            if ($user_role_info['is_school_user'] && $school_id) {
                // School users statistics only for their school
                $statsBaseQuery = StudentMaster::where('school_code_fk', $school_id);
            } elseif ($user_role_info['is_circle_officer'] && $circle_id) {
                // Circle officers statistics for their circle
                $statsBaseQuery = StudentMaster::where('circle_code_fk', $circle_id);

                if ($district_id) {
                    $statsBaseQuery->where('district_code_fk', $district_id);
                }

                if ($management_id) {
                    $statsBaseQuery->whereHas('school', function ($q) use ($management_id) {
                        $q->where('school_management_code_fk', $management_id);
                    });
                }

                if ($school_id) {
                    $statsBaseQuery->where('school_code_fk', $school_id);
                }
            } else {
                // Super Admin statistics with filters
                $statsBaseQuery = StudentMaster::query();

                if ($district_id) {
                    $statsBaseQuery->where('district_code_fk', $district_id);
                }

                if ($circle_id) {
                    $statsBaseQuery->where('circle_code_fk', $circle_id);
                }

                if ($management_id) {
                    $statsBaseQuery->whereHas('school', function ($q) use ($management_id) {
                        $q->where('school_management_code_fk', $management_id);
                    });
                }

                if ($school_id) {
                    $statsBaseQuery->where('school_code_fk', $school_id);
                }
            }

            // Active students (status = 1)
            $activeQuery = (clone $statsBaseQuery)->where('status', 1);
            // Deactivated students (status = 2)
            $deactivatedQuery = (clone $statsBaseQuery)->where('status', 2);

            // Apply common filters to statistics
            if ($gender_param) {
                $activeQuery->where('gender_code_fk', $gender_param);
            }

            if ($class_param) {
                $activeQuery->where('cur_class_code_fk', $class_param);
            }

            if ($category_param) {
                $activeQuery->where('social_category_code_fk', $category_param);
            }

            if ($academic_year_param) {
                $activeQuery->where('academic_year', $academic_year_param);
            }

            if ($bpl_param !== '') {
                $activeQuery->where('bpl_y_n', $bpl_param);
            }

            if ($cwsn_param !== '') {
                $activeQuery->where('cwsn_y_n', $cwsn_param);
            }

            $data['total_students'] = $activeQuery->count();
            $data['deactivated_students'] = $deactivatedQuery->count();

            $data['male_students']   = (clone $activeQuery)->where('gender_code_fk', 1)->count();
            $data['female_students'] = (clone $activeQuery)->where('gender_code_fk', 2)->count();
            $data['bpl_students']    = (clone $activeQuery)->where('bpl_y_n', 1)->count();
            $data['cwsn_students']   = (clone $activeQuery)->where('cwsn_y_n', 1)->count();

            $data['category_distribution'] = (clone $activeQuery)
                ->select('social_category_code_fk', DB::raw('COUNT(*) as count'))
                ->groupBy('social_category_code_fk')
                ->get();

            // ===============================
            // GET GENDERS AND CLASSES (available to all)
            // ===============================
            $data['class_distribution'] = ClassMaster::where('status', 1)->count();

            $data['genders'] = GenderMaster::where('status', 1)
                ->select('id', 'name')
                ->orderBy('id')
                ->get();

            $data['classes'] = ClassMaster::where('status', 1)
                ->select('id', 'name')
                ->orderBy('id')
                ->get();

            // ===============================
            // Prepare encrypted params for view
            // ===============================
            $encrypted_params = [];
            if ($district_id) {
                $encrypted_params['district_id'] = Crypt::encrypt($district_id);
            }
            if ($circle_id) {
                $encrypted_params['circle_id'] = Crypt::encrypt($circle_id);
            }
            if ($subdivision_id) {
                $encrypted_params['subdivision_id'] = Crypt::encrypt($subdivision_id);
            }
            if ($management_id) {
                $encrypted_params['management_id'] = Crypt::encrypt($management_id);
            }
            if ($school_id) {
                $encrypted_params['school_id'] = Crypt::encrypt($school_id);
            }

            // ===============================
            // VIEW
            // ===============================
            return view('src.modules.student_information.student_list', [
                'data' => $data,
                'user_school' => $userSchool,
                'user_role_info' => $user_role_info, // Pass role info to view
                'user_role_name' => $roleName,
                'is_school_user' => $isSchoolUser,
                'selected_district_id'   => $district_id,
                'selected_subdivision_id'  => $subdivision_id,
                'selected_circle_id'      => $circle_id,
                'selected_management_id' => $management_id,
                'selected_school_id'     => $school_id,
                'search_param'           => $search_param,
                'gender_param'           => $gender_param,
                'class_param'            => $class_param,
                'category_param'         => $category_param,
                'bpl_param'              => $bpl_param,
                'cwsn_param'             => $cwsn_param,
                'academic_year_param'    => $academic_year_param,
                'per_page'               => $per_page,
                'encrypted_params'       => $encrypted_params
            ]);
        } catch (\Exception $e) {
            Log::error('Student list error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'An error occurred while loading student list.');
        }
    }

    public function getSubDivisionByDistrict(Request $request)
    {
        try {
            $district_id = Crypt::decrypt($request->district_id);

            $subdivisions = SubdivisionMaster::where('district_id', $district_id)
                ->where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($subdivision) {
                    return [
                        'id' => $subdivision->id,
                        'encrypted_id' => Crypt::encrypt($subdivision->id),
                        'name' => $subdivision->name
                    ];
                });

            return response()->json($subdivisions);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getBlocksByDistrictSubDivision(Request $request)
    {
        try {
            $district_id    = Crypt::decrypt($request->district_id);
            $subdivision_id = Crypt::decrypt($request->subdivision_id);

            $blocks = BlockMaster::where('district_id', $district_id)
                ->where('subdivision_id', $subdivision_id)
                ->where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($block) {
                    return [
                        'id'            => $block->id,
                        'encrypted_id'  => Crypt::encrypt($block->id),
                        'name'          => $block->name,
                    ];
                });

            return response()->json([
                'success' => true,
                'data'    => $blocks
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid encrypted parameters'
            ], 400);
        }
    }

    /**
     * Get circles by district
     */
    public function getCirclesByDistrict(Request $request)
    {
        try {
            $district_id = Crypt::decrypt($request->district_id);

            $circles = CircleMaster::where('district_id', $district_id)
                ->where('status', 1)
                ->select('id', 'name')
                ->orderBy('name')
                ->get()
                ->map(function ($circle) {
                    return [
                        'id' => $circle->id,
                        'encrypted_id' => Crypt::encrypt($circle->id),
                        'name' => $circle->name
                    ];
                });

            return response()->json($circles);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Get schools by filters
     */
    public function getSchoolsByFilters(Request $request)
    {
        try {
            $district_id = Crypt::decrypt($request->district_id);
            $circle_id = $request->filled('circle_id') ? Crypt::decrypt($request->circle_id) : null;

            $query = SchoolMaster::where('district_code_fk', $district_id)
                ->where('status', 1);

            if ($circle_id) {
                $query->where('circle_code_fk', $circle_id);
            }

            $schools = $query->select('id', 'school_name', 'schcd')
                ->orderBy('school_name')
                ->get()
                ->map(function ($school) {
                    return [
                        'id' => $school->id,
                        'encrypted_id' => Crypt::encrypt($school->id),
                        'name' => $school->school_name . ' (' . $school->schcd . ')',
                        'code' => $school->schcd
                    ];
                });

            return response()->json($schools);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
