@foreach(course()->meta_tags as $name => $content)
	<meta name="{{ $name }}" content="{{ htmlentities($content) }}"/>
@endforeach
<meta property="og:url" content="https://nova-advanced-ui.com/"/>
<meta property="og:type" content="article"/>
<meta property="og:description" content="Documentation for the Nova Advanced UI course."/>
<meta property="og:image" content="https://tailwindcss.com/_next/static/media/twitter-large-card.85c0ff9e455da585949ff0aa50981857.jpg"/>
<meta property="og:title" content="Nova Advanced UI - Learn how to develop UI Components for Laravel Nova."/>