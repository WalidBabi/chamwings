<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddPassportRequest extends FormRequest
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
            'number' => 'required|integer',
            'status' => 'required',
            'passport_expiry_date' => 'required|date',
            'passport_issued_date' => 'required|date',
            'passport_issued_country' => 'required',
            'passport_image' => 'required|file',
        ];
    }
}