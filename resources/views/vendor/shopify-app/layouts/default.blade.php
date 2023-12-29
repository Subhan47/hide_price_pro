
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('shopify-app.app_name') }}</title>

        <link rel="stylesheet" href="{{ asset('uptown.css') }}">
        <link rel="stylesheet" href="{{ asset('style.css') }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        @yield('styles')

@if(\Osiset\ShopifyApp\Util::getShopifyConfig('appbridge_enabled'))
            <script src="https://unpkg.com/@shopify/app-bridge{{ \Osiset\ShopifyApp\Util::getShopifyConfig('appbridge_version') ? '@'.config('shopify-app.appbridge_version') : '' }}"></script>
            <script src="https://unpkg.com/@shopify/app-bridge-utils{{ \Osiset\ShopifyApp\Util::getShopifyConfig('appbridge_version') ? '@'.config('shopify-app.appbridge_version') : '' }}"></script>
            <script
                @if(\Osiset\ShopifyApp\Util::getShopifyConfig('turbo_enabled'))
                    data-turbolinks-eval="false"
                @endif
            >
                var AppBridge = window['app-bridge'];
                var actions = AppBridge.actions;
                var utils = window['app-bridge-utils'];
                var createApp = AppBridge.default;
                var app = createApp({
                    apiKey: "{{ \Osiset\ShopifyApp\Util::getShopifyConfig('api_key', base64_decode(\Request::get('host'))) }}",
                    shopOrigin: "{{ base64_decode(\Request::get('host')) }}",
                    host: "{{ \Request::get('host') }}",
                    forceRedirect: true,
                });
            </script>

            @include('shopify-app::partials.token_handler')
            @include('shopify-app::partials.flash_messages')
        @endif

    </head>

    <body>
        <div class="app-wrapper">
            <div class="app-content">
                <main role="main">
                <div class="lds-ring app-loader"></div>
                    @yield('content')
                </main>
            </div>
        </div>
        @yield('scripts')
    </body>
</html>
