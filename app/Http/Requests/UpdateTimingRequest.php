<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    protected $trim = true;

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
        //$rules['time'] = 'required|regex:^\d\d:\d\d\s-\s\d\d:\d\d^';
        $rules['time'] = 'required|regex:^\d\d:\d\d^';

        return $rules;
    }
}
