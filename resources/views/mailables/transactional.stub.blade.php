<x-email::newsletter title="Welcome to [Product Name]!"
                     themeColor="#3869D4"
                     secondaryColor="#22BC66"
                     dangerColor="#FF6136">
    <x-slot name="preheader">
        Thanks for trying out [Product Name]. We’ve pulled together some information and resources to help you get started.
    </x-slot>

    <x-slot name="header">
        <x-email::header-logo src="https://placehold.co/150x50" alt="Logo" width="150" height="50" />
        <!-- Alternatively, you could use the header title component like this: -->
        <!-- <x-email::header-title color="#333" href="https://example.com">[Product Name]</x-email::header-title> -->
    </x-slot>

    <x-email::paragraph>
        Thanks for trying [Product Name]. We’re thrilled to have you on board.
    </x-email::paragraph>

    <x-email::paragraph>
        To get the most out of [Product Name], do this primary next step:
    </x-email::paragraph>

    <x-email::single-button url="#" bgColor="#3869D4" text="Do this Next">
    </x-email::single-button>

    <x-email::paragraph>
        For reference, here's your login information:
    </x-email::paragraph>

    <x-email::details>
        <x-email::detail-line label="Login Page" value="aa@aa.com"></x-email::detail-line>
        <x-email::detail-line label="Username" value="bfalcao"></x-email::detail-line>
    </x-email::details>

    <x-email::paragraph>
        You've started a 3 day trial. You can upgrade to a paying account or cancel any time.
    </x-email::paragraph>

    <x-email::details>
        <x-email::detail-line label="Trial Start Date" value="2024-05-23"></x-email::detail-line>
        <x-email::detail-line label="Trial End Date" value="2025-05-25"></x-email::detail-line>
    </x-email::details>

    <x-email::paragraph>
        If you have any questions, feel free to email our customer success team. (We're lightning quick at replying.) We also offer live chat during business hours.
    </x-email::paragraph>

    <x-email::paragraph>
        Thanks, [Sender Name] and the [Product Name] Team
    </x-email::paragraph>

    <x-email::paragraph>
        <strong>P.S.</strong> Need immediate help getting started? Check out our help documentation. Or, just reply to this email, the [Product Name] support team is always ready to help!
    </x-email::paragraph>

    <x-email::call-to-action heading="10% off your next purchase!"
                             body="Thanks for your support! Here's a coupon for 10% off your next purchase."
                             button_url="http://example.com"
                             button_text="Use this discount now..."
                             button_bg_color="#22BC66">
    </x-email::call-to-action>

    <x-email::thumbnail-text thumbnailUrl="#" thumbnailSrc="https://placehold.co/150x150" thumbnailAlt="Thumbnail" text="This is a new tutorial video that explains the features of our product in detail. Click to watch!">
    </x-email::thumbnail-text>

    <x-email::subline>
        If you’re having trouble with the button above, copy and paste the URL below into your web browser.<br>
        #
    </x-email::subline>

    <x-slot name="footer">
        <x-email::footer>© 2024 [Product Name]. All rights reserved.</x-email::footer>
    </x-slot>
</x-email::newsletter>
