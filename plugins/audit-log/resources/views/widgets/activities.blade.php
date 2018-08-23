@if ($histories->count() > 0)
<div class="scroller">
    <ul class="item-list padding">
        @foreach ($histories as $history)
            <li>
                @include('plugins.audit-log::activity-line', compact('history'))
            </li>
        @endforeach
    </ul>
</div>
<div class="widget_footer">
    @include('core.dashboard::partials.paginate', ['data' => $histories, 'limit' => $limit])
</div>
@else
    <div class="dashboard_widget_msg">
        <p class="smiley" aria-hidden="true"></p>
        <p>{{ trans('core.base::tables.no_data') }}</p>
    </div>
@endif
