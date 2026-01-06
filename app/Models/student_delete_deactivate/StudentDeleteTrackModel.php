<?php

namespace App\Models\student_delete_deactivate;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ReasonForStudentDeletionnMaster;
use App\Models\StudentDeleteArchive;
use App\Models\StudentMaster;
use App\Models\SchoolMaster;
class StudentDeleteTrackModel extends Model
{

    protected $table = 'bs_student_delete_track';

    protected $primaryKey = 'id'; // IMPORTANT
    protected $fillable = [
        'district_code_fk',
        'circle_code_fk',
        'school_code_fk',
        'student_code',
        'student_name',
        'delete_reason_code_fk',
        'prev_delete_status',
        'entry_ip',
        'enter_by',
        'enter_by_stake_cd',
        'update_ip',
        'update_by',
        'update_by_stake_cd',
        'status'
    ];

    public function deleteReason()
    {
        return $this->belongsTo(ReasonForStudentDeletionnMaster::class, 'delete_reason_code_fk','id');
    }
    public function studentInfoArchive()
    {
        return $this->belongsTo(StudentDeleteArchive::class, 'student_code','student_code');
    }
    public function studentInfo()
    {
        return $this->belongsTo(StudentMaster::class, 'student_code','student_code');
    }  
    public function schoolInfo()
    {
        return $this->belongsTo(SchoolMaster::class,'school_code_fk','id');
    }
}
