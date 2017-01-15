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


    /*
        ATTRIBUTES
    */

    //MEDIA ENTRE LAS NOTAS DISPONIBLES
    public function getAverageAttribute()
    {
        if($this->im_rat != -1) {
            if($this->rt_rat != -1) {
                return intval(($this->im_rat + $this->rt_rat / 10 + $this->fa_rat) / 3);
            }
            return intval(($this->im_rat + $this->fa_rat) / 2);
        }
        return intval($this->fa_rat);
    }
}
