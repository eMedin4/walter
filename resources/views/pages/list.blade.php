@extends('layouts.master')

@section('title', $list->name)
@section('metadescription', 'Listas de cine: ' . $list->name . ' de ' . $list->user->name)
@section('bodyclass', 'list-page')

@section('content')

	<div class="wrap">

		<section class="info info-default">

			<div class="info-data">

				<h1 class="h1 original-name">{{$list->name}}</h1>
				@if (!empty($list->description))
					<h2 class="original-description">{{$list->description}}</h2>
				@endif

				<ul class="info-details">
					<li>{{$list->movies->count()}} películas</li>
					<li class="separator">·</li>					
					<li class="time">Actualizada {{$list->updated_at->diffForHumans()}}</li>
					<li class="separator">·</li>
					<li><span class="author">De <a href="{{route('userlists', ['name' => str_slug($list->user->name), 'id' => $list->user->id])}}">{{$list->user->name}}</a></span></li>
				</ul>
				<ul class="btn-wrap">
					@if(Auth::check() && Auth::id() == $list->user_id)
						<li><div class="btn js-on-edit" data-url="{{route('posteditlist')}}" data-id="{{$list->id}}">Editar</div></li>
					@else
						@if (!$list->savedToUsers()->count())
							<li><div class="btn js-add-to-mylists" data-url="{{route('addtomylists')}}" data-id="{{$list->id}}">Guardar lista</div></li>
						@else
							<li><div class="btn btn-success">Lista guardada</div><div class="btn btn-double js-del-from-mylists" data-url="{{route('delfrommylists')}}" data-id="{{$list->id}}">Borrar</div></li>
						@endif
					@endif        
                	<li><a class="btn" href="{{route('userlists', ['name' => str_slug($list->user->name), 'id' => $list->user->id])}}">Más listas de {{$list->user->name}}</a></li>
                </ul>

			</div>

		</section>

		@if(Auth::check() && Auth::id() == $list->user_id)
			
			<section class="info info-edit">

				<div class="edit-note">
					<h3><strong>Modo Edición</strong>Arrastra las películas para reordenarlas tal como quieras. Haz click en guardar para salvar los cambios</h3>
				</div>

				<h2 class="h1 name">{{$list->name}}</h2>
				<h2 class="description">{{$list->description}}</h2>

                <ul class="btn-wrap">
                    <li><div class="btn btn-power edit-submit" data-url="{{route('posteditlist')}}" data-id="{{$list->id}}">Guardar</div></li>
                    <li><div class="btn js-edit-list" data-order="{{$list->ordered}}">Editar info</div></li>
                    <li><div class="btn btn-cancel js-off-edit">Cancelar</div></li>
                    <li><div class="btn btn-alert edit-delete" data-url="{{route('deletelist')}}" data-url-movielist="{{route('deletemovielist')}}" data-redirect="{{route('userlists', ['name' => str_slug($list->user->name), 'id' => $list->user->id])}}" data-name="{{$list->name}}" data-id="{{$list->id}}">Borrar lista</div></li>
                </ul>
	            </form>

			</section>

		@endif

		<section class="loop" id="js-loop">
			@include('includes.loop', ['movies' => $list->movies])
		</section>

	</div>

@endsection

@section('scripts')
	
@endsection
