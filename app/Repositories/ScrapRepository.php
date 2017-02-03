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
use App\Entities\MovistarSchedule;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class ScrapRepository {

    public function addMovistar($movieId, $datetime, $channelCode, $channel)
    {
        $exist = MovistarSchedule::where('time', $datetime)->where('movie_id', $movieId)->first();
        if (!$exist) {
            $schedule = new MovistarSchedule;
            $schedule->time = $datetime;
            $schedule->channel = $channel;
            $schedule->channel_code = $channelCode;
            $schedule->movie_id = $movieId;
            $schedule->save();
        }

    }

    public function resetTVList()
    {
        if (MovistarSchedule::where('time', '<', Carbon::now()->subHour())->count()) {
            MovistarSchedule::where('time', '<', Carbon::now()->subHour())->delete();
        }
    }

    public function searchByTitle($title)
    {
        //BUSCAMOS POR TITULO EXACTO
        $movie = Movie::where('title', $title)->get();
        if ($movie->count() == 1) return $movie->first();

        //BUSCAMOS POR TITULO EXACTO SIN PARÉNTESIS
        if (strpos($title, '(') !== FALSE) { 
            $title = trim(preg_replace("/\([^)]+\)/","",$title));
            $movie = Movie::where('title', $title)->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;
    }

    public function searchByYear($movistarTitle, $movistarOriginal, $movistarYear)
    {
        $cycle = [$movistarYear - 1, $movistarYear + 1];

        //BUSCAMOS POR LIKE
        $movie = Movie::where('title', 'like', '%' . $movistarTitle . '%')
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return $movie->first();

        //SI HAY PARÉNTESIS LOS QUITAMOS Y VOLVEMOS A BUSCAR
        if (strpos($movistarTitle, '(') !== FALSE) { 
            $movistarTitleNoBrackets = trim(preg_replace("/\([^)]+\)/","",$movistarTitle));
            $movie = Movie::where('title', 'like', '%' . $movistarTitleNoBrackets . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO BUSCAMOS POR EXACTO
        $movie = Movie::where('title', $movistarTitle)
            ->whereBetween('year', $cycle)
            ->get();
        if ($movie->count() == 1) return $movie->first();

        if($movistarOriginal && $movistarOriginal != $movistarTitle) {
            //SI NO BUSCAMOS POR ORIGINAL CON LIKE
            $movie = Movie::where('original_title', 'like', '%' . $movistarOriginal . '%')
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();

            //SI NO BUSCAMOS POR ORIGINAL EXACTO
            $movie = Movie::where('original_title', $movistarOriginal)
                ->whereBetween('year', $cycle)
                ->get();
            if ($movie->count() == 1) return $movie->first();
        }

        //SI NO SE ENCUENTRA DEVOLVEMOS NULL
        return NULL;

    }


}
