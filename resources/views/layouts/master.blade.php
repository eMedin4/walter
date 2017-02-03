<!DOCTYPE html>
<html lang="es">
<head>
	<title>@yield('title')</title>
	<meta name="description" content="@yield('metadescription')">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="initial-scale=1.0, width=device-width">
	<link rel="stylesheet" href="{{ asset('/assets/css/style2.css') }}">
	<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600" rel="stylesheet">
	<script src="https://use.fortawesome.com/712aad58.js"></script>
	@yield('topscripts')
</head>

<body class="@yield('bodyclass', '')">

	<div class="inner">
		<div class="limit">
			@include('includes.header')
		</div>
		
		<div class="limit">
			@yield('content')	
		</div>

		@include('includes.footer')

	<!-- Modals -->
		<div class="modal-wrap"><div class="modal"><div class="inner"></div></div></div>

	</div>

<!-- All site scripts -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
	<script src="{{ asset('/assets/js/scripts.js') }}"></script>

<!-- Page scripts -->
	@yield('scripts')

</body>
</html>

