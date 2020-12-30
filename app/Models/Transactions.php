<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transactions extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';
    protected $primaryKey = 'idtransactions';

    protected $fillable = [
        'idgradetotals', 'type', 'amount', 'idtransfers'
    ];
}
