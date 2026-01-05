<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'sso_id',
        'name',
        'email',
        'password',
        'phone',
        'dise_code',
        'department',
        'designation',
        'last_login_at',
        'status',
        'impersonator_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'status' => 'boolean',
    ];

    // Add this method to handle role assignment from SSO
    public function assignRoleFromSSO($ssoUserData)
    {
        $roleName = $this->determineRoleFromSSO($ssoUserData);

        // Remove existing roles
        $this->roles()->detach();

        // Assign new role
        $this->assignRole($roleName);

        return $roleName;
    }

    protected function determineRoleFromSSO($ssoUserData)
    {
        // Map SSO user data to roles
        $roleMappings = [
            'state_admin' => 'State Admin',
            'cm' => 'CM',
            'mic' => 'MIC',
            'cs' => 'CS',
            'principal_secretary' => 'Principal Secretary',
            'directorate_school_education' => 'Directorate of School Education',
            'dme' => 'DME',
            'md_pbrssm' => 'MD-PBRSSM',
            'nic' => 'NIC',
            'pd_cmdmp' => 'PD-CMDMP',
            'pbssm' => 'PBSSM',
            'spd' => 'SPD',
            'sdmc' => 'SDMC',
            'planning_budget' => 'Planning Budget',
            'adse' => 'ADSE',
            'dpsc' => 'DPSC',
            'district_admin' => 'District Admin',
            'dm' => 'DM',
            'adm' => 'ADM',
            'deo_dpo' => 'DEO/DPO',
            'dis' => 'DIS',
            'oc_mdm' => 'OC-MDM',
            'dno' => 'DNO',
            'doma' => 'DOMA',
            'district_mis' => 'DISTRICT-MIS',
            'sub_division' => 'Sub Division',
            'sdo' => 'SDO',
            'seo' => 'SEO',
            'si' => 'SI',
            'school' => 'School',
            'hoi' => 'HOI',
            'hoi_primary' => 'HOI Primary',
            'hoi_secondary' => 'HOI Secondary',
            'bmmu' => 'BMMU',
            'dmmu' => 'DMMU',
            'msme' => 'MSME',
            'noc' => 'NOC',
        ];

        // Extract role from SSO data (adjust based on your SSO provider)
        $ssoRole = $ssoUserData['role'] ?? $ssoUserData['user_type'] ?? 'district_admin';

        return $roleMappings[$ssoRole] ?? 'District Admin';
    }

    /**
     * school master relationship
     */
    public function schoolMaster()
    {
        return $this->belongsTo(SchoolMaster::class, 'user_id', 'id');
    }

    public function circle()
    {
        return $this->hasOne(CircleMaster::class, 'schcd', 'dise_code');
    }

    public function school()
    {
        return $this->hasOne(SchoolMaster::class, 'schcd', 'dise_code');
    }

    public function district()
    {
        return $this->hasOne(DistrictMaster::class, 'district_id', 'dise_code');
    }

    public function hoi()
    {
        return $this->hasOne(SchoolMaster::class, 'schcd', 'dise_code');
    }


    /**
     * Get the user who is impersonating this user
     */
    public function impersonator()
    {
        return $this->belongsTo(User::class, 'impersonator_id');
    }

    /**
     * Check if user is currently being impersonated
     */
    public function isImpersonated()
    {
        return !is_null($this->impersonator_id);
    }

    /**
     * Get users that this user is impersonating
     */
    public function impersonating()
    {
        return $this->hasMany(User::class, 'impersonator_id');
    }

    /**
     * Get direct permissions count (excluding role permissions)
     */
    public function getDirectPermissionsCountAttribute()
    {
        return $this->permissions()->count();
    }

    /**
     * Get all permissions count (including role permissions)
     */
    public function getAllPermissionsCountAttribute()
    {
        return $this->getAllPermissions()->count();
    }

    /**
     * Check if user is online (active in last 5 minutes)
     */
    public function getIsOnlineAttribute()
    {
        return $this->last_login_at && $this->last_login_at->gt(now()->subMinutes(5));
    }
}
