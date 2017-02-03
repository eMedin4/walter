<?php

namespace App\Classes;

use App\Classes\Format;
use App\Repositories\ScrapRepository;

class Movistar 
{

	private $repository;
	private $showRepository;
	private $format;

	public function __Construct(ScrapRepository $repository, Format $format)
	{
		$this->repository = $repository;
		$this->format = $format;
		set_time_limit(0);
	}

	public function start($crawler, $client, $channelCode, $date)
	{
		$channel = $this->format->channelCode($channelCode);

		//RECORREMOS LAS FILAS PARA UN CANAL Y UN DIA
		$result = $crawler->filter('li.fila')->each(function($row, $i) use($client, $channelCode, $channel, $date) {

			//COMPROBAMOS SI ES CINE
			if ($row->filter('li.genre')->text() == 'Cine'){

				$title = trim($row->filter('li.title')->text());
				$id = $this->format->movistarId($row->filter('a')->attr('href'));
				$time = $row->filter('li.time')->text();
				$datetime = $this->format->movistarDate($time, $date);
				$splitDay = $this->format->splitDay($date);

				//SI LA HORA DE LA PELICULA ES ANTES DE LAS 6:00 (SPLITTIME) Y LA FILA DE LA TABLA ES DESPUES DE LA FILA 6, AÑADIMOS UN DÍA
				if ($datetime < $splitDay && $i > 6) {
					$datetime = $datetime->addDay();
				}

				//ANULAMOS SI EL TITULO COINCIDE CON FRASES BANEADAS
				foreach(config('movies.moviesTvBan') as $ban) {
					if (strpos($title, $ban) !== FALSE) {
						return;
					}
				}

				//BORRAMOS PALABRAS BANEADAS DEL TITULO
				$title = str_replace(config('movies.wordsTvBan'), '', $title);

				//BUSCAMOS EN VERIFICADAS MANUALMENTE
				if (array_key_exists($title, config('movies.moviesTvVerified'))) {
					$this->repository->addMovistar(config('movies.moviesTvVerified')[$title], $datetime, $channelCode, $channel);
					return;
				}

				//BUSCAMOS 1 COINCIDENCIA POR TITULO EXACTO
				$movie = $this->repository->searchByTitle($title);
				if ($movie) {
					$this->repository->addMovistar($movie->id, $datetime, $channelCode, $channel);
					return;
				}

				//SI NO ENTRAMOS A LA FICHA
				echo '<br>entramos en la ficha de ' . $title;
				$link = $row->filter('a')->link();
				$page = $client->click($link);
				//ALGUNAS FICHAS DE 'CINE CUATRO', 'CINE BLOCKBUSTER',.. SIN PELICULA, NO TIENEN AÑO EN LA FICHA, ANULAMOS
				if ($page->filter('p[itemprop=datePublished]')->count() == 0) {
					return;
				}
				$year = $page->filter('p[itemprop=datePublished]')->attr('content');
				$original = $this->format->getElementIfExist($page, '.title-especial p', NULL);

				//BUSCAMOS 1 COINCIDENCIA
				$movie = $this->repository->searchByYear($title, $original, $year);

				if ($movie) {
					$this->repository->addMovistar($movie->id, $datetime, $channelCode, $channel);
				} else {
					echo '<br><span style="color:orange">' . $title . ' ' . $channel . ' ' . $datetime . '. Sin resultados</span>';
					\Log::info($title . ' ' . $channel . ' ' . $datetime . '. Sin resultados');	
				}
			}
		});
	}
}
