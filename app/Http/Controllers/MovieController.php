<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\BuildRepository;
use App\Repositories\ShowRepository;
use Auth;
use Image;

class MovieController extends Controller
{

	private $build;
	private $show;

	public function __Construct(BuildRepository $build, ShowRepository $show)
	{
		$this->build = $build;
		$this->show = $show;
	}
    
	public function show($slug)
	{
		$movie = $this->show->movieBySlug($slug);
		$characters = $movie->characters()->orderBy('character_movie.order')->get();
		$comments = $this->show->comments($movie->id);

	    if(Auth::check()){
	    	$lists = $this->show->listsByUser(Auth::user()->id);
	        $movieInList = False;
	        foreach( $lists as $list ){
	            if( $list->movies->where('id', $movie->id) ){
	                $movieInList= True;
	            }
	        }
	        $otherLists = $movie->lists->where('user_id', '<>', Auth::id());
	    } else {
	    	$otherLists = $movie->lists;
	    }

		return view('pages.single', compact('movie', 'characters', 'comments', 'lists', 'movieInList', 'otherLists'));
	}

	public function home()
	{
		$listId = 1;
		$list = $this->show->home($listId);
		return view('pages.home', compact('list'));
	}

	public function characters($name, $id)
	{
		$character = $this->show->character($id);
		return view('pages.character', compact('character'));		
	}

	public function userLists($name, $id)
	{
		$user = $this->show->userLists($id, $name);
		return view('pages.user-lists', compact('user'));	
	}

	public function userSavedLists($name, $id)
	{
		$user = $this->show->userSavedLists($id, $name);
		return view('pages.user-saved-lists', compact('user'));			
	}

	public function userCritics($name, $id)
	{
		$user = $this->show->userCritics($id, $name);
		return view('pages.user-critics', compact('user'));	
	}

	public function comment($id, Request $request)
	{
		$this->validate($request, [
	        'comment' => 'required|min:2|max:200',
	    ],[
	    	'comment.required' => 'Por favor escribe tu crítica',
	    	'comment.min' => 'Por favor escribe tu crítica',
	    	'comment.max' => 'Máximo 200 carácteres',
	    ]);

	    $this->build->comment($id, $request->input('comment'));
	    return back();

	}

    public function liveSearch(Request $request)
    {
    	if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'string' => 'required|max:50'
	    ]);

	    $results = $this->show->liveSearch($request->input('string'));

        if ($results->isEmpty()) {
            return response()->json(['response' => false]);
        }

        return response()->json(['response' => true, 'result' => $results]);
	}

	public function normalSearch(Request $request)
	{
        $this->validate($request, [
	        'search' => 'required|max:50'
	    ]);
        $search = $request->input('search');
	    $movies = $this->show->normalSearch($search);

	    return view('pages.search', compact('movies', 'search'));
	}

}


