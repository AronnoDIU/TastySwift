@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            @isset($unsubscribeUrl)
                <div style="margin-top: 10px; color: #6c757d; font-size: 12px;">
                    If you're having trouble clicking the button, copy and paste the URL below into your web browser:
                    <a href="{{ $unsubscribeUrl }}" style="color: #4a6cf7; text-decoration: none; word-break: break-all;">
                        {{ $unsubscribeUrl }}
                    </a>
                </div>
            @endisset
        @endcomponent
    @endslot
@endcomponent
