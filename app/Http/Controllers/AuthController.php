<?php

namespace App\Http\Controllers;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthController extends Controller
{
    public function get (Request $Request)
    {
        try {
            // = Get Authenticated User
            $user = $Request->user();

            // : Return Bearer Token
            return response()->json([
                'status' => true,
                'user' => $user
            ]);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                #'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    public function register(Request $Request)
    {
        try {
            // () Valid User Input Request Data
            $validatedUser = Validator::make($Request->all(), [
                'name'     => 'required|string|max:255',
                'cpf'      => [
                    'required',
                    function ($attribute, $value, Closure $fail) {
                        if ( ! $this->validateCPF($value) ) {
                            $fail('CPF is invalid.');
                        }
                    },
                    'unique:users'
                ],
                'email'    => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role'     => 'required|in:client,collaborator'
            ]);
            if ($validatedUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request Input validation error',
                    'errors' => $validatedUser->errors()
                ], 401);
            }

            // () Create User
            $User = User::create([
                'name' => $Request->name,
                'cpf' => $Request->cpf,
                'email' => $Request->email,
                'password' => Hash::make($Request->password),
                'role' => $Request->role
            ]);
            // () Get Bearer Token
            $token = $User->createToken($Request->email)->plainTextToken;
            // : Return Bearer Token
            return response()->json([
                'status' => true,
                'message' => 'User successfully registered',
                'token' => $token
            ], 200);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                #'debug' => $Throwable->getMessage()
            ], 500);
        }
    }
    public function validateCPF (string $cpf) // TODO move
    {
        // Remove any non-digits
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Check if CPF is the correct length
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Validate CPF using the algorithm
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += intval($cpf[$i]) * (10 - $i);
        }

        $remainder = 11 - ($sum % 11);
        if ($remainder >= 10) {
            $remainder = 0;
        }

        if ($remainder != $cpf[9]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += intval($cpf[$i]) * (11 - $i);
        }

        $remainder = 11 - ($sum % 11);
        if ($remainder >= 10) {
            $remainder = 0;
        }

        if ($remainder != $cpf[10]) {
            return false;
        }

        return true;
    }

    public function login (Request $Request)
    {
        try {
            // () Validate User Input Request Data
            $validatedUser = Validator::make($Request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            if ($validatedUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Request Input validation error',
                    'errors' => $validatedUser->errors()
                ], 401);
            }

            // () Authenticate user
            if (!Auth::attempt(['email' => $Request->email, 'password' => $Request->password])) {
                return response()->json([
                    'status' => false,
                    'message' => 'The provided credentials are incorrect or not exists.',
                    'errors' => $validatedUser->errors()
                ], 401);
            }

            // = Get Authenticated User
            $user = $Request->user();

            // () Create User Token
            $token = $user->createToken($Request->email)->plainTextToken;

            // : Return Bearer Token
            return response()->json([
                'status' => true,
                'message' => 'User logged in successfully!',
                'token' => $token
            ]);
        } catch (Throwable $Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.',
                #'debug' => $Throwable->getMessage()
            ], 500);
        }
    }

    public function logout (Request $Request)
    {
        try {
            // () Revoke User Token
            $Request->user()->currentAccessToken()->delete();

            return response()->json([
                'status' => true,
                'message' => 'User successfully logged out'
            ], 200);
        } catch (Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.'
            ], 500);
        }
    }
}
