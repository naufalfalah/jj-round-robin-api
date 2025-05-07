@if ($data->status == 'pending' || $data->status == 'reject')
<div class="d-flex align-items-center gap-2 fs-6">
    <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="View detail"
        data-bs-original-title="View detail" aria-label="Views"><i
            class="bi bi-eye-fill"></i></a>
    <a href="{{ route('user.ads.edit', $data->hashid) }}" class="text-warning edit-contact" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Edit info" data-bs-original-title="Edit info"
        aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
    <a class="text-danger" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Delete" data-bs-original-title="Delete"
        aria-label="Delete" href="javascrip:void(0);" onclick="ajaxRequest(this)" data-url="{{route('user.ads.delete',$data->hashid)}}"><i class="bi bi-trash-fill"></i></a>
</div>
@else
<a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
data-bs-placement="bottom" title="View detail"
data-bs-original-title="View detail" aria-label="Views"><i
    class="bi bi-eye-fill"></i></a>

@endif

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
