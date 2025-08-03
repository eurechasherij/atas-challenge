<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroContact extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'xero_id', 'name', 'email'
    ];
}
