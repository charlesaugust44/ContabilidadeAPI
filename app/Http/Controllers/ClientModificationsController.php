<?php

namespace App\Http\Controllers;

use App\ClientModification;

class ClientModificationsController extends Controller
{
    public function readAllByClient($id)
    {
        $modifications = ClientModification::all()->where('client_id', $id);

        if (count($modifications) == 0)
            return response(null, 204);

        return response()->json($modifications);
    }
}
