<div class="d-flex align-items-center gap-3 fs-6">
    <a href="{{ route('user.leads-management.client_details',$data->hashid) }}" class="text-primary" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="View detail"
        data-bs-original-title="View detail" aria-label="Views"><i
            class="bi bi-eye-fill"></i></a>
    @if (!isset($type))
    <a href="javascript:;" class="text-warning edit-contact" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Edit info" data-bs-original-title="Edit info"
        aria-label="Edit" data-data="{{ $data }}"><i class="bi bi-pencil-fill"></i></a>
    <a class="text-danger" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Delete" data-bs-original-title="Delete"
        aria-label="Delete" href="javascript:;" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.delete',$data->hashid)}}"><i class="bi bi-trash-fill"></i></a>
    @endif
</div>
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
