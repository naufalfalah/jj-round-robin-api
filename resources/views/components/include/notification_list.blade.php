@forelse ($notifications as $val)
<a class="dropdown-item" href="{{ $val->lead_url ?? 'javascript:void(0);' }}">
    <div class="d-flex align-items-center">
        <div class="ms-3 flex-grow-1">
            <h6 class="mb-0 dropdown-msg-user">{{ $val->title }} <span class="msg-time float-end text-secondary">{{
                    $val->created_at->diffForHumans() }}</span></h6>
            <small class="mb-0 dropdown-msg-text text-secondary d-flex align-items-center" style="max-width: 60ch; overflow: hidden; text-overflow: ellipsis; white-space: normal; ">{{ $val->body }}</small>
        </div>
    </div>
</a>
@empty
<a class="dropdown-item" href="javascript:void(0);">
    <div class="text-center">
        <h5 class="mb-0">No Notifiaction Found</h5>
    </div>
</a>
@endforelse
