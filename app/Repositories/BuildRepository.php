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
use App\Classes\Images;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class BuildRepository {

	public function setParams($name, $value=NULL, $date=NULL) 
    {
        //SI EXISTE UNA FILA CON EL NOMBRE QUE VAMOS A GUARDAR, ANTES LA BORRAMOS
        $old = Param::where('name', $name);
        if ($old->count() > 0) {
            $old->delete();
        }

        $param = New Param;
        $param->name = $name;
        $param->value = $value;
        $param->date = $date;
        $param->save();
    }

    public function storeFilm($data)
    {
        $movie = Movie::firstOrNew(['fa_id' => $data['faId']]);
        $movie->title            = $data['faTitle'];
        if (!$movie->exists) { /*solo recalculamos slugs para nuevas películas*/
            $movie->slug             = $this->setSlug($data['faTitle']);
        }
        $movie->original_title   = $data['faOriginal'];
        $movie->country          = $data['faCountry'];
        $movie->duration         = $data['faDuration'];
        $movie->review           = $this->setReview($data['faReview'], $data['tmReview']);
        $movie->fa_id            = $data['faId'];
        $movie->tm_id            = $data['tmId'];
        $movie->year             = $data['faYear'];
        $movie->poster           = $data['tmPoster'];
        $movie->imdb_id          = $data['imId'];
        $movie->rt_url           = $data['rtUrl'];
        $movie->fa_rat           = $data['faRat'];
        $movie->fa_rat_count     = $data['faCount'];
        $movie->im_rat           = $data['imRat'];
        $movie->im_rat_count     = $data['imCount'];
        $movie->rt_rat           = $data['rtRat'];
        $movie->rt_rat_count     = $data['rtCount'];
        $movie->revenue          = $data['tmRevenue'];
        $movie->budget           = $data['tmBudget'];
        $movie->save();

        $this->storeCharacters($movie, $data['tmCredits']);
        $this->storeGenres($movie, $data['tmGenres']);
        $this->storeCritics($movie, $data['faCritics']);

        return $movie;
    }

    public function storeCharacters($movie, $characters)
    {
        //BORRAMOS DATOS PREVIOS DE LA TABLA PIVOTE
        $movie->characters()->detach();

        foreach($characters['cast'] as $i => $cast) {

            //GUARDAMOS ACTOR
            $character = Character::firstOrNew(['id' => $cast['id']]);
            $character->id             = $cast['id'];
            $character->name           = $cast['name'];
            $character->department     = 'actor';
            $character->photo          = $cast['profile_path'];
            $character->save();
            //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
            $sync[$cast['id']] = ['order' => $cast['order']];
        }

        foreach($characters['crew'] as $i => $crew)
        {
            //SOLO GURADAMOS DIRECTOR
            if($crew['department'] == 'Directing') {
                $character = Character::firstOrNew(['id' => $crew['id']]);
                $character->id             = $crew['id'];
                $character->name           = $crew['name'];
                $character->department     = 'director';
                $character->photo          = $crew['profile_path'];
                $character->save();
                //GUARDAMOS EN ARRAY LISTO PARA SINCRONIZAR DESPUES
                $sync[$crew['id']] = ['order' => -1];
            }
        }

        //SINCRONIZAMOS TABLA PIVOTE
        if (isset($sync)) {
            $movie->characters()->sync($sync);
        }
    }

    public function storeGenres($movie, $genres)
    {
        //EXTRAEMOS LA COLUMNA ID DEL ARRAY GENRES
        $values = array_column($genres, 'id');
        if (in_array(10769, $values)) {
            $filter = array(10769);
            $values = array_diff($values, $filter);
        }
        //SINCRONIZAMOS, LOS QUE NO ESTEN EN VALUES SE ELIMINARÁN
        $movie->genres()->sync($values);
    }

    public function storeCritics($movie, $critics)
    {
        if ($critics) {
            //BORRAMOS CRITICAS PREVIAS
            $movie->critics()->delete();
            //GUARDAMOS LAS NUEVAS
            foreach ($critics as $value) {
                if (strlen($value['author']['name']) < 40 AND strlen($value['author']['alias']) < 40) { //puntualmente alguna critica llega mal screpeada con texto en el ext_author, la saltamos
                    $critic = new Critic;
                    $critic->text           = $value['text'];
                    $critic->ext_author     = $value['author']['name'];
                    $critic->ext_media      = $value['author']['alias'];
                    $critic->movie_id       = $movie->id;
                    $critic->user_id        = 2;
                    $critic->save();     
                }
            }
        }
    }

    public function setSlug($slug)
    {
        $slug = str_slug($slug, '-');
        $count = Movie::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        /*if ($slug == 'walker') dd($count);*/
        return $count ? "{$slug}-{$count}" : $slug;
    }

    public function setReview($faReview, $tmReview)
    {
        return empty($tmReview) ? $faReview : $tmReview;
    }

    public function setGenres($api)
    {
        foreach($api['genres'] as $genre) {
            Genre::firstOrCreate($genre);
        }
    }

    public function resetMainList()
    {
        Theatre::truncate();
        $list = MovieList::find(1);
        $list->movies()->detach();
    }

    public function setDescriptionList($date)
    {
        echo $date;
        $list = MovieList::find(1);
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $list->description = "Cartelera de cines en España, semana del " . $date->formatLocalized('%e de %B de %Y');
        $list->save();        
    }

    public function setMainList($movieId, $release, $order)
    {
        $list = MovieList::find(1);
        $list->movies()->attach($movieId, ['order' => $order]);

        $theatre = new Theatre;
        if ($order == 1) { 
            $name = 'Próximo estreno';
        } elseif ($order == 2) {
            $name = 'Estreno';
        } else {
            $name = '';
        }
        $theatre->name = $name;
        $theatre->date = $release;
        $theatre->movie_id = $movieId;
        $theatre->save();
    }


    public function updateMojo($id, $mojo, $state)
    {
        //COJEMOS LA PELICULA A ACTUALIZAR
        $theatre = Theatre::where('movie_id', $id)->first();
        if ($theatre) {
            $theatre->order = $state + $mojo['rank'];
            $theatre->mojo_rank = $mojo['rank'];
            $theatre->mojo_previous = $mojo['previous'];
            $theatre->mojo_weekend = $mojo['weekend'];
            $theatre->mojo_total = $mojo['total'];
            $theatre->mojo_weeks = $mojo['weeks'];
            $theatre->save();   
            return true;         
        }
        return false;
    }


    public function cleanOrder()
    {
        Theatre::whereNull('mojo_weeks')->update(['order' => 200]);
    }

    public function comment($id, $text)
    {
        $comment = New Comment;
        $comment->text = $text;
        $comment->user_id = Auth::user()->id;
        $comment->movie_id = $id;
        $comment->save();
    }

    public function newList($name, $movie, $ordered, $description)
    {
        if (MovieList::where([['name', '=', $name], ['user_id', '=', Auth::user()->id]])->count() > 0) {
            return ['state' => false, 'error' => 'duplicate', 'message' => 'Esta lista ya existe'];
        }
        $sum = MovieList::where('user_id', '=', Auth::user()->id)->count();
/*        if ($sum > 9) {
            return ['state' => false, 'error' => 'full', 'message' => 'No puedes crear mas de 10 listas'];
        } */
        $list = New MovieList;
        $list->name = $name;
        if ($description) $list->description = $description;
        $list->ordered = $ordered;
        $list->user_id = Auth::user()->id;
        $list->save();
        if ($movie) { //si estamos añadiendo a la vez alguna película $movie será su id, sino será 0
            $list->movies()->attach($movie, ['order' => 0]);
        }
        return ['state' => true, 'name' => $name, 'sum' => $sum, 'id' => $list->id];
    }

    public function addList($listId, $movie)
    {
        $list = MovieList::find($listId);

        $max = $list->movies()->max('list_movie.order');
        $list->movies()->attach($movie, ['order' => $max + 1]);

        if ($list->movies->count() == 4) {
            $firstMovies = $list->moviesByOrder->pluck('poster')->take(4);
            $images = New Images;
            $images->setImageList($firstMovies, $list->id);     
        }

        $list->touch();
    }

    public function updateList($listId, $movies, $name, $description)
    {
        $list = MovieList::find($listId);
        $list->name = $name;
        $list->description = $description;
        $list->save();

        $i = 1;
        foreach($movies as $movie) {
            $order[$movie] = ['order' => $i];
            $i++;
        } //esto da el formato para sync: 3 => ['order' => 1],...

        $list->movies()->sync($order);

        if ($list->movies->count() >= 4) {
            $firstMovies = $list->moviesByOrder->pluck('poster')->take(4);
            $images = New Images;
            $images->setImageList($firstMovies, $list->id);     
        }
    }

    public function deleteList($id)
    {   
        $list = MovieList::find($id);
        $list->savedToUsers()->detach();
        $list->movies()->detach();
        $del = MovieList::where([['id', '=', $id],['user_id', '=', Auth::id()]])->delete();
        return $del ? 'deleted' : 'error';

    }

    public function deleteMovieList($listId, $movieId)
    {
        $list = MovieList::find($listId);
        $del = $list->movies()->detach($movieId);
        return $del ? 'deleted' : 'error';
    }

    public function addToMylists($listId)
    {
        $list = MovieList::find($listId);  
        if (!$list->savedToUsers()->count()) {
            $list->savedToUsers()->attach(Auth::id());   
        }
    }

    public function delFromMylists($listId)
    {
        $list = MovieList::find($listId);  
        if ($list->savedToUsers()->count()) {
            $list->savedToUsers()->detach(Auth::id());   
        }
    }

    public function checkPoster()
    {
        $movies = Movie::select('id', 'poster')->get();
        foreach ($movies as $movie) {
            $updateMovie = Movie::find($movie->id);
            if (file_exists(public_path() . '/assets/posters/medium' . $movie->poster)) {
                $updateMovie->check_poster = 1;
            } else {
                $updateMovie->check_poster = 0;
                echo $movie->id . ' no se encuentra <br>';
            }
            $updateMovie->save();
        }
    }


}
