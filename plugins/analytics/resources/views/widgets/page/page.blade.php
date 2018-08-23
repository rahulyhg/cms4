@if (count($pages) > 0)
    <div class="scroller">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ trans('core.base::tables.url') }}</th>
                    <th>{{ trans('core.base::tables.views') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pages as $page)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td class="text-left"><a href="{{ $page['url'] }}" target="_blank">{{ string_limit_words($page['pageTitle'], 80) }}</a></td>
                        <td>{{ $page['pageViews'] }} ({{ ucfirst(trans('plugins.analytics::analytics.views')) }})</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p>{{ trans('core.base::tables.no_data') }}</p>
@endif