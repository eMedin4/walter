@extends('layouts.master')

@section('title', 'Película - ' . $movie->title)
@section('metadescription', $movie->title . ': Sinópsis, críticas y puntuaciones. Guarda ' . $movie->title . 'en tus listas o crea rankings personalizados')
@section('bodyclass', 'single-page')

@section('content')

<div class="single-wrap" data-facebook="{{route('authsocial', ['provider' => 'facebook'])}}" data-google="{{route('authsocial', ['provider' => 'google'])}}">

	<section class="info-single">

		<div class="info-single-header">
			<div class="large-image">
				@if ($movie->check_poster)
				    <img src="{{asset('/assets/posters/large') . $movie->poster}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}" >
				@else 
					<img src="{{asset('/assets/images/no-poster.png')}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}">
				@endif			
			</div>
			@include('includes.rating-single')
		</div>

		<div class="summary-mobile">
			<h1 class="h1">{{$movie->title}}</h1>
			<p>
				{{$movie->country}} ({{$movie->original_title}}) · {{$movie->year}} · {{$movie->duration}} mins
				@foreach ($movie->genres as $genre)
					@if($loop->first) · @endif
					{{$genre->name}}@if($loop->last) @else, @endif
				@endforeach
			</p>
		</div>

		<div class="lists-item-wrap">
			<h3>Guárdala en tus listas</h3>
			@if (Auth::check())
				<ul class="my-lists lists-item" data-movie="{{$movie->id}}" data-url="{{route('addlist')}}">
					@foreach($lists as $list)
			            @if ($list->movies->where('id', $movie->id)->count())
			            	<li><span class="disable-add-list"><em>{{$list->name}}<i class="icon-check-list fa fa-check"></i></em></span></li>
			            @else
			            	<li><span class="js-add-list" data-id="{{$list->id}}" data-name="{{$list->name}}" data-ordered="{{$list->ordered}}"><em>{{$list->name}}<i class="fa fa-popup icon-add-list"></i></em></span></li>
			            @endif
					@endforeach
				</ul>
				<span class="btn btn-active js-new-list" data-csrf="{{ csrf_token() }}" data-movie="{{$movie->id}}" data-url="{{route('newlist')}}">Crear lista<i class="fa fa-add-to-list icon-new-list "></i></span>
			@else
				<ul class="register-lists lists-item" data-info="Crea y usa tus listas para consultarlas cuando quieras, puedes ordenarlas o crear rankings y compartirlas con todos">
					<li><span class="js-launch-login" ">Vistas<i class="fa fa-popup icon-add-list"></i></span></li>
					<li><span class="js-launch-login" ">Para ver<i class="fa fa-popup icon-add-list"></i></span></li>
					<li><span class="js-launch-login" ">Mi top 100<i class="fa fa-popup icon-add-list"></i></span></li>
					<li><span class="js-launch-login btn-active" ">Crear lista<i class="fa fa-add-to-list icon-new-list "></i></span></li>
				</ul>
			@endif
		</div>

		<div class="lists-item-wrap">
			<h3>Otras listas que la incluyen</h3>
			<ul class="other-lists lists-item">
				@foreach($otherLists as $otherList)
					<li><a href="{{route('list', ['id' => $otherList->id, 'name' => str_slug($otherList->name)])}}">{{$otherList->name}}</a></li>
				@endforeach
			</ul>
		</div>

	</section>

	@include('includes.summary')

</div>
@endsection
