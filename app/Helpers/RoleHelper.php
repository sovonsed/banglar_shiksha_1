<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('current_user')) {
    function current_user()
    {
        return Auth::user();
    }
}

if (!function_exists('current_user_role')) {
    function current_user_role(): ?string
    {
        return current_user()?->roles()->pluck('name')->first();
    }
}

if (!function_exists('current_user_circle')) {
    function current_user_circle(): ?\App\Models\CircleMaster
    {
        $user = current_user();

        if (!$user) return null;

        if (current_user_role() === 'SI') {
            return $user->circle;   // relation call
        }

        return null;
    }
}

if (!function_exists('current_user_master_data')) {
    function current_user_master_data()
    {
        $user = Auth::user();
        if (!$user) return null;

        return match (current_user_role()) {
            'SI'            => $user->circle,
            'School Admin'      => $user->school,
            'HOI Primary'       => $user->hoi,
            'District Officer'  => $user->district,
            default             => null,
        };
    }
}


if (!function_exists('user_roles_map')) {
    function user_roles_map(): array
    {
        $role = current_user_role();

        return [
            'role_name'           => $role,
            'is_super_admin'      => $role === 'Super Admin',
            'is_hoi_pe'              => $role === 'HOI Primary',
            'is_hoi_se'              => $role === 'HOI Secondary',
            'is_school_admin'     => $role === 'School Admin',
            'is_si_officer'   => $role === 'SI',
            'is_district_officer' => $role === 'District Officer',
            'is_school_user'      => in_array($role, ['School Admin', 'HOI']),
            'master_details'      => current_user_master_data(),
        ];
    }
}

if(!function_exists('is_district_officer')) {
    function is_district_officer(): bool
    {
        return user_roles_map()['is_district_officer'];
    }
}

if (!function_exists('master_details')) {
    function master_details()
    {
        return user_roles_map()['master_details'];
    }
}



if (!function_exists('is_super_admin')) {
    function is_super_admin(): bool
    {
        return user_roles_map()['is_super_admin'];
    }
}

if (!function_exists('is_school_user')) {
    function is_school_user(): bool
    {
        return user_roles_map()['is_school_user'];
    }
}

if (!function_exists('is_hoi_pe_user')) {
    function is_hoi_pe_user(): bool
    {
        return user_roles_map()['is_hoi_pe'];
    }
}

if (!function_exists('is_hoi_se_user')) {
    function is_hoi_se_user(): bool
    {
        return user_roles_map()['is_hoi_se_user'];
    }
}

if (!function_exists('is_si_officer')) {
    function is_si_officer(): bool
    {
        return user_roles_map()['is_si_officer'];
    }
}

if (!function_exists('circle_details')) {
    function circle_details(): ?\App\Models\CircleMaster
    {
        return user_roles_map()['circle_details'];
    }
}


if (!function_exists('user_scope')) {
    function user_scope(): array
    {
        $user = Auth::user();
        $role = current_user_role();
        $master = master_details();

        return match ($role) {
            'School Admin', 'HOI' => [
                'school_code_fk'   => $master->id,
                'circle_code_fk'   => $master->circle_code_fk,
                'district_code_fk' => $master->district_code_fk,
            ],

            'SI' => [
                'circle_code_fk'   => $master->id,
                'district_code_fk' => $master->district_id,
            ],

            'District Officer' => [
                'district_code_fk' => $master->id,
            ],

            default => [] // Super Admin
        };
    }
}
