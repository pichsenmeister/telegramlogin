<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CodeRequest extends Request
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
            'code' => 'required|string',
            'client_id' => 'required|numeric',
            'client_secret' => 'required|string'
        ];
    }
}
