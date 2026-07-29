@php
    $popup = \App\Models\PopupModal::currentlyActive()->latest()->first();
@endphp

@if($popup)
    <div
        id="site-popup"
        class="site-popup"
        data-trigger-type="{{ $popup->trigger_type }}"
        data-trigger-value="{{ $popup->trigger_value }}"
        data-frequency-days="{{ $popup->display_frequency_days }}"
        data-popup-id="{{ $popup->id }}"
        hidden
    >
        <div class="site-popup__content">
            {!! $popup->content !!}
            <button type="button" data-popup-close>&times;</button>
        </div>
    </div>
    @vite(['resources/js/site-popup.js'])
@endif
