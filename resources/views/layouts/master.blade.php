<!DOCTYPE html>
<html lang="es">
<head>
	<title>@yield('title')</title>
	<meta name="description" content="@yield('metadescription')">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="initial-scale=1.0, width=device-width">
	<script>
	  (function(d) {
	    var config = {
	      kitId: 'ocy2tfu',
	      scriptTimeout: 3000,
	      async: true
	    },
	    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
	  })(document);
	</script>
	<script src="https://use.fortawesome.com/712aad58.js"></script>
	<link rel="stylesheet" href="{{ asset('/assets/css/style2.css') }}">
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

	<script src="{{ asset('/assets/js/scripts.js') }}"></script>

<!-- Page scripts -->
	@yield('scripts')

</body>
</html>

