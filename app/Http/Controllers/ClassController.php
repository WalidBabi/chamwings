<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClassRequest;
use App\Models\Airplane;
use App\Models\ClassM;
use App\Models\Log;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    //Add Class Function
    public function addClass(Airplane $airplane, ClassRequest $classRequest)
    {
        $user = Auth::guard('user')->user();
        $seats_number = ['a', 'b', 'c', 'd', 'e', 'f'];
        $classRequest->validated([
            'class_name' => 'required',
            'cabin_weight' => 'required',
        ]);
        foreach ($airplane->classes as $class) {
            if ($class->class_name === $classRequest->class_name) {
                return error('some thing went wrong', 'this class already created', 422);
            }
        }
        $class = ClassM::create([
            'airplane_id' => $airplane->airplane_id,
            'class_name' => $classRequest->class_name,
            'price_rate' => $classRequest->price_rate,
            'weight_allowed' => $classRequest->weight_allowed,
            'number_of_meals' => $classRequest->number_of_meals,
            'number_of_seats' => $classRequest->class_name == 'Business' ? 18 : 150,
            'cabin_weight' => $classRequest->cabin_weight,
        ]);

        if ($classRequest->class_name === 'Business') {
            for ($row_number = 1; $row_number <= 3; $row_number++) {
                foreach ($seats_number as $seat_number) {
                    Seat::create([
                        'class_id' => $class->class_id,
                        'seat_number' => $seat_number,
                        'row_number' => $row_number,
                    ]);
                }
            }
        } else {
            for ($row_number = 4; $row_number <= 28; $row_number++) {
                foreach ($seats_number as $seat_number) {
                    Seat::create([
                        'class_id' => $class->class_id,
                        'seat_number' => $seat_number,
                        'row_number' => $row_number,
                    ]);
                }
            }
        }
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' added class ' . $class->class_name,
            'type' => 'insert',
        ]);
        return success(null, 'this class created successfully', 201);
    }

    //Edit Class Function
    public function editClass(ClassM $classM, ClassRequest $classRequest)
    {
        $user = Auth::guard('user')->user();
        $classM->update([
            'price_rate' => $classRequest->price_rate,
            'weight_allowed' => $classRequest->weight_allowed,
            'number_of_meals' => $classRequest->number_of_meals,
            'cabin_weight' => $classRequest->cabin_weight,
        ]);

        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' updated class ' . $classM->class_name,
            'type' => 'update',
        ]);

        return success(null, 'this class updated successfully');
    }

    //Delete Class Function
    public function deleteClass(ClassM $classM)
    {
        $user = Auth::guard('user')->user();
        Log::create([
            'message' => 'Employee ' . $user->employee->name . ' delete class ' . $classM->class_name,
            'type' => 'delete',
        ]);
        $classM->delete();
        return success(null, 'this class deleted successfully');
    }
}