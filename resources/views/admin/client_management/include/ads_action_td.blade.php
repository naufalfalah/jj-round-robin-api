@if ($data->status == 'complete' || $data->status == 'running')
    <center>
        <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View detail"
            data-bs-original-title="View detail" aria-label="Views"><i
                class="bi bi-eye-fill"></i></a>
                &nbsp;
            <a href="{{ route('admin.sub_account.advertisements.view_progress', ['sub_account_id' => session()->get('sub_account_id'), 'ads_id' => $data->hashid, 'client_id' => hashids_encode($data->client_id)]) }}" class="text-primary"
                data-data="{{ $data }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="View Progress"
                data-bs-original-title="View detail" aria-label="Views">
                <i class="bi bi-info-circle-fill"></i>
            </a>
    @if($data->status == 'running')
        <a href="javascrip:void(0);" class="text-warning ads-running" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-status="{{ $data->status }}" data-clientid="{{ $data->client->id}}" data-id="{{$data->id}}}" title="Edit" data-bs-original-title="Edit"
            aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>
    @endif  
    @if($data->status == 'complete')
        <a href="javascrip:void(0);" class="text-primary ads_amt_refund" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-status="{{ $data->status }}" data-clientid="{{ $data->client->id}}" data-id="{{$data->id}}" title="Refund Amount" data-bs-original-title="Refund Amount"
            aria-label="Edit"> <i class="bi bi-currency-dollar"></i></a>
        </a>
    @endif  
    </center>
@else
    <center>
        <a href="javascrip:void(0);" class="text-primary view_detail" data-data="{{ $data }}" data-bs-toggle="tooltip"
            data-bs-placement="bottom" title="View detail"
            data-bs-original-title="View detail" aria-label="Views"><i
                class="bi bi-eye-fill"></i></a>



        <a href="javascrip:void(0);" class="text-warning change-ads-status" data-bs-toggle="tooltip"
            data-bs-placement="bottom" data-data="{{ $data }}" title="Edit" data-bs-original-title="Edit"
            aria-label="Edit"><i class="bi bi-pencil-fill"></i></a>



    </center>

@endif
<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
