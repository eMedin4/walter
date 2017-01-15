@if (!$movies->isEmpty())
	@foreach ($movies as $movie)
		<article>
			<a class="movie" href="{{route('show', $movie->slug)}}" data-id="{{$movie->id}}">

				<div class="meta">
					<span>{{$movie->year}} <i class="separator">·</i> {{$movie->country}}</span>
					<div class="rating rating-{{$movie->average}}">
						@include('includes.ratings', ['ratings' => $movie->average])
					</div>
				</div>

				<div class="medium-image relative">
					<img class="loop-image" src="{{asset('/assets/posters/medium') . $movie->poster}}" alt="{{$movie->title}}" title="poster de {{$movie->title}}" width="166" height="249">
					@if(isset($list->ordered) AND $list->ordered)
						<div class="order" data-current="{{$movie->pivot->order}}">{{$movie->pivot->order}}</div>
					@endif
				</div>

				<div class="loop-title">
					<h3>{{$movie->title}}</h3>
				</div>

			</a>
		</article>
	@endforeach


	@if ($movies->count() < 7)
		@for ($i = 0; $i < $list->countItem['remainder']; $i++)
		    <article class="empty-grid empty-grid-{{$list->countItem['total'] + $i + 1}} js-ignore-edit"><!-- grid  -->
		    	<div></div>
		    </article>
		@endfor
	@else
		@for ($i = 0; $i < 6; $i++)
		    <article class="empty-grid-require js-ignore-edit"><!-- grid  -->
		    	<div></div>
		    </article>
		@endfor	
	@endif


@else
	<h3 class="empty">No hay nada aún</h3>
@endif
