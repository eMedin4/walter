<?php

/*
POSTER QUE NO SE GUARDA: 2QoLPgwWphEO2eVDm6ehGxCWoXV c9YqifOPRSdSPapWVwRZipwORz7 actor8830
*/

/*
	ARTISAN
*/

	Route::get('/artisan', function() {
		/*Artisan::call('make:migration', ['name' => 'create_lists_user_pivot_table', '--table' => 'lists']);*/
		Artisan::call('migrate');
		/*Artisan::call('make:model', ['name' => 'Entities/List']);*/
		/*Artisan::call('make:controller', ['name' => 'ListController']);*/
		dd(Artisan::output());
	});

/*
	DEVELOPMENT ZONE
*/


/*
	PRIVATE ADMIN
*/

	Route::group(['middleware'=>['auth', 'admin'], 'namespace' => 'Admin', 'prefix' => 'admin'], function () {
		Route::get('/scraper', 'BuildController@getTheatres');
		Route::get('/scraperall', 'BuildController@getAll');
		Route::get('/testmojo', 'BuildController@getMojo');
		Route::get('/checkposter', 'BuildController@checkPoster');
	});

/*
	PRIVATE USERS
*/

	Route::group(['middleware' => 'auth'], function () {
		Route::post('/comment/{id}', ['as' => 'comment', 'uses' => 'MovieController@comment']);
		Route::post('/newlist', ['as' => 'newlist', 'uses' => 'ListController@newList']);
		Route::post('/addlist', ['as' => 'addlist', 'uses' => 'ListController@addList']);
    	Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\SocialController@logout']);
    	Route::get('/lista/{id}/editar', ['as' => 'editlist', 'uses' => 'ListController@getEdit']);
    	Route::post('/lista/editar', ['as' => 'posteditlist', 'uses' => 'ListController@postEdit']);
    	Route::post('/lista/borrar', ['as' => 'deletelist', 'uses' => 'ListController@deleteList']);
    	Route::post('/lista/borrarpelicula', ['as' => 'deletemovielist', 'uses' => 'ListController@deleteMovieList']);
    	Route::post('/lista/addtomylists', ['as' => 'addtomylists', 'uses' => 'ListController@addToMylists']);
    	Route::post('/lista/delfrommylists', ['as' => 'delfrommylists', 'uses' => 'ListController@delFromMylists']);
    	Route::get('/perfil/listas-guardadas/{name}/{id}', ['as' => 'usersavedlists', 'uses' => 'MovieController@userSavedLists']);
	});

/*
	AUTH
*/

	Route::group(['middleware' => 'guest'], function () {
		//google oauth: https://console.developers.google.com/apis/library?project=indice-cine (elann2013)
	    Route::get('/authsocial/manage/{provider}', ['as' => 'authsocial', 'uses' => 'Auth\SocialController@redirectToProvider']);
	    Route::get('/authsocial/callback/{provider}', 'Auth\SocialController@handleProviderCallback');
	    Route::get('/login', ['as' => 'login', 'uses' => 'Auth\SocialController@login']);
	});

/*
	MAIN
*/

	Route::get('/', ['as' => 'home', 'uses' => 'MovieController@home']);
	Route::get('/ficha/{name}/{id}', ['as' => 'character', 'uses' => 'MovieController@characters']);
	Route::get('/perfil/listas/{name}/{id}', ['as' => 'userlists', 'uses' => 'MovieController@userLists']);
	Route::get('/perfil/criticas/{name}/{id}', ['as' => 'usercritics', 'uses' => 'MovieController@userCritics']);
	Route::get('/lista/{name}/{id}', ['as' => 'list', 'uses' => 'ListController@show']);
	Route::post('/livesearch', ['as' => 'livesearch', 'uses' => 'MovieController@liveSearch']);
	Route::get('/normalsearch', ['as' => 'normalsearch', 'uses' => 'MovieController@normalSearch']);
	Route::get('/{slug}', ['as' => 'show', 'uses' => 'MovieController@show']);

