<?php

namespace Botble\ACL\Http\Controllers\API;

use Botble\ACL\Http\Requests\API\LoginRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     * @param BaseHttpResponse $response
     * @return mixed
     * @author Sang Nguyen
     */
    public function login(LoginRequest $request, BaseHttpResponse $response)
    {
        $http = new Client;

        $data = $http->post(url('/oauth/token'), [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => $request->input('client_id'),
                'client_secret' => $request->input('client_secret'),
                'username' => $request->input('username'),
                'password' => $request->input('password'),
                'scope' => '*',
            ],
        ]);

        return $response->setData(json_decode((string)$data->getBody(), true));
    }

    /**
     * @param Request $request
     * @return BaseHttpResponse
     * @author Sang Nguyen
     */
    public function logout(Request $request, BaseHttpResponse $response)
    {
        $request->user()->token()->delete();
        return $response;
    }
}