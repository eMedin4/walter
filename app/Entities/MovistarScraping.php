<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class MovistarScraping extends Model
{
    protected $table = 'movistar_scraping';
    public $timestamps = false;

    public function movies()
	{
		return $this->belongsTo(Movie::class);
	}
}
