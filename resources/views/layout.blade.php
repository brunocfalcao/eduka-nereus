<!DOCTYPE html>
<html>
<head>
	<title>{{ $title ?? env('APP_NAME') }}</title>
	@yield('head.links')
</head>
<body>
	@yield('body')
</body>
</html>