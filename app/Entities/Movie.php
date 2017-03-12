<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $guarded = [];

    public function characters()
    {
    	return $this->belongsToMany(Character::class)->withPivot('order');
    }

    public function genres()
    {
    	return $this->belongsToMany(Genre::class);
    }

    public function critics()
    {
    	return $this->hasMany(Critic::class);
    }

    public function lists()
    {
        return $this->belongsToMany(MovieList::class, 'list_movie', 'movie_id', 'list_id');
    }

    public function theatre()
    {
        return $this->hasOne(Theatre::class);
    }

    public function movistarSchedule()
    {
        return $this->hasOne(MovistarSchedule::class);
    }


    /*
        ATTRIBUTES
    */

    //MEDIA ENTRE LAS NOTAS DISPONIBLES
    public function getAverageAttribute()
    {
        if($this->im_rat != -1) {
            if($this->rt_rat != -1) {
                $value = intval(($this->im_rat + $this->rt_rat / 10 + $this->fa_rat) / 3);
                return $this->stars($value);
            }
            $value = intval(($this->im_rat + $this->fa_rat) / 2);
            return $this->stars($value);
        }
        $value = intval($this->fa_rat);
        return $this->stars($value);
    }

    public function stars($value)
    {
        switch (true) {
            case ($value > 8): return 5;
            case ($value > 7): return 4;
            case ($value > 6): return 3;
            case ($value > 5): return 2;
            case ($value > 4): return 1;
            default: return 0;
        }
    }
}
