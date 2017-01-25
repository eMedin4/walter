<?php

namespace App\Classes;

use App\Classes\Format;
use App\Repositories\BuildRepository;
use App\Repositories\ShowRepository;


class Movistar 
{

	private $repository;
	private $showRepository;
	private $format;

	public function __Construct(BuildRepository $repository, ShowRepository $showRepository, Format $format)
	{
		$this->repository = $repository;
		$this->showRepository = $showRepository;
		$this->format = $format;
		set_time_limit(0);
	}

	public function start($crawler)
	{
		$result = $crawler->filter('.listado-az')->each(function($row) {
			if ($row->filter('.gen-az')->text() == 'Cine'){
				$info = [
					'title' 		=> $row->filter('.tit-az a')->text(),
					'movistar_id' 	=> $this->format->movistarId($row->filter('.tit-az a')->attr('href'))
				];
				return $info;
			}
		});
		$result = array_filter($result);
		dd($result);
	}

}
