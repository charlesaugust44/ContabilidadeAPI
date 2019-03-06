<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'value','writeoff','client_id'
    ];
}