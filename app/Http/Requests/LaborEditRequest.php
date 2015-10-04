<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class LaborEditRequest extends Request
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
     * @return array
     */
    public function rules()
    {
        //dd($this->input( 'labor_photo' ));
        return [
            'employee_no' => 'required|numeric',
            'name' => 'required',
            'trade_id' => 'required',
            'labor_photo' => 'mimes:jpeg',
        ];
    }

    public function messages()
{
    return [
        'employee_no.required' => 'Er, you forgot to put employee no.',
        'employee_no.numeric' => 'Employee No. must be a number',
        'name.required' => 'Every person should have a name, peace bro.',
        'trade_id.required' => 'Why would you add someone without a profession?',
        'labor_photo.mimes' => 'PHOTO must be of type jpg',
    ];
}
}
