
<header class="page-header">

	<div class="logo">
		<a href="{{route('home')}}">
			<div class="image">
				<img src="{{asset('/assets/images/logo.png')}}" alt="Indicecine" title="Indicecine" width="22" height="16">
			</div>
			<h2>IndiceCine</h2>
		</a>
	</div>

	<div class="search">
		<form autocomplete="off" method="GET" action="{{route('normalsearch')}}">
			{!! csrf_field() !!}
			<div class="close"><i class="fa fa-times"></i></div>
			<button type="submit"><i class="icon-search fa fa-search-btb"></i></button>
			<input type="text" name="search" class="input-search" placeholder="Busca una película" data-url="{{ route('livesearch') }}" data-path="{{ asset('') }}">
			<div class="search-results"></div>
			<div class="search-results-wrap"></div>
		</form>
	</div>

	<div class="menu">
		<span class="search-launch"><i class="fa fa-search-btb"></i></span>
		@if (Auth::check())
		    <a class="user-logged" href="{{route('userlists', ['name' => str_slug(Auth::user()->name), 'id' => Auth::user()->id])}}">
			    @if(Auth::user()->avatar)
			    	{{Auth::user()->name}}
			    @else
		    		<i class="fa fa-user-circle-o avatar-default-me"></i>
			    @endif
		    </a>
		@else
			<a class="no-logged" href="{{route('login')}}"><i class="fa fa-user-circle"></i></a>
		@endif
	</div>

</header>




