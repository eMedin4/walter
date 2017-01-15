<ul class="rating-single">
	<li>
		<div class="rating-provider">Filmaffinity</div>
		<span class="rating-total">{{$movie->fa_rat}}</span>
		<span class="rating-count">{{$movie->fa_rat_count}}</span>
		<div class="rating rating-{{intval($movie->fa_rat)}}">
			@include('includes.ratings', ['ratings' => $movie->fa_rat])
		</div>
	</li>
	@if ($movie->im_rat != -1)
	<li>
		<div class="rating-provider">IMDB</div>
		<span class="rating-total">{{$movie->im_rat}}</span>
		<span class="rating-count">{{$movie->im_rat_count}}</span>
		<div class="rating rating-{{intval($movie->im_rat)}}">
			@include('includes.ratings', ['ratings' => $movie->im_rat])
		</div>
	</li>
	@endif
	@if ($movie->rt_rat != -1)
	<li>
		<div class="rating-provider">Rotten Tomattoes</div>
		<span class="rating-total">{{$movie->rt_rat}}<i class="percentage">%</i></span>
		<span class="rating-count">{{$movie->rt_rat_count}}</span>
		<div class="rating rating-{{intval($movie->rt_rat /10)}}">
			@include('includes.ratings', ['ratings' => $movie->rt_rat /10])
		</div>
	</li>
	@endif
</ul>