@extends('layouts.master')

@section('title', 'Cartelera - Películas en cines')
@section('metadescription', 'Cartelera - Películas en cines')
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		<section class="info">
		
			<div class="info-data">
				<h1 class="h1">En TV</h1>
				<h2>{{$list->description}}</h2>
				<ul class="info-details">
					<li>{{$list->movies->count()}} películas</li>
					<li class="separator">·</li>
					<li><span class="author">De <a href="{{route('userlists', ['name' => str_slug('Oficial Indicecine'), 'id' => 1])}}">Indicecine</a></span></li>
				</ul>
			</div>

			<div class="related-lists">
				<div class="line"></div>
				<h3>Otras listas populares</h3>
				<ul class="lists-item">
					<li><a href="{{route('home')}}">En Cines</a></li>
				</ul>
			</div>

		</section>

		<section class="loop">
			@include('includes.loop', ['movies' => $list->movies->where('movistarSchedule.time', '>', \Carbon\Carbon::now()->subHour())->sortBy('movistarSchedule.time')->unique()])
		</section>

	</div>
@endsection
