<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;
class PolicyController extends Controller
{
    //Get Policies Function
    // public function getPolicies()
    // {
    //     $policies = Policy::all();

    //     return success($policies, null);
    // }

    //Get Policy Information Function
    public function getPolicyInformation(Policy $policy)
    {
        $policyInfo = $policy->only(['policy_id', 'policy_name', 'description', 'value']);
        return success($policyInfo, null);
    }

    // //Edit Policy Value Fucntion
    // public function editPolicy(Policy $policy, Request $request)
    // {
    //     $request->validate([
    //         'value' => 'required',
    //     ]);

    //     $policy->update([
    //         'value' => $request->value,
    //     ]);

    //     return success($policy, 'this policy value updated successfully');
    // }

    public function index()
    {
        $policies = Policy::all();
        return success($policies, 'Policies retrieved successfully');
    }

    public function store(Request $request)
    {
        // dd($request);
        $validatedData = $request->validate([
            'policy_name' => 'required|string|max:255',
            'value' => 'required|numeric|between:0,100',
            'description' => 'required|string',
        ]);

        $policy = Policy::create($validatedData);
        // dd(Auth::guard('user')->user());
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name  . ' created policy: ' . $policy->policy_name,
            'type' => 'insert',
        ]);

        return success($policy, 'Policy created successfully', 201);
    }

    public function update(Request $request, Policy $policy)
    {
        $validatedData = $request->validate([
            'policy_name' => 'required|string|max:255',
            'value' => 'required|numeric|between:0,100',
            'description' => 'required|string',
        ]);

        $policy->update($validatedData);
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name  . ' updated policy: ' . $policy->policy_name,
            'type' => 'update',
        ]);

        return success($policy, 'Policy updated successfully');
    }

    public function destroy(Policy $policy)
    {
        $policy->delete();
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' deleted policy: ' . $policy->policy_name,
            'type' => 'delete',
        ]);

        return success(null, 'Policy deleted successfully');
    }
}