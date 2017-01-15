@extends('layouts.master')

@section('title', 'Usuario - ' . $user->name)
@section('bodyclass', 'user-page')

@section('content')

	<div class="wrap">

		<section class="info">
			<div class="info-data">
				@include('includes.info-user')
			</div>
		</section>

		<section class="loop">
			@include('includes.loop-list', ['lists' => $user->lists])

			@if ($user->lists->count() < 7)
				@for ($i = 0; $i < $user->countItem['remainder']; $i++)
				    <article class="empty-grid empty-grid-{{$user->countItem['total'] + $i + 1}} js-ignore-edit"><!-- grid  -->
				    	<div></div>
				    </article>
				@endfor
			@endif
		</section>

	</div>

@endsection
