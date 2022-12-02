<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBarberRequest extends FormRequest
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
        $rules['name'] = 'required';
        $rules['address_line_1'] = 'required';
        $rules['email'] = 'required|email';
        $rules['mobile'] = 'required|numeric';
        $rules['latitude'] = 'required|numeric';
        $rules['longitude'] = 'required|numeric';
        $rules['profile'] = 'image|mimes:jpeg,png,jpg';
        $rules['document_1_name'] = 'string';
        $rules['document_1'] = 'image|mimes:jpeg,png,jpg';
        $rules['document_2_name'] = 'nullable|string';
        if ($this->input('document_2_name') != null) {
            $rules['document_2'] = 'image|mimes:jpeg,png,jpg';
        }
        $rules['city'] = 'required|exists:cities,id';
        //$rules['state'] = 'required|exists:states,id';
        $rules['gender'] = 'required';

        return $rules;
    }
}
