<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable= ['user_id', 'date', 'work_start', 'work_end', 'break_start', 'break_end'];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
}
