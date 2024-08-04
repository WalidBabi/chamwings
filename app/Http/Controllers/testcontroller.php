<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class testcontroller extends Controller
{
    public function test(){
        return response()->json(['message' => 'Hello World']);
    }
}
