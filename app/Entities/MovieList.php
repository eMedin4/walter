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
    /*CANCELADO PORQUE SOLO SIRVE PARA LISTAS, NO SIRVE PARA RESULTADOS DE BUSQUEDA NI PARA PELICULAS DE ACTORES*/
/*    public function getCountItemAttribute() {
        $countList = $this->movies->count();
        return [
            'remainder' => 7 - $countList,
            'total' => $countList
        ];
    }*/

    public function getAutolinkDescriptionAttribute()
    {
        $str = htmlentities($this->description);
        $attributes = array('rel' => 'nofollow', 'target' => '_blank');
        $str = str_replace(["http://www", "https://www"], "www", $str);
        $attrs = '';

        foreach ($attributes as $attribute => $value) {
            $attrs .= " {$attribute}=\"{$value}\"";
        } 

        $str = ' ' . $str;
        $str = preg_replace('`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i', '$1<a href="$2"'.$attrs.'>$2</a>', $str);
        $str = preg_replace('`([^"=\'>])((www).[^\s<]+[^\s<\.)])`i', '$1<a href="http://$2"'.$attrs.'>$2</a>', $str);
        $str = substr($str, 1);
        return $str;
    }
}
