@extends('layouts.master')

@section('title', 'Indicecine - Tu usuario')
@section('bodyclass', 'login-page')

@section('content')
	<div class="full-layout-wrap">
		<div class="full-layout panel-login">
			<div class="inner">

		        <h1>Entra</h1>
		        <h2>en <span class="brand">Indicecine</span></h2>

				<a class="social-btn facebook" href="{{route('authsocial', ['provider' => 'facebook'])}}">
			       <i class="fa fa-facebook-fa" aria-hidden="true"></i>
			       <span>Entra con Facebook</span>
			   </a>

			   <a class="social-btn google" href="{{route('authsocial', ['provider' => 'google'])}}">
			       <i class="fa fa-google" aria-hidden="true"></i>
			       <span>Entra con Google</span>
			   </a>

			    <div class="oval-shape"></div>

			    <p>Crea y usa tus listas para consultarlas cuando quieras, puedes ordenarlas o crear rankings y compartirlas con todos</p>
			    
			</div>
		</div>
	</div>

@endsection
