<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\VerificationCodeRequest;
use App\Mail\Verification;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\VerifyAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
        if ($employeeRequest->file('image')) {
            $path = $employeeRequest->file('image')->storePublicly('ProfileImage', 'public');
            $user->update([
                'image' => 'storage/' . $path,
            ]);
        }
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
        if ($employee->user->email != $updateEmployeeRequest->email) {
            $updateEmployeeRequest->validate([
                'email' => 'unique:users,email',
            ]);
        }
        if ($updateEmployeeRequest->file('image')) {
            if (File::exists($employee->user->image)) {
                File::delete($employee->user->image);
            }
            $path = $updateEmployeeRequest->file('image')->storePublicly('ProfileImage', 'public');
            $employee->user->update([
                'image' => 'storage/' . $path,
            ]);
        }
        $employee->user->update([
            'phone' => $updateEmployeeRequest->phone,
        ]);
        if ($updateEmployeeRequest->password) {
            if ($updateEmployeeRequest->password != $updateEmployeeRequest->confirm_password) {
                return error('some thing went wrong', 'incorrect confirming password', 422);
            }
            $employee->user->update([
                'password' => Hash::make($updateEmployeeRequest->password),
            ]);
        }
        $employee->update([
            'name' => $updateEmployeeRequest->name,
            'job_title' => $updateEmployeeRequest->job_title,
            'department' => $updateEmployeeRequest->department,
        ]);
        foreach ($employee->rolesEmployee as $role) {
            $role->delete();
        }

        if ($employee->user->email != $updateEmployeeRequest->email) {
            $verify = VerifyAccount::create([
                'email' => $updateEmployeeRequest->email,
                'code' => rand(1000, 9999),
                'created_at' => Carbon::now()->addMinutes(15),
            ]);
            try {
                Mail::to($updateEmployeeRequest->email)->send(new Verification($verify->code));
            } catch (Exception $e) {
                $verify->delete();
                return error('some thing went wrong', 'cannot send verification code, try arain later....', 422);
            }

            return success($verify->email, 'your profile updated and we sent verification code to your new email');
        }

        return success(null, 'this employee updated successfully');
    }

    //Update Email Function
    public function updateEmail(Employee $employee, $email, VerificationCodeRequest $verificationCodeRequest)
    {
        $verify = VerifyAccount::where('email', $email)->latest()->first();
        if ($verify && $verify->created_at > Carbon::now() && $verify->code == $verificationCodeRequest->code) {
            $employee->user->update([
                'email' => $email,
            ]);
            $verify->delete();

            return success(null, 'your email updated successfully');
        }

        return error('some thing went wrong', 'incorrect verification code', 422);
    }

    //Delete Employee Roles Function
    public function deleteRoles(Employee $employee, Request $request)
    {
        $roles = explode(',', $request->roles);

        foreach ($roles as $role) {
            UserRole::where(['employee_id' => $employee->employee_id, 'role_id' => $role])->first()->delete();
        }

        return success(null, 'this roles deleted successfully');
    }

    //Add Roles To Employee Function
    public function addRoles(Employee $employee, Request $request)
    {
        $roles = explode(',', $request->roles);

        foreach ($roles as $role) {
            // $employeeRoles = UserRole::where('employee_id',$employee->employee_id)->whereIn('role_id',$roles);
            if ($employee->rolesEmployee->where('role_id', $role)->first())
                break;
            UserRole::create([
                'employee_id' => $employee->employee_id,
                'role_id' => $role,
            ]);
        }
        return success(null, 'this roles added successfully', 201);
    }

    //Delete Employee Function
    public function deleteEmployee(Employee $employee)
    {
        $employee->user->delete();
        $employee->delete();

        return success(null, 'this employee deleted successfully');
    }

    //Get Employees Function
    public function getEmployees(Request $request)
    {
        if ($request->search) {
            $search = $request->search;
            $employees = Employee::whereHas('user', function ($query) use ($search) {
                $query->where('email', 'LIKE', '%' . $search . '%');
            })->orWhere('name', 'LIKE', '%' . $search . '%')->withTrashed()->with('user')->paginate(15);
        } else {
            $employees = Employee::with('user', 'roles')->withTrashed()->paginate(15);
        }

        return success($employees, null);
    }

    //Get Employee Information Function
    public function getEmployeeInformation(Employee $employee)
    {
        return success($employee->with('user', 'roles')->find($employee->employee_id), null);
    }

    //Activate Employee Function
    public function activateEmployee($employee)
    {
        $employee = Employee::withTrashed()->find($employee);
        $user = User::withTrashed()->find($employee->user_id);
        if(!$employee){
            return error(null, null, 404);
        }
        $employee->deleted_at = null;
        $user->deleted_at = null;
        $employee->update();
        $user->update();

        return success(null, 'this employee activated successfully');
    }
}