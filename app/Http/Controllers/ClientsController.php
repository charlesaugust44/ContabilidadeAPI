<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientModification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateInterval;

class ClientsController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $client = Client::query()->create($request->all());

        $clientModification = new ClientModification();
        $clientModification->client_id = $client->id;
        $clientModification->user_id = $user->id;
        $clientModification->type = ClientModification::CREATE;
        $clientModification->save();

        return response(null, 201);
    }

    public function read($id)
    {
        $client = Client::query()->find($id);

        if ($client === null)
            return response(null, 404);

        return response()->json($client);
    }

    public function readAll()
    {
        $clients = Client::all();

        if (count($clients) == 0)
            return response(null, 204);

        return response()->json($clients);
    }

    public function readAllDeleted()
    {
        $clients = Client::all()->where('deleted', true);

        if (count($clients) == 0)
            return response(null, 204);

        return response()->json($clients);
    }

    public function readAllNonDeleted()
    {
        $clients = Client::all()->where('deleted', false);

        if (count($clients) == 0)
            return response(null, 204);

        return response()->json($clients);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $client = Client::query()->find($id);

        if ($client == null || $client->deleted == true)
            return response(null, 404);

        $old = $client->toJson();

        $client->name = $request->input('name');
        $client->address = $request->input('address');
        $client->save();

        $clientModification = new ClientModification();
        $clientModification->client_id = $client->id;
        $clientModification->user_id = $user->id;
        $clientModification->type = ClientModification::UPDATE;
        $clientModification->changes = json_encode("{old:" . $old . ",new:" . $client->toJson() . "}");
        $clientModification->save();

        return response(null, 204);
    }

    public function delete($id)
    {
        $user = Auth::user();
        $client = Client::query()->find($id);

        if ($client == null || $client->deleted == true)
            return response(null, 404);

        $clientModification = new ClientModification();
        $clientModification->client_id = $client->id;
        $clientModification->user_id = $user->id;
        $clientModification->type = ClientModification::DELETE;
        $clientModification->save();

        $client->deleted = true;
        $client->save();

        return response(null, 204);
    }
    /*

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
    }*/
}
