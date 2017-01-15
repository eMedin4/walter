<section class="summary">
	<div class="summary-desktop">
		<h1>{{$movie->title}}</h1>
		<p>
			{{$movie->country}} ({{$movie->original_title}}) · {{$movie->year}} · {{$movie->duration}} mins
			@foreach ($movie->genres as $genre)
				@if($loop->first) · @endif
				{{$genre->name}}@if($loop->last) @else, @endif
			@endforeach
		</p>
	</div>

	<div class="summary-part synopsis">
		@if($movie->review)
			<p><span class="note">Sinopsis</span>{{$movie->review}}</p>
		@endif
	</div>

	<div class="summary-part characters">
		@if($characters)
			<p class="js-characters"><span class="note hack-note">Reparto</span>
				@foreach ($characters as $character)
					<a href="{{route('character', ['id' => $character->id, 'name' => $character->slug])}}">{{$character->name}}</a>
					@if($character->department == 'director')<span class="italic">(director)</span>@endif 
					@if($loop->last). @else, @endif
				@endforeach
			</p>
		@endif
	</div>

	<div class="summary-part critics">
		@foreach ($comments as $comment)
			@if ($comment->ext_author)
			<article class="external">
				<header>
					<div class="no-avatar"><i class="fa fa-user-circle"></i></div>
					<span class="author" href="#">{{$comment->ext_author}}</span>
					<span class="alias" href="#">{{$comment->ext_media}}</span>
				</header>
				<p>{{$comment->text}}</p>
			</article>
			@else
			<article class="comment">
				<header>
					<div>
						@if($comment->user->avatar)
					    	<img src="{{ $comment->user->avatar }}" alt="{{$comment->user->name}}" width="24" height="24" class="avatar-comments">
					    @else
					    	<div class="no-avatar"><i class="fa fa-user-circle"></i></div>
					    @endif
						<a class="author" href="#">{{$comment->user->name}}</a>
					</div>
					<time>{{$comment->created_at->diffForHumans()}}</time>
				</header>
				<p>{{$comment->text}}</p>
			</article>				
			@endif
		@endforeach
	</div>
</section>

<div class="add-critic">
	<form method="POST" action="{{route('comment', ['id' => $movie->id])}}"  data-info="Añade tus propias microcríticas o vota las de los otros, si recibes votos positivos tu valoración será más visible que las demás">{!! csrf_field() !!}
		
		<label for="comment">Tu valoración en 200 carácteres</label>
		@if (Auth::check())
		<textarea name="comment" rows="3" maxlength="200">{{ old('comment') }}</textarea>
		@else
		<div class="js-launch-login simil-textarea"></div>
		@endif

		@if (Auth::check())	
			<button type="submit" class="btn">Enviar</button>
		@else
			<span class="btn js-launch-login">Enviar</span>
		@endif

	</form>
</div>
