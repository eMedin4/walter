@extends('layouts.master')

@section('title', 'Cartelera - Películas en cines')
@section('metadescription', 'Cartelera - Películas en cines')
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		<section class="info">
		
			<div class="info-data">
				<h1 class="h1">En Cines</h1>
				<h2>{{$list->description}}</h2>
				<ul class="info-details">
					<li>{{$list->movies->count()}} películas</li>
					<li class="separator">·</li>
					<li><span class="author">De <a href="{{route('userlists', ['name' => str_slug('Oficial Indicecine'), 'id' => 1])}}">Indicecine</a></span></li>
				</ul>
			</div>

			<ul class="related lists-item">
				<li><a href="#">Taquilla 27/11/2016</a></li>
				<li><a href="#">En Televisión</a></li>
				<li><a href="#">Más vistas</a></li>
				<li><a href="#">TOP100 Filmaffinity</a></li>
				<li><a href="#">TOP100 Imdb</a></li>
				<li><a href="#">TOP100 RottenTomatoes</a></li>
				<li><a href="#">TOP10 2016 Blogdecine</a></li>
			</ul>
		</section>

		<section class="loop">
			@include('includes.loop', ['movies' => $list->movies])
		</section>

	</div>
@endsection
