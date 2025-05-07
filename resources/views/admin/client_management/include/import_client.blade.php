<a href="{{ route('admin.sub_account.client-management.import_client', ['sub_account_id' => session('sub_account_id'), 'id' => $data->hashid]) }}" class="text-success" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Import Client" aria-label="Import Client">
    <i class="fa-solid fa-upload"></i>
</a>


<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>