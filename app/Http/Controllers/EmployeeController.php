<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    //Add Employee Function
    public function addEmployee(EmployeeRequest $employeeRequest)
    {
        $roles = explode(',', $employeeRequest->roles);
        $user = User::create([
            'email' => $employeeRequest->email,
            'password' => Hash::make($employeeRequest->password),
            'phone' => $employeeRequest->phone,
        ]);
        $employee = Employee::create([
            'user_id' => $user->user_id,
            'name' => $employeeRequest->name,
            'job_title' => $employeeRequest->job_title,
            'department' => $employeeRequest->department,
        ]);
        foreach ($roles as $role) {
            UserRole::create([
                'employee_id' => $employee->employee_id,
                'role_id' => $role,
            ]);
        }

        return success(null, 'this employee added successfully', 201);
    }

    //Update Employee Function
    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $updateEmployeeRequest)
    {
        // return $employee->user_id;
        if ($employee->user->email != $updateEmployeeRequest->email) {
            $updateEmployeeRequest->validate([
                'email' => 'unique:users,email',
            ]);
        }
        $roles = explode(',', $updateEmployeeRequest->roles);
        $employee->user->update([
            'email' => $updateEmployeeRequest->email,
            'phone' => $updateEmployeeRequest->phone,
        ]);
        if ($updateEmployeeRequest->password) {
            $employee->user->update([
                'password' => Hash::make($updateEmployeeRequest->password),
            ]);
        }
        $employee->update([
            'user_id' => $employee->user->user_id,
            'name' => $updateEmployeeRequest->name,
            'job_title' => $updateEmployeeRequest->job_title,
            'department' => $updateEmployeeRequest->department,
        ]);
        foreach ($employee->rolesEmployee as $role) {
            $role->delete();
        }
        foreach ($roles as $role) {
            UserRole::create([
                'employee_id' => $employee->employee_id,
                'role_id' => $role,
            ]);
        }

        return success(null, 'this employee updated successfully');
    }

    //Delete Employee Function
    public function deleteEmployee(User $user)
    {
        $user->delete();

        return success(null, 'this employee deleted successfully');
    }

    //Get Employees Function
    public function getEmployees()
    {
        $employees = Employee::with('user', 'roles')->get();

        return success($employees, null);
    }

    //Get Employee Information Function
    public function getEmployeeInformation(Employee $employee)
    {
        return success($employee->with('user', 'roles')->find($employee->employee_id), null);
    }
}