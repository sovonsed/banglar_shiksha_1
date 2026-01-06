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
            StudentTransferOutModel::create([                                                                            
                'student_code'               => $student->student_code,
                'academic_year'              => $student->academic_year,
                'school_code_fk'             => $student->school_code_fk,
                'circle_code_fk'             => $student->circle_code_fk,
                'block_munc_code_fk'         => $student->block_munc_code_fk,
                'district_code_fk'           => $student->district_code_fk,
                'gs_ward_code_fk'            => $student->gs_ward_code_fk,
                'reason_code_fk'             => $data['deactivate_reason_code_fk'],
                'not_transfer_reason_code_fk'=> null,
                'uniform_status'             => $student->uniform_status,
                'entry_ip'                   => $request->ip(),
                'created_at'                 => now(),
                'created_by'                 => $user->id,
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
}
