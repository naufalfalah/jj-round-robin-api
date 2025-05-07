<div class="text-center">
    <a href="javascript:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="View detail" aria-label="Views"><i class="bi bi-eye-fill"></i></a>
    
    @if ($data->status == 'pending' || $data->status == 'reject')
        <a href="{{ route('admin.sub_account.client-management.ads_edit', ['sub_account_id' => $sub_account_id, 'id' => $data->hashid]) }}" class="text-warning edit-contact" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Edit info" aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
        <a class="text-danger" href="javascript:void(0);" onclick="ajaxRequest(this)" data-url="{{ route('admin.sub_account.client-management.ads_delete', ['sub_account_id' => $sub_account_id, 'id' => $data->hashid]) }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="Delete" aria-label="Delete"><i class="bi bi-trash-fill"></i></a>
    @endif
</div>

@push('scripts')
    <script>
        $(function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush