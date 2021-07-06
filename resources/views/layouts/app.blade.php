<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<!-- FB Meta -->
	<meta property="og:url" content="{{ Request::url() }}" />
	<meta property="og:title" content="Load Order Library - @yield('title')" />
	<meta property="og:description" content="@yield('description', 'A modlist files site to help with support.')" />
	<meta property="og:image" content="@yield('image', url('/images/logo.png'))" />
	<meta property="og:type" content="website" />

	<!-- Twitter Meta -->
	<meta name="twitter:card" content="summary">
	<meta name="twitter:url" content="{{ Request::url() }}">
	<meta name="twitter:title" content="Load Order Library - @yield('title')">
	<meta name="twitter:description" content="@yield('description', 'A modlist files site to help with support.')">
	<meta name="twitter:image" content="@yield('image', url('/images/logo.png'))">

	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>@yield('title') - {{ config('app.name', 'Load Order Library') }}</title>

	<!-- Fonts -->
	<!-- <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet"> -->

	<!-- Styles -->
	<link href="{{ mix('/css/app.css') }}" rel="stylesheet">
</head>

<body>
	<div id="app">
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<div class="container">
				<a class="navbar-brand" href="{{ config('app.url') }}">
					{{ config('app.name', 'Load Order Library') }}
				</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav ms-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a class="nav-link" href="https://loadorderlibrary.com">Return to Main Site</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<main class="py-4">
			<div class="container text-white">
				@yield('content')
			</div>
		</main>

		<footer>
			<div class="container d-flex flex-column align-items-center">
				<div class="text-white">
					<p>
						Load Order Library &copy; 2021 Phinocio
					</p>

				</div>
				<div class="text-white">
					<p>
						<a href="https://github.com/phinocio/loadorderlibrary/issues/new" target="_blank" rel="noopener noreferrer">Create Github Issue</a> |
						<a href="https://github.com/phinocio/loadorderlibrary" target="_blank" rel="noopener noreferrer">Github</a> |
						<a href="https://discord.gg/K3KnEgrQE4" target="_blank" rel="noopener noreferrer">Discord</a> |
						<a href="https://www.reddit.com/r/LoadOrderLibrary/" target="_blank" rel="noopener noreferrer">Reddit</a> |
						<a href="/support-me">Support Me</a>
					</p>
				</div>
			</div>
		</footer>
	</div>
	<script src="{{ mix('/js/manifest.js') }}"></script>
	<script src="{{ mix('/js/vendor.js') }}"></script>
	<script src="{{ mix('/js/app.js') }}"></script>

	<svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
		<symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
			<path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
		</symbol>
		<symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
			<path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
		</symbol>
		<symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
			<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
		</symbol>
	</svg>
</body>

</html>