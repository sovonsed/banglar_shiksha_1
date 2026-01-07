<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\StudentMaster;

class StudentApiTrackModel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'bs_student_api_track';
    protected $fillable = [
        'student_code',
        'school_code_fk',
        'district_id_fk',
        'sms_status',
        'kanyashree_status',
        'vocational_council_status',
        'rbsk_status',
        'bcw_status',
        'udise_status',
        'utsashree_status',
        'sabooj_sathi_status',
        'wbchse_status',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public function studentInfo()
    {
        return $this->belongsTo(StudentMaster::class, 'student_code','student_code');
    }  
}
