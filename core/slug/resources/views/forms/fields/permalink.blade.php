@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
    @endif
@endif

@if ($showField)
    {!! Form::permalink($name, $options['value'], 0, array_get($options, 'route_create', route('slug.create')), array_get($options, 'route_public', route('public.single', config('core.slug.general.pattern')), url('/'))) !!}
    @include('core.base::forms.partials.help_block')
@endif

@include('core.base::forms.partials.errors')

@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
