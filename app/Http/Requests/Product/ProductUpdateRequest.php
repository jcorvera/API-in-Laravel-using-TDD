<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(Auth::user())
            return true;
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=> 'required|max:64|unique:products,name,'.$this->product,
            'slug'=> 'required|max:64|unique:products,slug,'.$this->product,
            'price'=> 'required|integer'
        ];
    }
}
