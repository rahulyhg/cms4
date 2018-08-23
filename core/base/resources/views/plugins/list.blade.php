@extends('core.base::layouts.master')
@section('content')
    <div id="plugin-list" class="clearfix app-grid--blank-slate row">
        @foreach ($list as $plugin)
            <div class="app-card-item col-lg-3 col-md-6 col-sm-6 col-xs-12">
                <div class="app-item app-{{ $plugin->path }}">
                    <div class="app-icon">
                        @if ($plugin->image)
                            <img src="data:image/png;base64,{{ $plugin->image }}">
                        @endif
                    </div>
                    <div class="app-details">
                        <h4 class="app-name">{{ $plugin->name }}</h4>
                    </div>
                    <div class="app-footer">
                        <div class="app-description" title="{{ $plugin->description }}">{{ $plugin->description }}</div>
                        <div class="app-author">{{ trans('core.base::system.author') }}: <a href="{{ $plugin->url }}" target="_blank">{{ $plugin->author }}</a></div>
                        <div class="app-version">{{ trans('core.base::system.version') }}: {{ $plugin->version }}</div>
                        <div class="app-actions">
                            <a class="btn @if ($plugin->status) btn-warning @else btn-info @endif btn-trigger-change-status" data-plugin="{{ $plugin->path }}" data-status="{{ $plugin->status }}">@if ($plugin->status) {{ trans('core.base::system.deactivate') }} @else {{ trans('core.base::system.activate') }} @endif</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop
