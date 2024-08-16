<x-email::newsletter title="Thanks for buying {{ $order->course->name }}"
                     primaryColor="{{ $order->course->theme['primary-color'] }}"
                     secondaryColor="{{ $order->course->theme['secondary-color'] }}"
                     dangerColor="{{ $order->course->theme['danger-color'] }}">
    <x-slot name="preheader">
        Thanks for buying {{ $order->course->name }}!
    </x-slot>

    <x-slot name="header">
        @if(file_exists(storage_path('app/public/' . $order->course->canonical . '/' . $order->course->filename_logo)))
        <x-email::header-logo src="{{ Storage::disk($order->course->canonical)->url($order->course->filename_logo) }}" alt="{{ $order->course->name }}" width="150" height="50" />
        @else
        <x-email::header-title color="#333" href="{{ url_with_app_http_scheme($order->course->domain) }}">{{ $order->course->name }}</x-email::header-title>
        @endif
    </x-slot>

    <x-email::paragraph>
        <strong>Thank you so much for buying my course!</strong><br>
    </x-email::paragraph>

    <x-email::paragraph>
		Since you already bought other courses from me, all you have to do is to
		login into your backoffice and you will see the new course active there.
    </x-email::paragraph>

    <x-email::call-to-action heading="Click on the button to access your backoffice"
                             body=""
                             buttonUrl="{{ url_with_app_http_scheme($order->course->backend->domain) }}"
                             buttonText="Access your backoffice"
                             buttonBgColor="{{ $order->course->theme['primary-color'] }}">
    </x-email::call-to-action>

    <x-slot name="footer">
        <x-email::footer><a target="_new" href="{{ url_with_app_http_scheme($order->course->domain) }}">{{ $order->course->name }}</a>. All rights reserved.</x-email::footer>
    </x-slot>
</x-email::newsletter>
