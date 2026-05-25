<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class RolePermissions
{
    // Module 1 - Patient Management
    public static function canManagePatients(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse', 'Personnel/HR Staff']);
    }

    public static function canFullyManagePatients(): bool
    {
        return Auth::user()?->role === 'Medical Director';
    }

    public static function canViewPatients(): bool
    {
        return self::canManagePatients();
    }

    // Module 2 - Staff & Department Management
    public static function canManageStaff(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Personnel/HR Staff']);
    }

    public static function canViewStaffSchedules(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse', 'Personnel/HR Staff']);
    }

    public static function canEditStaffAssignments(): bool
    {
        return Auth::user()?->role === 'Personnel/HR Staff' || Auth::user()?->role === 'Medical Director';
    }

    // Module 3 - Ward & Bed Management
    public static function canManageBeds(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse']);
    }

    public static function canViewBeds(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse', 'Personnel/HR Staff']);
    }

    // Module 4 - Appointments & Treatments
    public static function canManageAppointments(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse']);
    }

    public static function canViewAppointments(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse', 'Personnel/HR Staff']);
    }

    public static function canManageTreatments(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Charge Nurse']);
    }

    // Module 5 - Billing & Reporting
    public static function canManageBilling(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Personnel/HR Staff']);
    }

    public static function canViewBillingReports(): bool
    {
        return Auth::user()?->role === 'Medical Director';
    }

    public static function canProcessPayments(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, ['Medical Director', 'Personnel/HR Staff']);
    }
}
