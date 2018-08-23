<?php

namespace Botble\ACL\Http\Requests\API;

use Botble\Support\Http\Requests\Request;

class LoginRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'client_id' => 'required',
            'client_secret' => 'required',
            'username' => 'required|string',
            'password' => 'required|string',
        ];
    }
}
