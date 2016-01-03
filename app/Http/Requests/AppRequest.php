<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

use App\App;

class AppRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if($this->route('app')) {
            $id = $this->route('app');
            $user = $this->user();
            return App::where('id', '=', $id)
                ->where('user_id', '=', $user->id)
                ->exists();
        }
        return $this->user() ? true : false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|required',
            'redirect_url' => 'url|required',
            'website' => 'url'
        ];
    }
}
