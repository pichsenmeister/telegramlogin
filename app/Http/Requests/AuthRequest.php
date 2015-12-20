<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AuthRequest extends Request
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
            'code' => 'required|string|exists:codes,code',
            'client_id' => 'required|numeric|exists:apps,client_id',
            'client_secret' => 'required|string|exists:apps,client_secret'
        ];
    }
}
