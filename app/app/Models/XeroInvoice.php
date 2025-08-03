<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class XeroInvoice extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'xero_id', 'number', 'date', 'amount', 'status'
    ];
}
