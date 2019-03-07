<?php

namespace App\Http\Controllers;

use App\Client;
use App\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DebtsController extends Controller
{
    public function create(Request $request)
    {
        $client = Client::query()->find($request->input('client_id'));

        if ($client === null)
            return response("Client does not exist", 400);

        $user = Auth::user();
        $attributes = array_merge(["user_id" => "$user->id"], $request->all());
        Debt::query()->create($attributes);

        return response(null, 201);
    }

    public function readAllByClient($id)
    {
        $debt = Debt::all()->where('client_id', $id);

        if (count($debt) == 0)
            return response(null, 204);

        return response()->json($debt);
    }

    public function delete($id)
    {
        $debt = Client::query()->find($id);

        if ($debt == null)
            return response(null, 404);

        $debt->delete();

        return response(null, 204);
    }
}
