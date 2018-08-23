{!! SeoHelper::render() !!}

@if (env('FACEBOOK_APP_ID'))
    <meta property="fb:app_id" content="{{ env('FACEBOOK_APP_ID') }}">
@endif

{!! Theme::asset()->styles() !!}
{!! Theme::asset()->container('after_header')->styles() !!}
{!! Theme::asset()->container('header')->scripts() !!}
