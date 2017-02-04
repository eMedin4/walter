
@foreach ($lists as $list)
	<article>
		<a class="list" href="{{route('list', ['id' => $list->id, 'name' => str_slug($list->name)])}}"><!-- data-id="{{$list->id}}" -->

			<div class="list-image relative">
				@if ($list->movies->count() >= 4)
					<img class="loop-image" src="{{asset('/assets/imagelists') . '/' . $list->id . '.jpg'}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="166" height="248">
				@elseif ($list->movies->count() == 0)
					<div class="loop-no-image"></div>
				@elseif ($list->movies->count() == 1)
					<img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[0]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><div class="no-thumb"></div><!-- 
				 --><div class="no-thumb"></div><!-- 
				 --><div class="no-thumb"></div>
				@elseif ($list->movies->count() == 2)
					<img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[0]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[1]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><div class="no-thumb"></div><!-- 
				 --><div class="no-thumb"></div>
				@elseif ($list->movies->count() == 3)
					<img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[0]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[1]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><img class="thumb-image" src="{{asset('/assets/posters/medium') . $list->movies[2]->poster}}" alt="{{$list->name}}" title="Lista de películas {{$list->name}}" width="83" height="124"><!-- 
				 --><div class="no-thumb"></div>
				@endif
			</div>

			<div class="meta">
				<span>
					<span>
						@if ($list->movies->count() == 0)
							No hay nada
						@elseif ($list->movies->count() == 1)
							1 película
						@else
							{{$list->movies->count()}} películas
						@endif
					</span>
					@if ($list->updated_at)
					<i class="separator">·</i>
						<span class="no-wrap">
							{{$list->updated_at->diffForHumans()}}
						</span>
					@endif
				</span>
			</div>
			
			<div class="loop-title">
				<h3>{{$list->name}}@if ($list->ordered)<i class="fa fa-sort-numeric-asc icon-ordered"></i>@endif</h3>
			</div>
		</a>
	</article>
@endforeach


