<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;

class UserController extends Controller
{
    public function index ()
    {
        try {
            $User = auth()->user();
            if ($User->role === 'Client') {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to list users.'
                ], 403);
            }

            $users = User::all();

            return response()->json($users, 200);
        } catch (Throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Some internal error on the server has occurred.'
            ], 500);
        }
    }
}
