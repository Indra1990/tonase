<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogActivities extends Model
{
    use SoftDeletes;
    
    protected $table = 'log_activities';
    protected $primaryKey = 'idlogactivities';

    protected $fillable = [
        'subject', 'url', 'method', 'idusers'
    ];

    public function users()
    {
        return $this->belongsTo('App\Models\User','idusers');
    }
}
