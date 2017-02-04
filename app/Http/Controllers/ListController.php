<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\BuildRepository;
use App\Repositories\ShowRepository;

class ListController extends Controller
{
	private $build;
	private $show;

	public function __Construct(BuildRepository $build, ShowRepository $show)
	{
		$this->build = $build;
		$this->show = $show;
	}
    
	public function newList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'name' => 'required|max:32',
	        'description' => 'max:200',
	        'ordered' => 'boolean'
	    ],[
	    	'name.required' => 'Introduce un nombre para tu lista',
	    	'name.max' => '32 carácteres máximo',
	    	'description.max' => '200 carácteres máximo',
	    	'ordered.boolean' => 'valor de lista numerada incorrecto'
	    ]);

	    $state = $this->build->newList($request->input('name'), $request->input('movie'), $request->input('ordered'), $request->input('description'));

	    return response()->json($state);
	}

	public function addList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }

        $this->validate($request, [
	        'list' => 'required|integer',
	        'movie' => 'required|integer',
	        'ordered' => 'required|boolean'
	    ]);

	    $this->build->addList($request->input('list'), $request->input('movie'));

	    return response()->json(['success' => true, 'message' => 'correcto']);		
	}

	public function show($name, $id)
	{
		$list = $this->show->showList($id, $name);
		return view('pages.list', compact('list'));		
	}

	public function getEdit($id)
	{
		$list = $this->show->showList($id);
		return view('pages.list', compact('list'));			
	}

	public function postEdit(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        }

	    $this->build->updateList($request->input('list'), $request->input('movies'), $request->input('title'), $request->input('description'));

	    return response()->json(['success' => true, 'message' => 'correcto']);		
	}

	public function deleteList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $del = $this->build->deleteList($request->input('id'));
        return $del; 		
	}


	public function deleteMovieList(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $del = $this->build->deleteMovieList($request->input('list'), $request->input('movie'));
        return $del;
	}

	public function addToMylists(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->addToMylists($request->input('list'));

	}

	public function DelFromMylists(Request $request)
	{
		if( ! $request->ajax()) {       
            return back(); 
        } 
        $this->build->delFromMylists($request->input('list'));

	}

}
