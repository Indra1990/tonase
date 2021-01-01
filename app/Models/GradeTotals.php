<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeTotals extends Model
{
    use SoftDeletes;
    
    protected $table = 'grade_totals';
    protected $primaryKey = 'idgradetotals';

    protected $fillable = [
        'idusers', 'total'
    ];

    public function transaction()
    {
        return $this->hasMany('App\Models\Transactions','idgradetotals','idgradetotals');
    }
}
