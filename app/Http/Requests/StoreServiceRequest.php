<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
        $rules['category_id'] = 'required';
        $rules['subcategory_id'] = 'required';
        $rules['time'] = 'required|integer';
        $rules['image'] = 'image|mimes:jpeg,png,jpg';

        return $rules;
    }
}
