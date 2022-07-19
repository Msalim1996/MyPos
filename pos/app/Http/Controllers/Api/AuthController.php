<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Http\Requests\UserRegisterRequest as StoreRequest;
use App\Http\Resources\CurrentAuthenticatedUserResource;
use App\Http\Resources\UserResource;

/**
 * @group User Authentication
 */
class AuthController extends Controller
{
    /**
     * Register new user
     */
    public function register(StoreRequest $request)
    {
        $request['password'] = Hash::make($request['password']);
        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];

        return response()->json(['message' => $response], 200);
    }

    /**
     * Login user
     *
     * @bodyParam username string required
     * @bodyParam password string required
     */
    public function login(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = [
                    'token' => $token,
                    'data' => new CurrentAuthenticatedUserResource($user),
                ];

                return response($response, 200);
            } else {
                $response = 'Password salah, silahkan coba kembali';
                return response()->json(['message' => $response], 422);
            }
        } else {
            $response = 'Username tidak ditemukan';
            return response()->json(['message' => $response], 422);
        }
    }

    /**
     * Logout user
     *
     * @authenticated
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();

        $response = 'Log out berhasil';
        return response()->json(['message' => $response], 200);
    }

    /**
     * check user token
     * if user authenticated (through middleware) return 200
     */
    public function checkToken() {
        return response()->json(['message' => 'User authenticated'], 200);
    }
}
