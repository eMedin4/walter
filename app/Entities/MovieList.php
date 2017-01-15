<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class MovieList extends Model
{
    protected $table = 'lists';

    public function movies()
    {
    	return $this->belongsToMany(Movie::class, 'list_movie', 'list_id', 'movie_id')->withPivot('order');
    }

    public function moviesByOrder()
    {
    	return $this->belongsToMany(Movie::class, 'list_movie', 'list_id', 'movie_id')->withPivot('order')->orderBy('order');
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function savedToUsers()
    {
        return $this->belongsToMany(User::class, 'list_user', 'list_id', 'user_id');
    }

    /*ACCESSORS*/

    /*CANTIDAD DE ELEMENTOS VACÍOS A RELLENAR HASTA COMPLETAR FILAS DE 7 EN EL LOOP*/
    public function getCountItemAttribute() {
        $countList = $this->movies->count();
        return [
            'remainder' => 7 - $countList,
            'total' => $countList
        ];
    }
}
