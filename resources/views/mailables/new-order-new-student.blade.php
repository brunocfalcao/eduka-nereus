<x-email::newsletter title="Thanks for buying {{ $order->course->name }}"
                     primaryColor="{{ $order->course->theme['primary-color'] }}"
                     secondaryColor="{{ $order->course->theme['secondary-color'] }}"
                     dangerColor="{{ $order->course->theme['danger-color'] }}">
    <x-slot name="preheader">
        Thanks for buying {{ $order->course->name }}!
    </x-slot>

    <x-slot name="header">
        <x-email::header-logo src="{{ eduka_url($order->course->filename_email_logo) }}" alt="{{ $order->course->domain }}" width="150" height="50" />
        <!-- Alternatively, you could use the header title component like this: -->
        <!-- <x-email::header-title color="#333" href="https://example.com">[Product Name]</x-email::header-title> -->
    </x-slot>

    <x-email::paragraph>
        <strong>Thank you so much for buying my course!</strong><br>
    </x-email::paragraph>

    <x-email::paragraph>
    	Since it's the first time you're onboarded on {{ $order->course->backend->name }}, where you can watch all the course videos,
    	you will first need to reset your password.
    </x-email::paragraph>

    <x-email::call-to-action heading="Click on the button to reset your password"
                             body="Valid for the first week after the launch date"
                             buttonUrl="{{ $resetLink }}"
                             buttonText="Reset your password"
                             buttonBgColor="{{ $order->course->theme['primary-color'] }}">
    </x-email::call-to-action>

    <x-email::paragraph>
        <br/>After resetting your password you can login on <a target="_new" href="https://{{ $order->course->backend->domain }}/login">{{ $order->course->backend->name }}</a>
    </x-email::paragraph>

    <x-slot name="footer">
        <x-email::footer><a target="_new" href="https://{{ $order->course->domain }}">{{ $order->course->name }}</a>. All rights reserved.</x-email::footer>
    </x-slot>
</x-email::newsletter>
