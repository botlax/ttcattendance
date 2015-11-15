<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddLoanRequest extends Request
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
        return [
            'amount' => 'numeric|required',
            'months-to-pay' => 'required|numeric|min:1',
            'interval' => 'required|numeric|min:1',
            'starting_date' => 'required'
        ];
    }

    public function messages(){
        return [
            'amount.required'=>'Please enter amount',
            'months-to-pay.required'=>'Please enter no. of months to pay',
            'interval.required'=>'Please enter no. of interval',
            'starting_date.required'=>'Please enter starting date',
            'amount.numeric'=>'Amount field must be a number',
            'months-to-pay.numeric'=>'No. of months to pay must be a number',
            'interval.numeric'=>'Interval must be a number'
        ];
    }
}
