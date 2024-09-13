<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Airplane;
use App\Models\Airport;
use App\Models\City;
use App\Models\ClassM;
use App\Models\Employee;
use App\Models\Flight;
use App\Models\Policy;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin', 'manage reservation', 'read reservation', 'manage airplane', 'read airplane', 'manage flight', 'read flight', 'read airport', 'manage airport', 'manage employee', 'read employee', 'manage offer', 'read offer', 'answer question'];

        foreach ($roles as $role)
            Role::create([
                'name' => $role
            ]);

        $user = User::create([
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456789'),
            'phone' => '097784444',
        ]);

        $employee = Employee::create([
            'user_id' => $user->user_id,
            'name' => 'Admin',
            'job_title' => 'Admin',
            'department' => 'Management',
        ]);

        foreach (Role::get() as $role) {
            if ($role->name == 'admin')
                UserRole::create([
                    'employee_id' => $employee->employee_id,
                    'role_id' => $role->role_id,
                ]);
            break;
        }

        Policy::create([
            'policy_name' => 'after two weeks',
            'value' => 0,
        ]);
        Policy::create([
            'policy_name' => 'after month',
            'value' => 0,
        ]);
        Policy::create([
            'policy_name' => 'cancelation before a day',
            'value' => 75,
        ]);
        Policy::create([
            'policy_name' => 'cancelation before a week',
            'value' => 25,
        ]);
        Policy::create([
            'policy_name' => 'cancelation before more than a week',
            'value' => 5,
        ]);

        Airport::factory(5)->create();
        Airplane::factory(10)->create();
    }
}
