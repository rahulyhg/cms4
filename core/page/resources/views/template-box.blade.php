<div class="widget meta-boxes">
    <div class="widget-title">
        <h4><span>{{ trans('core.base::forms.template') }}</span></h4>
    </div>
    <div class="widget-body">
        <div class="form-group @if ($errors->has('template')) has-error @endif">
            {!! Form::select('template', $templates, $selected, ['class' => 'form-control select-full', 'id' => 'template']) !!}
            {!! Form::error('template', $errors) !!}
        </div>
    </div>
</div>