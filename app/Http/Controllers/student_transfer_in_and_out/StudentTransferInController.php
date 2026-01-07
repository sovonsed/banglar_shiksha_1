<?php

namespace App\Http\Controllers\student_transfer_in_and_out;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
StudentMaster,
StudentApiTrackModel};
use App\Models\student_transfer_in_and_out\StudentTransferInModel;
use Illuminate\Support\Facades\DB;
class StudentTransferInController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            // dd($user);
            // dd(optional($user->roles()->first())->name);    
            $user_role_info = user_roles_map();
            $roleName       = $user_role_info['role_name'];
            $scope = user_scope();     // 🔥 FULLY DYNAMIC ROLE ENGINE
            $district_id    = $scope['district_code_fk'] ?? null;
            $subdivision_id = $scope['subdivision_code_fk'] ?? null;
            $circle_id      = $scope['circle_code_fk'] ?? null;
            $management_id  = $scope['school_management_code_fk'] ?? null;
            $school_id      = $scope['school_code_fk'] ?? null;
            // dd($school_id);
            // dd($roleName);
            $query = StudentTransferInModel::query()
                ->with([
                    'studentInfo:student_code,studentname,dob,guardian_name,cur_roll_number',
                    'reasonInfo:id,name'
                ])
                ->where('status', 1);
            if (in_array($roleName, ['HOI Primary'])) {
                // School user → only their school
                $query->where('new_school_code_fk', $school_id);
            }
            // dd($query);
            $results = $query->get();

            return view(
                'src.modules.student_transfer_in.index',
                compact('results', 'user')
            );
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'message' => 'An error occurred',
                false   => $e->getMessage(),
            ], 500);
        }
    }
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user  = Auth::user();
            $scope = user_scope();

            $school_id   = $scope['school_code_fk'] ?? null;
            $district_id = $scope['district_code_fk'] ?? null;

            /* -------------------------------------------------
            | 1. Validate Input
            -------------------------------------------------*/
            $data = $request->validate([
                'student_code'   => 'required|string|size:14',
                'reason_code_fk' => 'required|integer',
            ]);

            /* -------------------------------------------------
            | 2. Check student in bs_student_master
            -------------------------------------------------*/
            $student = StudentMaster::where('student_code', $data['student_code'])
                ->where('school_code_fk', $school_id)
                ->lockForUpdate()
                ->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'This Student does not exist in this school'
                ]);
            }

            /* -------------------------------------------------
            | 3. Check Dropbox existence
            -------------------------------------------------*/
            if (
                StudentTransferInModel::withTrashed()
                    ->where('student_code', $data['student_code'])
                    ->exists()
            ) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'This Student already exists in Dropbox.'
                ]);
            }


            /* -------------------------------------------------
            | 4. Insert into ep_student_dropbox
            -------------------------------------------------*/
            StudentTransferInModel::create([
                'student_code'                => $student->student_code,
                'academic_year'               => $student->academic_year,
                'school_code_fk'              => $student->school_code_fk,
                'circle_code_fk'              => $student->circle_code_fk,
                'block_munc_code_fk'          => $student->block_munc_code_fk,
                'district_code_fk'            => $student->district_code_fk,
                'gs_ward_code_fk'             => $student->gs_ward_code_fk,
                'reason_code_fk'              => $data['reason_code_fk'],
                'not_transfer_reason_code_fk' => null,
                'entry_ip'                    => $request->ip(),
                'created_at'                  => now(),
                'created_by'                  => $user->id,
            ]);

            /* -------------------------------------------------
            | 5. Update ep_student_master
            -------------------------------------------------*/
            StudentMaster::where('student_code', $student->student_code)
            ->where('district_code_fk', $district_id)
            ->update([
                'status'     => 2, // Transfer Out
                'updated_at' => now(),
                'updated_by' => $user->id,
                'update_ip'  => $request->ip(),
            ]);
            /* -------------------------------------------------
            | 6. Refresh bs_student_api_track
            -------------------------------------------------*/
            $apiTrack = StudentApiTrackModel::withTrashed()
                ->where('student_code', $student->student_code)
                ->first();

            if ($apiTrack) {
                $apiTrack->restore();
                $apiTrack->update([
                    'school_code_fk'            => $student->school_code_fk,
                    'sms_status'                => 1,
                    'kanyashree_status'         => 1,
                    'vocational_council_status' => 1,
                    'rbsk_status'               => 1,
                    'bcw_status'                => 1,
                    'udise_status'              => 1,
                    'utsashree_status'          => 1,
                    'sabooj_sathi_status'       => 1,
                    'wbchse_status'             => 1,
                    'status'                    => 3,
                    'updated_at'                => now(),
                ]);
            } else {
                StudentApiTrackModel::create([
                    'student_code'              => $student->student_code,
                    'school_code_fk'            => $student->school_code_fk,
                    'district_id_fk'            => $student->district_code_fk,
                    'sms_status'                => 1,
                    'kanyashree_status'         => 1,
                    'vocational_council_status' => 1,
                    'rbsk_status'               => 1,
                    'bcw_status'                => 1,
                    'udise_status'              => 1,
                    'utsashree_status'          => 1,
                    'sabooj_sathi_status'       => 1,
                    'wbchse_status'             => 1,
                    'created_at'                => now(),
                ]);
            }


            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Student transferred out successfully'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
