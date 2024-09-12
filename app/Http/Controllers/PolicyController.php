<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    //Get Policies Function
    public function getPolicies()
    {
        $policies = Policy::all();

        return success($policies, null);
    }

    //Get Policy Information Function
    public function getPolicyInformation(Policy $policy)
    {
        return success($policy, null);
    }

    //Edit Policy Value Fucntion
    public function editPolicy(Policy $policy, Request $request)
    {
        $request->validate([
            'value' => 'required',
        ]);

        $policy->update([
            'value' => $request->value,
        ]);

        return success($policy, 'this policy value updated successfully');
    }
}