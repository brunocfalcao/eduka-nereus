@extends('eduka::layout')

@section('head.links')
<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
@endsection

@section('body')
	<form method="POST" action="{{ route('pre-launch.subscribe') }}" class="mt-10 ml-10">
	    @honeypot
	    @csrf
	    Your email for subscription:
	    <input name="email" type="text" class="border border-black">
	    <button type="submit" value="Submit">Submit</button>
	</form>
@endsection