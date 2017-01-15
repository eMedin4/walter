<?php

namespace App\Classes;

use App\Classes\Format;
use App\Repositories\BuildRepository;
use App\Repositories\ShowRepository;
use Image;


class Scraper 
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



	public function scrapList($i, $client, $crawler, $order, $filterScore, $configTmdb)
	{
		//SELECCIONAMOS LA SECCIÓN QUE CORRESPONDA Y LA FECHA
		$element = $crawler->filter('#main-wrapper-rdcat')->eq($i);
		$dateSection = $this->format->date($element->filter('.rdate-cat')->text());

		//SI SE TRATA DE PROXIMOS ESTRENOS
		if($order == 1) {
			if( strtotime($dateSection) > strtotime('+7 day') ) {
				return;
			}
		}

		//RECORREMOS LAS PELÍCULAS DE LA SECCION
		$results = $element->filter('.movie-card')->each(function($element) use($client, $filterScore, $configTmdb, $dateSection, $order) {
			return $this->scrapCard($element, $client, $filterScore, $configTmdb, $dateSection, $order);
		});	

		return $results;
	}

	public function scrapAll($client, $crawler, $filterScore, $configTmdb)
	{
		//RECORREMOS LAS PELÍCULAS DE LA PÁGINA
		$results = $crawler->filter('.movie-card')->each(function($element) use($client, $filterScore, $configTmdb) {
			return $this->scrapCard($element, $client, $filterScore, $configTmdb);
		});	

		return $results;
	}

	public function scrapCard($element, $client, $filterScore, $configTmdb, $dateSection = NULL, $order = NULL)
	{
			$href   = $element->filter('.mc-title > a'); 
			$id 	= $this->format->faId($href->attr('href'));
			$title  = $element->filter('.mc-title > a')->text(); 
			$score 	= $element->filter('.avgrat-box')->text();
			$countScore = $this->format->integer($this->format->getElementIfExist($element, '.ratcount-box', 0));


			//ANULAMOS SI EL NUMERO DE VOTOS NO LLEGA AL MINIMO O SI ES TV/C
			if ($score == '--' OR $countScore < $filterScore OR preg_match('(\(Serie de TV\)|\(C\))', $title)) { 
				//PELÍCULA NO VÁLIDA
				return ['boolean' => 0, 'title' => $title, 'state' => 'Película no válida: votos: ' . $countScore . ' Título: ' . $title];
			} 

			//ANULAMOS SI ESTÁ EN BLACKLIST
			if (in_array($id, config('movies.unavailable'))) {
				//PELÍCULA NO VÁLIDA
				return ['boolean' => 0, 'title' => $title, 'state' => 'Esta película está en blacklist' ];
			}

			//SI ES VÁLIDA HACEMOS CLICK
			$crawler = $client->click($href->link());

			//SCRAPEAMOS LA PELÍCULA
			$scrapMovie = $this->scrapMovie($crawler, $configTmdb);
			$scrapMovie['scraper']['release'] = $dateSection;
			$scrapMovie['scraper']['state'] = $order;

			//SI DA ERROR ESCRIBIMOS EN LOG
			if ($scrapMovie['response'] == false) {
				return ['boolean' => 0, 'title' => $title, 'state' => $scrapMovie['error'] ];
			}

			//SI ESTÁ TODO OK DEVOLVEMOS EL ARRAY
			return ['boolean' => 1, 'title' => $title, 'state' => $scrapMovie['scraper'] ];
	}



	public function scrapMovie($crawler, $configTmdb)
	{
		//SCRAPEAMOS FICHA DE FILMAFFINITY, DEVUELVE UN ARRAY CON LOS DATOS DE FA
		$scrapFa = $this->scrapFa($crawler);
		if (isset($scrapFa['error'])) {
			return [
				'response' => false,
				'error' => $scrapFa['error']
			];
		}

		//LLAMADA N.1 AL API DE THEMOVIEDB, DEVUELVE EL ID DE LA PELICULA O ERROR
		$tmdbId = $this->searchTmdbId($scrapFa['faId'], $scrapFa['faTitle'], $scrapFa['faOriginal'], $scrapFa['faYear']);
		if (isset($tmdbId['error'])) {
			return [
				'response' => false,
				'error' => $tmdbId['error']
			];
		}

		//LLAMADA N.2 AL API DE THEMOVIEDB, DEVUELVE EL ARRAY DE INFO
		$scrapTmdb = $this->scrapTmdb($tmdbId['id']);
		if (isset($scrapTmdb['error'])) {
			return [
				'response' => false,
				'error' => $scrapTmdb['error']
			];
		}

		//LLAMADA N.3 AL API DE THEMOVIEDB, DESCARGA POSTERS
		$this->imagesTmdb($scrapTmdb['tmPoster'], $configTmdb['poster']);

		//LLAMADA N.4 AL API DE THEMOVIEDB, DESCARGA IMAGENES DE ACTORES
		$this->profilesTmdb($scrapTmdb['tmCredits'], $configTmdb['profile']);

		//LLAMADA AL API DE OMDBAPI
		$scrapImdb = $this->scrapImdb($scrapTmdb['imId']);
		if (isset($scrapImdb['error'])) {
			return [
				'response' => false,
				'error' => $scrapImdb['error']
			];
		}

		//SI TODO VA CORRECTO HASTA AQUÍ DEVOLVEMOS EL ARRAY DE DATOS SCRAPEADOS
		return [
			'response' => true,
			'scraper' => array_merge($scrapFa, $scrapTmdb, $scrapImdb)
		];
		

	}


	public function scrapFa($crawler)
	{
		if ($crawler->filter('.ntabs a')->count()) {
			$data['faId'] 		= $this->format->faId($crawler->filter('.ntabs a')->eq(0)->attr('href'));
		} else {
			return ['error' => 'no se encuentra ID en el enlace de filmaffinity'];
		}
		$data['faTitle'] 	= $this->format->faTitle($crawler->filter('#main-title span')->text());
		$data['faRat'] 		= $this->format->float($crawler->filter('#movie-rat-avg')->text());
		$data['faCount'] 	= $this->format->integer($crawler->filter('#movie-count-rat span')->text());

		//CONSTRUIMOS ARRAY CON LOS DATOS DE LA TABLA (NO TIENEN IDS)
        $table = $crawler->filter('.movie-info dt')->each(function($element) {
            return [$element->text() => $element->nextAll()->text()];
        });
        //PASAMOS DE ARRAY DE ARRAYS A ARRAY
        foreach ($table as $key => $value) { 
            $result[key($value)] = current($value);
        }

        if (array_key_exists('Año', $result) AND isset($result['Año']) AND !empty($result['Año'])) {
        	$data['faYear'] = $this->format->integer($result['Año']);
        } else {
        	$data['faYear']	= 0;
        	$data['error'][] = 'error al seleccionar el año en Filmaffinity';
        }

        if (array_key_exists('Título original', $result) AND is_string($result['Título original'])) {
        	$data['faOriginal'] = $this->format->faOriginal($result['Título original']);
        } else {
        	$data['faOriginal']	= '';
        	$data['error'][] = 'error al seleccionar el título original en Filmaffinity';
        }    

        if (array_key_exists('País', $result) AND is_string($result['País'])) {
        	$data['faCountry'] 	= $this->format->cleanData($result['País']);
        } else {
        	$data['faCountry']	= '';
        	$data['error'][] = 'error al seleccionar el país en Filmaffinity';
        }        

        if (array_key_exists('Duración', $result) AND isset($result['Duración']) AND !empty($result['Duración'])) {
        	$data['faDuration'] = $this->format->Integer($result['Duración']);
        } else {
        	$data['faDuration']	= 0;
        	$data['error'][] = 'error al seleccionar la duración en Filmaffinity';
        }
        
        if (array_key_exists('Sinopsis', $result) AND is_string($result['Sinopsis'])) {
        	$data['faReview'] 	= $this->format->faReview($result['Sinopsis']);
        } else {
        	$data['faReview']	= '';
        	$data['warning'][] = 'error al seleccionar la sinopsis en Filmaffinity';
        }

        if (array_key_exists('Críticas', $result) AND isset($result['Críticas']) AND !empty($result['Críticas'])) {
        	$data['faCritics'] 	= $this->format->faCritics($result['Críticas']);
        } else {
        	$data['faCritics'] 	= NULL;
        }

        return $data;
	}


	public function searchTmdbId($faId, $faTitle, $faOriginal, $faYear)
	{

		if (array_key_exists($faId, config('movies.verified'))) {
			return ['id' => config('movies.verified')[$faId]]; //ID DE VERIFICADAS
		}

		$search = $this->apiTmdbId($faTitle, $faYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}

		$search = $this->apiTmdbId($faOriginal, $faYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}
		
		$fwYear = $faYear + 1;
		$search = $this->apiTmdbId($faTitle, $fwYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}

		$search = $this->apiTmdbId($faOriginal, $fwYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}

		$frYear = $faYear - 1;
		$search = $this->apiTmdbId($faTitle, $frYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}

		$search = $this->apiTmdbId($faOriginal, $frYear);
		if ($search['total_results']) {
			return ['id' => $search['results'][0]['id']];
		}

    	$data['error'][] = $faTitle . ' (' . $faId . ') no se encuentra en TheMovieDB';
    	return $data;

	}


	public function apiTmdbId($string, $year)
	{
		$api = file_get_contents('https://api.themoviedb.org/3/search/movie?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&query=' . urlencode($string) . '&year=' . $year . '&language=es');
		return json_decode($api, true);	
	}


	public function scrapTmdb($id)
	{
		$api = file_get_contents('https://api.themoviedb.org/3/movie/' . $id . '?api_key=2d6ee6298dd2dc10ef74cc25e1b0fc7c&language=es&append_to_response=credits,images&include_image_language=es,en,null');
		$result = json_decode($api, true);

		$data['tmTitle'] = $result['title'];
		$data['tmOriginal'] = $result['original_title'];
		$data['tmId'] = $result['id'];
		$data['tmRevenue'] = $result['revenue'];
		$data['tmBudget'] = $result['budget'];
		$data['tmCredits'] = $result['credits'];
		$data['tmGenres'] = $result['genres'];

		if (isset($result['imdb_id']) AND !empty($result['imdb_id'])) {
			$data['imId'] = $result['imdb_id'];
		} else {
			$data['imId'] = 0;
			$data['warning'][] = 'no existe Id de IMBD';
		}

		if (isset($result['overview']) AND !empty($result['overview'])) {
			$data['tmReview'] = $result['overview'];
		} else {
			$data['tmReview'] = '';
		}

		if (isset($result['poster_path']) AND !empty($result['poster_path'])) {
			$data['tmPoster'] = $result['poster_path'];
		} else {
			$data['error'][] = 'no hay poster en TheMovieDB';
		}

		return $data;
	}


	public function imagesTmdb($poster, $configTmdb)
	{
		if ($poster) {
			$getFile = $configTmdb . $poster; //si no funciona meter aqui file_get_containts
			try {
				Image::make($getFile)->resize(320, 480)->save(public_path() . '/assets/posters/large' . $poster);
				Image::make($getFile)->resize(166, 249)->save(public_path() . '/assets/posters/medium' . $poster);
	            Image::make($getFile)->resize(30, 45)->save(public_path() . '/assets/posters/small' . $poster);				
			}
			catch(\Exception $e) {
			    echo "error al salvar el poster" . $poster;
			}
		}
	}


	public function profilesTmdb($credits, $configTmdb)
	{
		foreach ($credits['cast'] as $credit) {
			$getFile = $configTmdb . $credit['profile_path'];
			$setFile = public_path() . '/assets/profiles' . $credit['profile_path'];
			if (!file_exists($setFile)) {
				try {
					Image::make($getFile)->save(public_path() . '/assets/profiles' . $credit['profile_path']);
				}
				catch(\Exception $e) {
				    echo "error al salvar la foto de actor" . $credit['id'];
				}
			}
		}


	}


	public function scrapImdb($id)
	{
        $api = file_get_contents('http://www.omdbapi.com/?i=' . urlencode($id) . '&plot=short&r=json&tomatoes=true');
        $result = json_decode($api, true);	

        if ($result['Response'] == false) {
        	$data['error'][] = 'Tenemos el id de IMBD pero la respuesta de omdbapi da error';
        	return $data;
        }	

        if (isset($result['imdbRating']) AND $result['imdbRating'] != 'N/A') {
        	$data['imRat'] = $this->format->float($result['imdbRating']);
        } else {
        	$data['imRat'] = -1;
        	$data['warning'][] = 'no hay puntuación de imdb';
        }

        if (isset($result['imdbVotes']) AND $result['imdbVotes'] != 'N/A') {
        	$data['imCount'] = $this->format->integer($result['imdbVotes']);
        } else {
        	$data['imCount'] = -1;
        	$data['warning'][] = 'no hay cantidad de votos en imdb';
        }

        if (isset($result['tomatoMeter']) AND $result['tomatoMeter'] != 'N/A') {
        	$data['rtRat'] = $this->format->float($result['tomatoMeter']);
        } else {
        	$data['rtRat'] = -1;
        	$data['warning'][] = 'no hay puntuación de Rotten Tomatoes';
        }

        if (isset($result['tomatoReviews']) AND $result['tomatoReviews'] != 'N/A') {
        	$data['rtCount'] = $this->format->integer($result['tomatoReviews']);
        } else {
        	$data['rtCount'] = -1;
        	$data['warning'][] = 'no hay contador de votos de Rotten Tomatoes';
        }

        if (isset($result['tomatoURL'])) {
        	$data['rtUrl'] = $result['tomatoURL'];
        } else {
        	$data['rtUrl'] = '';
        	$data['warning'][] = 'no hay URL de Rotten Tomatoes';
        }

        return $data;
	}


	public function scrapMojo($crawler)
	{
		//SCRAPEAMOS Y GUARDAMOS LA FECHA DE LA TAQUILLA (ULTIMO DIA)
		$date = $crawler->filter('h3')->text();
		$date = $this->format->date2($date);
        $this->repository->setParams('Taquilla', NULL, $date);

        //TRAEMOS FECHA DE CARTELERA PARA COMPARARLAS
        $theatresDate = $this->showRepository->getParam('Cartelera', 'date');
        if ($date > $theatresDate) {
            //TAQUILLA MOJO MAS NUEVO QUE FILMAFFINITY
            $state = 0; //Estrenos = 0
        } else {
            //TAQUILLA MOJO MAS VIEJO QUE FILMAFFINITY
            $state = 20; //Estrenos = 20
        }

        $table = $crawler->filter('table')->eq(4)->filter('tr')->each(function($element, $i) {
        	//deshechamos la primera fila que es de los titulos
            if ($i == 0) { return NULL; }

            //recopilamos datos del resto
            $data = [
            	'rank' 		=> $element->filter('td')->eq(0)->text(),
            	'previous' 	=> $element->filter('td')->eq(1)->text(),
            	'title' 	=> $element->filter('td')->eq(2)->text(),
            	'weekend' 	=> $this->format->Integer($element->filter('td')->eq(4)->text()),
            	'total' 	=> $this->format->Integer($element->filter('td')->eq(9)->text()),
            	'weeks' 	=> $element->filter('td')->eq(10)->text(),
            ];
            return $data;
        });

        //borramos la primera fila NULL
		$table = array_filter($table);

		//recorremos el array de resultados para guardar en la db
        foreach ($table as $mojo) {

        	//BUSCAMOS EN ARRAY
    		if (array_key_exists($mojo['title'], config('movies.matchMojo'))) {
    			$movie = $this->showRepository->getMovieByFa(config('movies.matchMojo')[$mojo['title']]);
    			//SI LO LOCALIZAMOS EN NUESTRA DB
    			if (count($movie)) {
    				$this->repository->updateMojo($movie->id, $mojo, $state);
    				echo $movie->year . $movie->original_title . '   ->   ' . $mojo['title'] . '<br>';
    			} else {
    				echo 'está en array pero no lo localizamos en nuestra db  ->  ' . $mojo['title'] . '<br>';
    			}

    		} else {
                //BUSCAMOS EN TABLA MOVIE POR TITULO ORIGINAL
                $movie = $this->showRepository->searchMojo($mojo['title']);
                if ($movie) {
                    if ($this->repository->updateMojo($movie->id, $mojo, $state)) {
                    	echo $movie->year . $movie->original_title . '   ->   ' . $mojo['title'] . '<br>';
                    } else {
                   		echo 'Encuentra la película' . $movie->title . ' pero esta no se encuentra en cartelera ->  ' . $mojo['title'] . '<br>';
                   	}
                } else {
                    //si no encontramos volvemos a buscar quitando parte entre paréntesis
                    $title = trim(preg_replace('/\s*\([^)]*\)/', '', $mojo['title']));
                    $movie = $this->showRepository->searchMojo($title);
                    if ($movie) {
                        if ($this->repository->updateMojo($movie->id, $mojo, $state)) {
                        	echo $movie->year . $movie->original_title . '   ->   ' . $mojo['title'] . '<br>';
                        } else {
                        	echo 'Encuentra la película' . $movie->title . ' pero esta no se encuentra en cartelera ->  ' . $mojo['title'] . '<br>';
                        }
                    } else {
                    echo 'sin resultados  ->  ' . $mojo['title'] . '<br>';
                    }
                } 
            }
        }

        $this->repository->cleanOrder();

        //actualizamos en la db el orden de los que no estan en mojo
        /*$this->repository->updateNoMojo($state);*/


    }

}
