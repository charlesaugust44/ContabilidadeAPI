<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        $user = User::query()->create($request->all());
        return response()->json($user);
    }
}
