<?php

namespace App\Entities;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function lists()
    {
        return $this->hasMany(MovieList::class);
    }

    public function savedLists()
    {
        return $this->belongsToMany(MovieList::class, 'list_user', 'user_id', 'list_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /*ACCESSORS*/

    /*CANTIDAD DE ELEMENTOS VACÍOS A RELLENAR HASTA COMPLETAR FILAS DE 7 EN EL LOOP*/
    public function getCountItemAttribute() {
        $countList = $this->lists->count();
        return [
            'remainder' => 7 - $countList,
            'total' => $countList
        ];
    }

    public function getCountSavedItemAttribute() {
        $countList = $this->savedLists->count();
        return [
            'remainder' => 7 - $countList,
            'total' => $countList
        ];
    }


}
