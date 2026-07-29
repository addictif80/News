@php
    $generalSettings = app(\App\Settings\GeneralSettings::class);
@endphp

<footer class="site-footer">
    <div class="site-footer__newsletter">
        <p>Restez informé, inscrivez-vous à notre newsletter.</p>
        @include('site.blocks.newsletter-signup')
    </div>

    <div class="site-footer__bottom">
        <span>&copy; {{ now()->year }} {{ $generalSettings->site_name }}</span>
    </div>
</footer>
