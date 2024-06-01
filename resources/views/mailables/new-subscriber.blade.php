<x-email::newsletter title="Thanks for subscribing to {{ $course->name }}"
                     primaryColor="{{ $course->theme['primary-color'] }}"
                     secondaryColor="{{ $course->theme['secondary-color'] }}"
                     dangerColor="{{ $course->theme['danger-color'] }}">
    <x-slot name="preheader">
        Thanks for subscribing to {{ $course->name }}!
    </x-slot>

    <x-slot name="header">
        @if(file_exists(storage_path('app/public/' . $course->canonical . '/' . $course->filename_email_logo)))
        <x-email::header-logo src="{{ Storage::disk('eduka')->url($course->filename_email_logo) }}" alt="{{ $course->name }}" width="150" height="50" />
        @else
        <x-email::header-title color="#333" href="{{ eduka_url($course->domain) }}">{{ $course->name }}</x-email::header-title>
        @endif
    </x-slot>

    <x-email::paragraph>
        <strong>Hi there (eduka template)!</strong><br>
    </x-email::paragraph>

    <x-email::paragraph>
        Thanks a lot for subscribing for my upcoming course, {{ $course->name }}. Super appreciated for your interest!
    </x-email::paragraph>

    @if($course->pioneer_voucher_discount > 0)
    <x-email::paragraph>
        As a good will offer, here you have a special voucher code for you, to use on top of the launch date price:
    </x-email::paragraph>
    <x-email::call-to-action heading="{{ $course->pioneer_voucher_discount }}% off voucher"
                             body="Valid for the first week after the launch date"
                             buttonUrl="#"
                             buttonText="Voucher code: PIONEER"
                             buttonBgColor="{{ $course->theme['primary-color'] }}">
    </x-email::call-to-action>
    @endif

    <x-email::paragraph>
        <br/>I will keep you updated closer to the launch date.
    </x-email::paragraph>


    <x-slot name="footer">
        <x-email::footer><a target="_new" href="https://{{ $course->domain }}">{{ $course->name }}</a>. All rights reserved.</x-email::footer>
    </x-slot>
</x-email::newsletter>
