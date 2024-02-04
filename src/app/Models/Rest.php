<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable= ['user_id', 'attendance_id', 'date', 'rest_start', 'rest_end'];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function previousRest()
    {
        return $this->where('date', '>', $this->date)->orderBy('date', 'desc')->get();
    }
    public function nestRest()
    {
        return $this->where('date', '>', $this->date)->orderBy('date', 'asc')->get();
    }
}
