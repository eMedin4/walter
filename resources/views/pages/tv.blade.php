@extends('layouts.master')

@section('title', 'Cartelera - Películas en televisión')
@section('metadescription', '¿Que películas estan echando ahora en Televisión? ¿Cuales podras ver esta noche? Toda las peliculas en programación de TDT, Movistar plus y canales digitales')
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		<section class="info">
		
			<div class="info-data">
				<h1 class="h1">En TV</h1>
				<h2>Toda las películas de la programación de televisión desde ahora</h2>
				<ul class="info-details">
					<li>{{$specialList->count()}} películas</li>
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
			@if (!$specialList->isEmpty())
				@foreach ($specialList as $schedule)
					<article>
						<a class="movie" href="{{route('show', $schedule->movie->slug)}}" data-id="{{$schedule->movie->id}}">

							<div class="tv-tag">
								<img src="{{asset('/assets/images/channels') . '/' . $schedule->channel_code . '.png'}}">
								<time>{!!$schedule->formatTime!!}</time>
							</div>

							<div class="medium-image relative">
								@if ($schedule->movie->check_poster)
									<img class="loop-image" src="{{asset('/assets/posters/medium') . $schedule->movie->poster}}" alt="{{$schedule->movie->title}}" title="poster de {{$schedule->movie->title}}" width="166" height="249">
								@else 
									<img class="loop-image" src="{{asset('/assets/images/no-poster-medium.png')}}" alt="{{$schedule->movie->title}}" title="poster de {{$schedule->movie->title}}" width="166" height="249">						
								@endif
							</div>

							<div class="meta">
								<span>{{$schedule->movie->year}} <i class="separator">·</i> {{$schedule->movie->country}}</span>
								<div class="rating rating-{{$schedule->movie->average}}">
									@include('includes.ratings', ['ratings' => $schedule->movie->average])
								</div>
							</div>

							<div class="loop-title">
								<h3>{{$schedule->movie->title}}</h3>
							</div>

						</a>
					</article>
				@endforeach
			@else
				<h3 class="empty">No hay nada aún</h3>
			@endif
		</section>
	</div>
@endsection
