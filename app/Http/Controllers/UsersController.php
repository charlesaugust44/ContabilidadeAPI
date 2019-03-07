<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use DateTime;
use DateInterval;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        $user = User::query()->create($request->all());

        $user->password = hash_password($request->input('password'));
        $user->save();

        return response(null, 201);
    }

    public function read($id)
    {
        $user = User::query()->find($id);

        if ($user === null)
            return response(null, 404);

        return response()->json($user);
    }

    public function readAll()
    {
        $users = User::all();

        if (count($users) == 0)
            return response(null, 204);

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        $user = User::query()->find($id);

        if ($user == null)
            return response(null, 404);

        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        if ($old_password !== "") {
            if ($new_password === "")
                return response("Empty new password!", 400);

            if ($user->password === hash_password($old_password)) {
                $user->password = hash_password($new_password);
                $user->token = null;
                $user->secret = null;
            } else
                return response("Old password does not match!", 400);
        }

        $user->name = $request->input('name');
        $user->user = $request->input('user');
        $user->admin = $request->input('admin');
        $user->save();

        return response(null, 204);
    }

    public function delete($id)
    {
        $user = User::query()->find($id);

        if ($user == null)
            return response(null, 404);

        $user->delete();

        return response(null, 204);
    }

    public function token(Request $request)
    {
        $email = $request->input('user');
        $password = hash_password($request->input('password'));

        $user = User::query()
            ->where('user', $email)
            ->where('password', $password)
            ->first();

        if ($user === null)
            return response("Unauthorized", 401);

        $expiration = new DateTime();
        $expiration->add(new DateInterval('P1MT1M'));

        [$token, $secret] = generate_token([
            'iss' => 'localhost',
            'name' => $user->name,
            'user' => $user->user,
            'exp' => $expiration->getTimestamp()
        ]);

        $user->token = $token;
        $user->secret = $secret;
        $user->save();

        return response()->json(['token' => $token]);
    }
}
