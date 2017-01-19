@extends('layouts.master')

@section('title', 'Indicecine - Tu usuario')
@section('bodyclass', 'login-page')

@section('content')
	<div class="full-layout-wrap">
		<div class="full-layout panel-login">
			<div class="inner">

				<form method="POST" action="{{route('postloginadmin')}}">
					{!! csrf_field() !!}
					<label>Email</label>
					<input type="email" name="email">
					<label>Constraseña</label>
					<input type="password" name="password">
					<button type="submit">Entrar</button>
				</form>
			    
			</div>
		</div>
	</div>

@endsection
