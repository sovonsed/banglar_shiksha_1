<?php

namespace App\Http\Controllers\search_student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\{
    StudentMaster};
use App\Models\student_transfer_in_and_out\StudentTransferOutModel;

class SearchStudentController extends Controller
{
    public function searchStudentByStudentCode(Request $request)
    {
        try {
            $user = Auth::user();
            $roleName = user_roles_map()['role_name'] ?? null;
            $scope = user_scope();

            $school_id = $scope['school_code_fk'] ?? null;

            /* -------------------------------------------------
            | Validation
            -------------------------------------------------*/
            $request->validate([
                'student_code'   => ['required', 'digits:14'],
                'search_purpose' => ['required'], // 1=Deactivate, 2=Delete, 3=Transfer Out, 4=Transfer In
            ]);

            $student_code  = $request->student_code;
            $searchPurpose = (int) $request->search_purpose;

            /* =================================================
            | PURPOSE 4 → SEARCH IN DROPBOX
            =================================================*/
            if ($searchPurpose === 4) {

                $student = StudentTransferOutModel::with([
                        'studentInfo.currentClass:id,name',
                        'studentInfo.currentSection:id,name',
                    ])
                    ->where('student_code', $student_code)
                    ->first();
                    if (!$student || !$student->studentInfo) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Student not found or not pending for selected action'
                        ], 200);
                    }

                    return response()->json([
                        'status' => true,
                        'data'   => [
                            'student_code'    => $student->student_code,
                            'studentname'     => $student->studentInfo->getAttributes()['studentname'],
                            'dob'             => $student->studentInfo->dob,
                            'guardian_name'   => $student->studentInfo->guardian_name,
                            'current_class'   => $student->studentInfo->currentClass?->name,
                            'current_section' => $student->studentInfo->currentSection?->name,
                            'cur_roll_number' => $student->studentInfo->cur_roll_number,
                            'status'          => $student->studentInfo->status,
                            'search_purpose'  => $searchPurpose,
                        ]
                    ]);

            }

            /* =================================================
            | PURPOSE 1,2,3 → SEARCH IN STUDENT MASTER
            =================================================*/
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
                    'status',
                    'school_code_fk'                ])
                ->where('student_code', $student_code);

            /* -------------------------------------------------
            | Role-based filtering (HOI)
            -------------------------------------------------*/
            if (in_array($roleName, ['HOI Primary'])) {

                if (in_array($searchPurpose, [1, 2, 3])) {
                    $query->where('school_code_fk', $school_id)
                        ->where('status', 1);
                }
                else if($searchPurpose === 4){
                    $query->where('status', 2);
                }
            }
            $student = $query->first();

            if (!$student) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Student not found or not pending for selected action'
                ], 200);
            }

            /* -------------------------------------------------
            | Response (same structure)
            -------------------------------------------------*/
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
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

}
