<!DOCTYPE html>
<html lang="es">
<head>
	<title>Error</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
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
</head>

<body class="error-page">
	<div class="full-layout-wrap">
		<div class="full-layout">

				<h1>Página no encontrada</h1>

				<div class="large-logo">
					<a href="{{route('home')}}">
						<h2>IndiceCine</h2>
					</a>
				</div>

				<h3>Puedes volver al <a href="{{route('home')}}">inicio</a></h3>

		</div>
	</div>
</body>
</html>

