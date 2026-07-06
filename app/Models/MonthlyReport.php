<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    protected $fillable = ['user_id', 'year', 'month', 'report_text'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}