@foreach ($stylesheets as $style)
    {!! Html::style($style) !!}
@endforeach

@foreach ($headScripts as $script)
    @if (is_array($script))
        {!! Html::script($script['url']) !!}
        @if ($script['fallback'])
            <script>window.{!! $script['fallback'] !!} || document.write('<script src="{{ $script['fallbackURL'] }}"><\/script>')</script>
        @endif
    @else
        {!! Html::script($script) !!}
    @endif
@endforeach