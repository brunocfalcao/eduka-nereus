@foreach(course()->meta_tags as $name => $content)
	<meta name="{{ $name }}" content="{{ htmlentities($content) }}"/>
@endforeach