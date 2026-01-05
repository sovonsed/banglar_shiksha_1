<?php

namespace App\Http\Controllers\search_student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    StudentMaster};

class SearchStudentController extends Controller
{
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
            // dd($school_id);

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
                if ($searchPurpose === 2) {
                    $query->where('status', 2);
                } else if ($searchPurpose === 1) {
                    $query->where('status', 3);
                }
                // -------------------------------
                // DISTRICT OFFICER
                // -------------------------------
            } elseif ($roleName === 'District Officer') {

                $query->where('district_code_fk', $district_id)
                    ->whereIn('status', [1, 2, 3]);
            }
            // dd($query);
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
}
