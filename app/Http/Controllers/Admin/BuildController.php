<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\BuildRepository;
use App\Classes\Scraper;
use App\Classes\Format;
use App\Mail\ReportScraper;

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class BuildController extends Controller
{

	private $repository;
	private $scraper;
	private $format;

	public function __Construct(BuildRepository $repository, Scraper $scraper, Format $format)
	{
		$this->repository = $repository;
		$this->scraper = $scraper;
		$this->format = $format;
	}


    public function getAll() {
        $client = new Client();
        $results = [];
        $filterScore = 100;
        $configTmdb = $this->configTmdb();
        $genres = $this->getGenres();

        //DATOS PARA PAGINAR: PAGINA INICIO Y NÚMERO DE PÁGINAS
        $crawler = $client->request('GET', 'https://www.filmaffinity.com/es/allfilms_0-9_1.html');
        $numOfPages = 100; //si es 0 cojerá el numero de paginas totales de la letra

        //¿CUANTAS PAGINAS RECORREMOS?
        if ($numOfPages == 0) {
            $count = $crawler->filter('.pager a')->last()->previousAll()->text();
        } else {
            $count = $numOfPages;
        }

        for ($i=1; $i<=$count; $i++) {
            //SCRAPEAMOS PAGINA Y AÑADIMOS A ARRAY
            $results = array_merge($results, $this->scraper->scrapAll($client, $crawler, $filterScore, $configTmdb));
            //AVANZAMOS PÁGINA
            if ($crawler->filter('.pager .current')->nextAll()->count()) {
                $upPage = $crawler->filter('.pager .current')->nextAll()->link();
                $crawler = $client->click($upPage);             
            }
        }

        $this->sendToRepository($results);

        //enviamos reporte
        Mail::to('elann2013@gmail.com')->send(new ReportScraper($results));
        dd($results);


    }

    public function getTheatres()
    {
    	$client = new Client();
    	$results = [];
    	$configTmdb = $this->configTmdb();
    	$genres = $this->getGenres();

    	//BORRAMOS LA TABLA
    	

    	//PELICULAS EN CARTELERA
    	$crawler = $client->request('GET', 'http://www.filmaffinity.com/es/rdcat.php?id=new_th_es'); 

    	//RECORREMOS TODAS LAS SECCIONES
    	$count = $crawler->filter('#main-wrapper-rdcat')->count();
		for ($i=5; $i<14; $i++) {
		
			if ($i==0) {
				//LA PRIMERA SECCION ES ESTRENOS SON 2 PUNTOS
				$order = 2;
				//LA FECHA DE ESTRENOS LA GUARDAMOS COMO FECHA DE CARTELERA
				$dateSection = $this->format->date($crawler->filter('#main-wrapper-rdcat')->eq(0)->filter('.rdate-cat')->text());
				$this->repository->setParams('Cartelera', NULL, $dateSection);
			} else { 
				//SI NO ES ESTRENO LE DAMOS 3 PUNTOS
				$order=3; 
			} 

			//SCRAPEAMOS
			$filterScore = 1;	//MINIMO DE VOTOS PARA SCRAPEAR
			$results = array_merge($results, $this->scraper->scrapList($i, $client, $crawler, $order, $filterScore, $configTmdb));
		}

    	/*
    		PRÓXIMOS ESTRENOS
    	*/

    	$crawler = $client->request('GET', 'http://www.filmaffinity.com/es/rdcat.php?id=upc_th_es');

		$count = 2; //CUANTAS SECCIONES?
		$order = 1; //PUNTOS PARA PROXIMOS ESTRENOS
		$filterScore = 200;	//MINIMO DE VOTOS PARA SCRAPEAR

		//SCRAPEAMOS


		$this->sendToRepository($results, $toList = 1, $dateSection);

		//enviamos reporte
		Mail::to('elann2013@gmail.com')->send(new ReportScraper($results));
		dd($results);
    }


    public function sendToRepository($results, $toList = NULL, $dateSection = NULL)
    {
        if ($toList) {
            $this->repository->setDescriptionList($dateSection);
        }
        
    	foreach($results as $result) {
    		if ($result['boolean']) {
    			$movie = $this->repository->storeFilm($result['state']);
                if ($toList) {
    			     $this->repository->setMainList($movie->id, $result['state']['release'], $result['state']['state']);
                }
    		}
    	}
    }


    public function configTmdb()
    {
        $api = file_get_contents('http://api.themoviedb.org/3/configuration?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c');
        $results = json_decode($api, true);
        return [
            'poster' => $results['images']['base_url'] . $results['images']['poster_sizes'][3],
            'profile' => $results['images']['base_url'] . $results['images']['profile_sizes'][1],
        ];
    }

    public function getGenres()
    {
    	$api = file_get_contents('https://api.themoviedb.org/3/genre/movie/list?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&language=es-ES');
    	$this->repository->setGenres(json_decode($api, true));
    }


    public function getMojo()
    {
    	
    	//mojo está protegido de scrapers, por lo que usamos curl para recibir el contenido de la pagina
    	$ch = curl_init("http://www.boxofficemojo.com/intl/spain/?yr=2016&wk=40&currency=local&p=.htm");
        
        //NOS SALTAMOS LA PROTECCION DE MOJO
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		//AISLAMOS EL CONTENIDO QUE NOS INTERESA
		preg_match_all('(table border(.*)Advertising)siU', $result, $matches1);
       	$crawler = new Crawler($matches1[1][0]);

        $this->scraper->scrapMojo($crawler);
    }

    public function checkPoster()
    {
        $this->repository->checkPoster();
    }


}
