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

		<section class="summary">
			@if (!$user->comments->isEmpty())
				@foreach ($user->comments as $comment)
					<article class="comment">
						<header>
							<div class="small-image-wrap">
								<img class="small-image" src="{{asset('/assets/posters/small') . $comment->movie->poster}}" alt="{{$comment->movie->title}}" title="poster de {{$comment->movie->title}}" width="30" height="45">
							</div>
							<a class="movie-referal" href="{{route('show', $comment->movie->slug)}}">{{$comment->movie->title}}</a>
							<time>{{$comment->created_at->diffForHumans()}}</time>
						</header>
						<p>{{$comment->text}}</p>
					</article>	
				@endforeach
			@else
				<h3 class="empty">No hay nada aún</h3>
			@endif
		</section>

	</div>
@endsection
