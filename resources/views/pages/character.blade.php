@extends('layouts.master')

@section('title', $character->name)
@section('metadescription', $character->name, $character->department)
@section('bodyclass', 'list-page')

@section('content')

	<div class="wrap">

		<section class="info">
			<h1>{{$character->name}}</h1>
			<p>{{$character->department}}</p>

			@if($character->photo && file_exists(public_path() . '/assets/profiles' . $character->photo))
				<img class="character-image" src="{{asset('/assets/profiles') . $character->photo}}" alt="{{$character->photo}}" title="foto de {{$character->photo}}">
			@endif
		</section>

		<section class="loop">
			@include('includes.loop', ['movies' => $character->movies])
		</section>

	</div>

@endsection

@section('scripts')
	
@endsection
