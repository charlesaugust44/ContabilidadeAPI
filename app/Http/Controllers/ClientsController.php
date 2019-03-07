<?php

namespace App\Http\Controllers;

use App\Client;
use App\ClientModification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $clientModification->changes = "{old:" . $old . ",new:" . $client->toJson() . "}";
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
}
