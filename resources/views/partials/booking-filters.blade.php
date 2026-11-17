<form method="GET" action="{{ url()->current() }}" class="bj-operations-filter" aria-label="{{ __('operations.filter') }}">
    <label>{{ $auditOnly ?? false ? __('operations.audit_search') : __('operations.search') }}<input type="search" name="q" value="{{ request('q') }}" maxlength="100"></label>
    <label>{{ __('operations.from') }}<input type="date" name="from" value="{{ request('from') }}"></label>
    <label>{{ __('operations.to') }}<input type="date" name="to" value="{{ request('to') }}"></label>
    @unless($auditOnly ?? false)
    <label>{{ __('operations.status') }}<select name="status"><option value="">{{ __('operations.all_statuses') }}</option>
        @foreach(['pending' => __('messages.pending'), 'selesai' => __('messages.completed'), 'dibatalkan' => __('messages.cancelled'), 'expired' => __('operations.expired')] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach
    </select></label>
    @if($showChannel ?? false)<label>{{ __('operations.channel') }}<select name="channel"><option value="">{{ __('operations.all_channels') }}</option><option value="online" @selected(request('channel') === 'online')>{{ __('operations.online') }}</option><option value="offline" @selected(request('channel') === 'offline')>{{ __('operations.offline') }}</option></select></label>@endif
    @endunless
    <div class="bj-operations-actions"><button type="submit">{{ __('operations.filter') }}</button><a href="{{ url()->current() }}">{{ __('operations.reset') }}</a></div>
</form>
