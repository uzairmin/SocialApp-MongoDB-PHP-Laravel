<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Friend extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'user1_id',
        'user2_id'
    ]; 
}
