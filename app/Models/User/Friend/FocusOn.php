<?php

namespace App\Models\User\Friend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FocusOn extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    use HasFactory;
}
