<?php

namespace App\Http\Requests;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;

class StoreBarberRequest extends FormRequest
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
        $rules['email'] = 'required|email|unique:users';
        $rules['mobile'] = 'required|numeric|unique:users';
        $rules['latitude'] = 'required|numeric';
        $rules['longitude'] = 'required|numeric';
        $rules['profile'] = 'required|image|mimes:jpeg,png,jpg';
        $rules['document_1_name'] = 'required|string';
        $rules['document_1'] = 'required|image|mimes:jpeg,png,jpg';
        $rules['document_2_name'] = 'string';
        if ($this->input('document_2_name') != null) {
            $rules['document_2'] = 'required|image|mimes:jpeg,png,jpg';
        }
        $rules['city'] = 'required|exists:cities,id';
        //$rules['state'] = 'required|exists:states,id';
        $rules['gender'] = 'required';

        return $rules;
    }
}
