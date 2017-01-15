@if(Auth::check() && Auth::user()->id == $user->id)
	<h1 class="h1">{{$user->name}}</h1>
	<div class="user-details">
	@if ($user->facebook_id)
		<i class="facebook fa fa-facebook-fa"></i>
	@elseif ($user->google_id)
		<i class="google fa fa-google"></i>
	@endif
		{{$user->email}}
	</div>
	<ul class="btn-wrap">

		<li><a class="btn {{ Request::route()->getName() == 'userlists' ? 'btn-active' : '' }}" href="{{route('userlists', ['name' => str_slug($user->name), 'id' => $user->id])}}">{{$user->lists->count()}} listas</a></li>

		<li><a class="btn {{ Request::route()->getName() == 'usersavedlists' ? 'btn-active' : '' }}" href="{{route('usersavedlists', ['name' => str_slug($user->name), 'id' => $user->id])}}">{{$user->savedLists->count()}} listas guardadas</a></li>

		<li><span class="btn js-new-list" data-csrf="{{ csrf_token() }}" data-movie="0" data-url="{{route('newlist')}}" data-path="{{ asset('') }}">Crear lista<i class="fa fa-add-to-list icon-new-list "></i></span></li>

		<li><a class="btn {{ Request::route()->getName() == 'usercritics' ? 'btn-active' : '' }}" href="{{route('usercritics', ['name' => str_slug($user->name), 'id' => $user->id])}}">Críticas</a></li>
		<li><a class="btn" href="{{route('logout')}}">salir</a></li>

	</ul>
@else
	<h1 class="h1">{{$user->name}}</h1>
	<ul class="btn-wrap">

		<li><span class="btn {{ Request::route()->getName() == 'userlists' ? 'btn-active' : '' }}">{{$user->lists->count()}} listas</span></li>

		<li><a class="btn {{ Request::route()->getName() == 'usercritics' ? 'btn-active' : '' }}" href="{{route('usercritics', ['name' => str_slug($user->name), 'id' => $user->id])}}">Críticas</a></li>
		
	</ul>
@endif
