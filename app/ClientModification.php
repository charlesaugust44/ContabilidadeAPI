<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClientModification extends Model
{
    const
        CREATE = 0,
        UPDATE = 1,
        DELETE = 2;
    /**
     * @var array
     */
    protected $fillable = [
        'client_id', 'user_id', 'type', 'changes'
    ];
}