<?php

namespace App\Models\Manager;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manager extends Model
{
    protected $guarded = ['id','created_at','updated_at'];
    use HasFactory;
}
