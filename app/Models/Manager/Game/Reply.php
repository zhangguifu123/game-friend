<?php

namespace App\Models\Manager\Game;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    use HasFactory;
}
