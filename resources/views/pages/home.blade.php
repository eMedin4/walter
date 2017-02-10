@extends('layouts.master')

@section('title', 'Cartelera - Películas en cines')
@section('metadescription', 'Cartelera - Películas en cines')
@section('og_type', 'website')
@section('og_url', 'http://indicecine.net')
@section('og_title', 'Indicecine')
@section('og_image', asset('/assets/posters/large') . $specialList->where('name', 'Estreno')->first()->movie->poster)
@section('og_description', 'Cartelera de cines en España, semana del ' . $date->formatLocalized('%e de %B de %Y'))
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		<section class="info">
		
			<div class="info-data">
				<h1 class="h1">En Cines</h1>
				<div class="related-lists-links">
					<span class="mark"></span>
					<a href="{{route('tv')}}">En Tv</a>
				</div>
				<h2>Cartelera de cines en España, semana del {{$date->formatLocalized('%e de %B de %Y')}}</h2>
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
					<li><a href="{{route('tv')}}">En Tv</a></li>
					@foreach ($popularLists as $list)
						<li><a href="{{route('list', ['id' => $list->id, 'name' => str_slug($list->name)])}}">{{$list->name}}</a></li>
					@endforeach
				</ul>
			</div>

		</section>

		<section class="loop">
			@if (!$specialList->isEmpty())
				@foreach ($specialList as $schedule)
					<article>
						<a class="movie" href="{{route('show', $schedule->movie->slug)}}" data-id="{{$schedule->movie->id}}">

							@if ($schedule->name == 'Próximo estreno')
								<div class="tag release-tag">
									<span>{{$schedule->name}}</span>
									<time>{{$schedule->date->formatLocalized('%d %b')}}</time>
								</div>
							@elseif ($schedule->name != 'Próximo estreno')
								<div class="tag">
									<span>En cartelera {{$schedule->date->diffForHumans()}}</span>
								</div>
							@endif

							<div class="medium-image relative">
								<div class="image-reflex"></div>
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
