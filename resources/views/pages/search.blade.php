@extends('layouts.master')

@section('title', 'Buscar - ' . $search)
@section('metadescription', 'Buscar - ' . $search)
@section('bodyclass', 'home-page')

@section('content')

	<div class="wrap">

		<section class="info">
			<div class="info-data">
				<h1 class="h1">{{$search}}</h1>
				<h2>Resultados de búsqueda</h2>
			</div>
		</section>

		<section class="loop">
			@include('includes.loop', ['movies' => $movies])
		</section>

	</div>
@endsection
