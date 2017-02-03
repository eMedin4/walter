<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MovistarSchedule extends Model
{
    protected $table = 'movistar_schedule';
    protected $dates = ['time'];

    public function movie()
	{
		return $this->belongsTo(Movie::class);
	}

	public function getFormatTimeAttribute()
    {
    	$now = Carbon::now();
    	if ($this->time < $now) {
    		return '<span>hace</span> ' . $this->time->diffInMinutes($now) . '<span> m.</span>';
    	} elseif ($this->time->isToday()) {
    		return $this->time->format('G:i') . ' <span>hoy</span>';
    	} else {
    		return $this->time->format('G:i') . ' <span>' . $this->time->formatLocalized('%a') . '</span>';
    	}
    }
}
