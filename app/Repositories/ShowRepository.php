<?php

namespace App\Repositories;

use App\Entities\Theatre;
use App\Entities\Param;
use App\Entities\Movie;
use App\Entities\Character;
use App\Entities\Critic;
use App\Entities\Comment;
use App\Entities\Genre;
use App\Entities\MovieList;
use App\Entities\User;
use App\Entities\MovistarSchedule;

use Carbon\Carbon;

class ShowRepository {

    public function getParam($name, $column)
    {
        return Param::where('name', $name)->value($column);
    }

    public function movieBySlug($slug)
    {
        return Movie::where('slug', $slug)->firstOrFail();
    }

    public function getMovieByFa($faId)
    {
    	return Movie::where('fa_id', $faId)->first();
    }

    public function searchMojo($original)
    {
    	$result = Movie::where('original_title', $original)->first();
    	if (count($result)) {
    		return $result;
    	}
    	return NULL;
    }

    public function home()
    {
        return Theatre::with('movie')->orderBy('date', 'desc')->get();
        //MovieList::where('id', 1)->with(['movies.theatre'])->first();
    }

    public function tv()
    {
        return MovistarSchedule::with('movie')->where('time', '>', Carbon::now()->subHour())->orderBy('time')->simplePaginate(60);
        //MovieList::where('id', 2)->with('movies', 'movies.movistarSchedule')->first();
    }

    public function character($id)
    {
        return Character::with(['movies' => function($q) {
            $q->orderBy('year', 'desc');
        }])->find($id);
    }

    public function comments($id)
    {
        $critics = Critic::where('movie_id', $id)->get();
        $comments = Comment::where('movie_id', $id)->with('user')->get();
        return $critics->merge($comments)->sortByDesc('created_at');
    }

    public function listsByUser($user)
    {
        return MovieList::where('user_id', $user)->with('movies')->orderBy('updated_at', 'desc')->get();
    }

    public function userLists($id, $name)
    {
        $user = User::where('id', $id)->with(['lists' => function($q) {
                $q->orderBy('updated_at', 'desc');
            }, 'lists.movies' => function($q) {
                $q->orderBy('order');
            }
        ])->first();
        if (str_slug($user->name) != $name) {
            abort(404);
        }
        return $user;
    }

    public function userSavedLists($id, $name)
    {
        $user = User::where('id', $id)->with(['savedLists' => function($q) {
                $q->orderBy('updated_at', 'desc');
            }, 'savedLists.movies' => function($q) {
                $q->orderBy('order');
            }
        ])->first();
        if (str_slug($user->name) != $name) {
            abort(404);
        }
        return $user;
    }

    public function userCritics($id, $name)
    {
        $user = User::where('id', $id)->with('comments.movie')->first();
        if (str_slug($user->name) != $name) {
            abort(404);
        }
        return $user;
    }

    public function showList($id, $name)
    {
        $list = MovieList::where('id', $id)->with(['movies' => function($q) {
            $q->orderBy('order'); //para ordenar movies por order : Constraining Eager Loads
        }])->first();
        if (str_slug($list->name) != $name) {
            abort(404);
        }
        return $list;

    }

    public function liveSearch($string)
    {
        return Movie::whereRaw("MATCH(title,original_title) AGAINST(? IN BOOLEAN MODE)", 
                array($string))->take(10)->get();
    }

    public function normalSearch($string)
    {
        return Movie::whereRaw("MATCH(title,original_title) AGAINST(? IN BOOLEAN MODE)", 
                array($string))->take(50)->get();
    }

}
