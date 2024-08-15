<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //Get Roles Function
    public function getRoles()
    {
        $roles = Role::paginate(15);
        return success($roles, null);
    }

    //Get Role Information Function
    public function getRoleInformation(Role $role)
    {
        return success($role, null);
    }
}