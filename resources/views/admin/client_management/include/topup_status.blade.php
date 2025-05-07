<td>
    @if ($data->status == "approve")
    <center>
        <span>-</span>
    </center>
    @else
        <center>
            <a href="javascript:void(0);" class="text-primary change-topup-status" data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="Change Status" data-bs-original-title="Change Status"
                aria-label="Edit" data-id="{{ $data->id }}" data-status="{{ $data->status }}"><i class="bi bi-pencil-fill"></i></a>
        </center>
    @endif

</td>
