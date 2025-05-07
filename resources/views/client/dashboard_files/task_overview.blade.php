@forelse ($client_task_overview as $key => $leads)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $leads->title ?? '' }} </td>
        <td>
            <a href="javascript:void(0);" class="text-warning change-status float-end" data-bs-toggle="tooltip"
        data-bs-placement="bottom" title="Change Status" data-bs-original-title="Change Status"
        aria-label="Change Status" data-id="{{ $leads->id }}" data-priority="{{ $leads->priority }}"><i class="bi bi-pencil-fill"></i></a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" align="center">No Task Found</td>
    </tr>
@endforelse

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
