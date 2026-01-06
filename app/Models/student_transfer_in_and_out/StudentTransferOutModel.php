<?php

namespace App\Models\student_transfer_in_and_out;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentMaster;
class StudentTransferOutModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bs_student_dropbox';
    protected $fillable = [
        'student_code',
        'academic_year',
        'school_code_fk',
        'circle_code_fk',
        'block_munc_code_fk',
        'district_code_fk',
        'gs_ward_code_fk',
        'reason_code_fk',
        'not_transfer_reason_code_fk',
        'uniform_status',
        'status',
        'entry_ip',
        'created_at',
        'created_by',
        'deleted_at'
    ];
    public function studentInfo()
    {
        return $this->belongsTo(StudentMaster::class, 'student_code','student_code');
    }  
}
