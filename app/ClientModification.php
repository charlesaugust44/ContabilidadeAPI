<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientModification extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'client_id', 'user_id', 'type', 'changes'
    ];
}