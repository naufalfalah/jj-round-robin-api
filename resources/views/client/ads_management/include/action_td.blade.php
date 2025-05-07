<div class="text-center">
    @if ($data->status == 'pending' || $data->status == 'reject')
        <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View detail"
            data-bs-original-title="View detail" aria-label="Views"><i
                class="bi bi-eye-fill"></i></a>
        <a href="{{ route('user.ads.edit', $data->hashid) }}" class="text-warning edit-contact" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Edit info" data-bs-original-title="Edit info"
            aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
        <a class="text-danger" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Delete" data-bs-original-title="Delete"
            aria-label="Delete" href="javascrip:void(0);" onclick="ajaxRequest(this)" data-titlemsg="Are you sure you want to delete this campaign?" data-msg="Once deleted, this ad campaign will be permanently removed, and we will not be able to attend on it." data-url="{{route('user.ads.delete',$data->hashid)}}"><i class="bi bi-trash-fill"></i></a>
    @elseif ($data->status == 'test')
        <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View detail"
            data-bs-original-title="View detail" aria-label="Views">
            <i class="bi bi-eye-fill"></i></a>
        <a href="{{ route('user.ads.edit', $data->hashid) }}" class="text-warning edit-contact" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Edit info" data-bs-original-title="Edit info"
            aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
        <a href="javascript:void(0);" class="text-info edit_no_lead" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Edit No Lead"
            aria-label="Update No Lead" aria-label="Edit">
            <i class="bi bi-pencil-square"></i>
        </a>
        <a class="text-danger" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Delete" data-bs-original-title="Delete"
            aria-label="Delete" href="javascrip:void(0);" onclick="ajaxRequest(this)" data-titlemsg="Are you sure you want to delete this campaign?" data-msg="Once deleted, this ad campaign will be permanently removed, and we will not be able to attend on it." data-url="{{route('user.ads.delete',$data->hashid)}}"><i class="bi bi-trash-fill"></i></a>
    @else
        <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View detail"
            data-bs-original-title="View detail" aria-label="Views"><i
            class="bi bi-eye-fill"></i></a>
            &nbsp;
        <a href="{{route('user.ads.view_progress', $data->hashid)}}" class="text-primary" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View Progress"
            data-bs-original-title="View detail" aria-label="Views"><i class="bi bi-info-circle-fill"></i></a>
    @endif
</div>

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
