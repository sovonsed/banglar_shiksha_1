<?php

namespace App\Http\Controllers\student_transfer_in_and_out;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    StudentMaster};
use App\Models\student_transfer_in_and_out\StudentTransferOutModel;
use Illuminate\Support\Facades\DB;
class StudentTransferOutController extends Controller
{

    public function index()
    {
        try {
            $user = Auth::user();
            // dd($user);
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
            $query = StudentTransferOutModel::query()
                ->with([
                    'studentInfo:student_code,studentname,dob,guardian_name,cur_roll_number'
                ])
                ->where('status', 1);
            if (in_array($roleName, ['HOI Primary'])) {
                // School user → only their school
                $query->where('school_code_fk', $school_id);
            }
            // dd($query);
            $results = $query->get();

            return view(
                'src.modules.student_transfer_out.index',
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
            | 2. Check student in bs_student_master (same school)
            -------------------------------------------------*/
            $student = DB::table('bs_student_master')
                ->where('student_code', $data['student_code'])
                ->where('school_code_fk', $school_id)
                ->lockForUpdate()
                ->first();

            if (!$student) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'This Student does not exist in this school'
                ], 404);
            }

            /* -------------------------------------------------
            | 3. Check Dropbox existence
            -------------------------------------------------*/
            $alreadyExists = DB::table('ep_student_dropbox')
                ->where('student_code', $data['student_code'])
                ->exists();

            if ($alreadyExists) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'This Student already exists in Dropbox.'
                ], 409);
            }

            /* -------------------------------------------------
            | 4. Insert into ep_student_dropbox
            -------------------------------------------------*/
            StudentTransferOutModel::create([
                'student_code'                => $student->student_code,
                'academic_year'               => $student->academic_year,
                'school_code_fk'              => $student->school_code_fk,
                'circle_code_fk'              => $student->circle_code_fk,
                'block_munc_code_fk'          => $student->block_munc_code_fk,
                'district_code_fk'            => $student->district_code_fk,
                'gs_ward_code_fk'             => $student->gs_ward_code_fk,
                'reason_code_fk'              => $data['reason_code_fk'],
                'not_transfer_reason_code_fk' => null,
                'uniform_status'              => $student->uniform_status,
                'status'                      => 1,
                'entry_ip'                    => $request->ip(),
                'created_at'                  => now(),
                'created_by'                  => $user->id,
            ]);

            /* -------------------------------------------------
            | 5. Update ep_student_master
            -------------------------------------------------*/
            DB::table('ep_student_master')
                ->where('student_code', $student->student_code)
                ->update([
                    'status'       => 2, // Transfer Out
                    'updated_at'   => now(),
                    'updated_by'   => $user->id,
                    'update_ip'    => $request->ip(),
                ]);

            /* -------------------------------------------------
            | 6. Update ep_student_history
            -------------------------------------------------*/
            DB::table('ep_student_history')->insert([
                'student_code' => $student->student_code,
                'status'       => 2,
                'remarks'      => 'Transferred Out',
                'created_at'   => now(),
                'created_by'   => $user->id,
                'entry_ip'     => $request->ip(),
            ]);

            /* -------------------------------------------------
            | 7. Refresh API track (partition table)
            -------------------------------------------------*/
            $apiTable = 'ep_student_api_track_' . $district_id;

            DB::table($apiTable)
                ->where('student_code', $student->student_code)
                ->delete();

            DB::table($apiTable)->insert([
                'student_code' => $student->student_code,
                'status'       => 3,
                'synced_at'    => now(),
            ]);

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
            ], 500);
        }
    }
}
