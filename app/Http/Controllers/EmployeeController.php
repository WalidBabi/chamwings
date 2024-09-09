<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\VerificationCodeRequest;
use App\Mail\Verification;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use App\Models\VerifyAccount;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EmployeeController extends Controller
{
    //Add Employee Function
    public function addEmployee(EmployeeRequest $employeeRequest)
    {
        $user = Auth::guard('user')->user();
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

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added new employee to system his name ' . $employee->name,
            'type' => 'insert',
        ]);

        return success($employee->with('user')->find($employee->employee_id), 'this employee added successfully', 201);
    }

    //Update Employee Function
    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $updateEmployeeRequest)
    {
        $user = Auth::guard('user')->user();
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

        return success($employee, 'this employee updated successfully');
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated employee ' . $employee->name . ' Information',
            'type' => 'update',
        ]);
    }

    //Update Email Function
    public function updateEmail(Employee $employee, $email, VerificationCodeRequest $verificationCodeRequest)
    {
        $user = Auth::guard('user')->user();
        $verify = VerifyAccount::where('email', $email)->latest()->first();
        if ($verify && $verify->created_at > Carbon::now() && $verify->code == $verificationCodeRequest->code) {
            $employee->user->update([
                'email' => $email,
            ]);
            if ($verify->password)
                $employee->user->update([
                    'password' => $verify->password
                ]);
            $verify->delete();
            Log::create([
                'message' => 'Employee ' . $user->employee->name . ' updated employee ' . $employee->name . ' email',
                'type' => 'update',
            ]);
            return success(null, 'your profile updated successfully');
        }

        return error('some thing went wrong', 'incorrect verification code', 422);
    }

    //Delete Employee Roles Function
    public function deleteRoles(Employee $employee, Request $request)
    {
        $user = Auth::guard('user')->user();
        $roles = explode(',', $request->roles);

        foreach ($roles as $role) {
            UserRole::where(['employee_id' => $employee->employee_id, 'role_id' => $role])->first()->delete();
        }

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' delete roles from employee ' . $employee->name,
            'type' => 'delete',
        ]);

        return success(null, 'this roles deleted successfully');
    }

    //Add Roles To Employee Function
    public function addRoles(Employee $employee, Request $request)
    {
        $user = Auth::guard('user')->user();
        $roles = explode(',', $request->roles);

        foreach ($roles as $role) {
            if ($employee->rolesEmployee->where('role_id', $role)->first())
                break;
            UserRole::create([
                'employee_id' => $employee->employee_id,
                'role_id' => $role,
            ]);
        }

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added roles to employee ' . $employee->name,
            'type' => 'insert',
        ]);
        return success(null, 'this roles added successfully', 201);
    }

    //Delete Employee Function
    public function deleteEmployee(Employee $employee)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted employee ' . $employee->name,
            'type' => 'delete',
        ]);
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
            })->orWhere('name', 'LIKE', '%' . $search . '%')->withTrashed()->with('user', 'roles')->paginate(15);
        } else {
            $employees = Employee::with('user', 'roles')->withTrashed()->orderby('employee_id', 'desc')->paginate(15);
        }

        $data = [
            'data' => $employees->items(),
            'total' => $employees->total()
        ];

        return success($data, null);
    }

    //Get Employee Information Function
    public function getEmployeeInformation(Employee $employee)
    {
        return success($employee->with('user', 'roles')->find($employee->employee_id), null);
    }

    //Activate Employee Function
    public function activateEmployee($employee)
    {
        $user = Auth::guard('user')->user();
        $employee = Employee::withTrashed()->find($employee);
        $user = User::withTrashed()->find($employee->user_id);
        if (!$employee) {
            return error(null, null, 404);
        }
        $employee->deleted_at = null;
        $user->deleted_at = null;
        $employee->update();
        $user->update();

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' activated employee ' . $employee->name,
            'type' => 'update',
        ]);

        return success(null, 'this employee activated successfully');
    }

    //Update Profile Function
    public function updateProfile(UpdateEmployeeRequest $updateEmployeeRequest)
    {
        $employee = Auth::guard('user')->user();

        if ($employee->email != $updateEmployeeRequest->email) {
            $updateEmployeeRequest->validate([
                'email' => 'unique:users,email',
            ]);
        }
        if ($updateEmployeeRequest->password) {
            $updateEmployeeRequest->validated([
                'old_password' => 'required',
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
        $employee->update([
            'phone' => $updateEmployeeRequest->phone,
        ]);
        if ($updateEmployeeRequest->password) {
            if (Hash::check($updateEmployeeRequest->old_password, $employee->password)) {
                if ($updateEmployeeRequest->password != $updateEmployeeRequest->confirm_password) {
                    return error('some thing went wrong', 'incorrect confirming password', 422);
                }
            } else {
                return error('some thing went wrong', 'incorrect password', 422);
            }
        }
        $employee->employee->update([
            'name' => $updateEmployeeRequest->name,
            'job_title' => $updateEmployeeRequest->job_title,
            'department' => $updateEmployeeRequest->department,
        ]);


        if ($employee->email != $updateEmployeeRequest->email || !Hash::check($updateEmployeeRequest->password, $employee->password)) {

            if ($updateEmployeeRequest->email) {
                $verify = VerifyAccount::create([
                    'email' => $updateEmployeeRequest->email,
                    'code' => rand(1000, 9999),
                    'created_at' => Carbon::now()->addMinutes(15),
                ]);
            } else {
                $verify = VerifyAccount::create([
                    'email' => $employee->email,
                    'code' => rand(1000, 9999),
                    'created_at' => Carbon::now()->addMinutes(15),
                ]);
            }

            if ($updateEmployeeRequest->password) {
                $verify->update([
                    'password' => Hash::make($updateEmployeeRequest->password),
                ]);
            }
            try {
                Mail::to($updateEmployeeRequest->email)->send(new Verification($verify->code));
            } catch (Exception $e) {
                $verify->delete();
                return error('some thing went wrong', 'cannot send verification code, try again later....', 422);
            }

            return success($verify->email, 'your profile updated and we sent verification code to your new email');
        }
        Log::create([
            'message' => 'Employee ' . $employee->employee->name . ' updated his profile information',
            'type' => 'update',
        ]);
        return success($employee, 'this employee updated successfully');
    }
}