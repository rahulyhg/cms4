<span class="log-icon log-icon-{{ $history->type }}"></span>
<span>
    @if ($history->user->id)
        <a href="{{ route('user.profile.view', $history->user->id) }}">{{ $history->user->getFullName() }}</a>
    @endif
    @if (Lang::has('plugins.audit-log::history.' . $history->action)) {{ trans('plugins.audit-log::history.' . $history->action) }} @else {{ $history->action }} @endif
    @if ($history->module)
        @if (Lang::has('plugins.audit-log::history.' . $history->module)) {{ trans('plugins.audit-log::history.' . $history->module) }} @else {{ $history->module }} @endif
    @endif
    @if ($history->reference_name)
        @if (empty($history->user) || $history->user->getFullName() != $history->reference_name)
            "{{ string_limit_words($history->reference_name, 30) }}"
        @endif
    @endif
    .
</span>
<span class="small italic">{{ Carbon::parse($history->created_at)->diffForHumans() }} </span>
<span>({{ $history->ip_address }})</span>