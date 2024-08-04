<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateFlightRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'departure_airport' => 'required|exists:airports,airport_id',
            'arrival_airport' => 'required|exists:airports,airport_id',
            'airplane_id' => 'required|exists:airplanes,airplane_id',
            'flight_number' => 'required|numeric',
            'departure_date' => 'required|date',
            'arrival_date' => 'required|date',
            'departure_time' => 'required',
            'arrival_time' => 'required',
            'duration' => 'required',
            'number_of_reserved_seats' => 'required|numeric',
            'price' => 'required|integer',
            'terminal' => 'required',
        ];
    }
}