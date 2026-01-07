<?php

namespace App\Models\student_transfer_in_and_out;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentMaster;
use App\Models\TransferOutReasonModel;
class StudentTransferInModel extends Model
{
 use HasFactory, SoftDeletes;
    protected $table = 'bs_student_transfer_history';
    public $timestamps = false; 
    protected $fillable = [
        'student_code',
        'studentname',
        'dob',
        'academic_year',
        'cur_class_code_fk',
        'cur_section_code_fk',
        'cur_roll_number',
        'old_school_code_fk',
        'new_school_code_fk',
        'reason_code_fk',
        'entry_ip',
        'enter_by',
        'enter_by_stake_cd',
        'transfer_out_date',
        'status',
        'created_at'
    ];
    public function studentInfo()
    {
        return $this->belongsTo(StudentMaster::class, 'student_code','student_code');
    }  
    public function reasonInfo()
    {
        return $this->belongsTo(TransferOutReasonModel::class, 'reason_code_fk','id');
    }
}
