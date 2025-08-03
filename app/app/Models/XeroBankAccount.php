<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroBankAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'xero_id', 'name', 'balance'
    ];
}
