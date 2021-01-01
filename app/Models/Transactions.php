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

    public function to_transfer()
    {
        return $this->belongsTo('App\Models\User','idtransfers','idusers');
    }

    public function from_transfer()
    {
        return $this->belongsTo('App\Models\User','idfrom','idusers');
    }
}
