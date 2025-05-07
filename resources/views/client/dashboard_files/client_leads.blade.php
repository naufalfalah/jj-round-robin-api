@forelse ($client_leads as $key => $leads)
    <tr>
        <td>{{ $key + 1 }}</td>
        @if(isset($num) && $num == 'show')
            <td>{{ $leads->created_at->format('Y-m-d') ?? '' }}</td>
        @endif
        <td>{{ $leads->name ?? '' }}</td>
        <td>{{ $leads->email ?? '' }}</td>
        @if(isset($num) && $num == 'show')
            <td>{{ $leads->mobile_number ?? '' }}</td>
        @endif
        <td>
            @foreach ($leads->lead_data as $k => $items )
                {{ Str::limit($items->key, 10, '...') ?? '' }} : {{ Str::limit($items->value, 10, '...') ?? '' }} <br>
            @endforeach
        </td>
        <td>
            <select class="form-control change_lead_status" name="change_lead_status" data-id="{{ $leads->id }}">
                <option value="">Select Status</option>
                <option value="contacted" {{ $leads->admin_status == 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="appointment_set" {{ $leads->admin_status == 'appointment_set' ? 'selected' : '' }}>Appointment Set</option>
                <option value="burst" {{ $leads->admin_status == 'burst' ? 'selected' : '' }}>Burst</option>
                <option value="follow_up" {{ $leads->admin_status == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                <option value="call_back" {{ $leads->admin_status == 'call_back' ? 'selected' : '' }}>Call Back</option>
            </select>
        </td>
        <td class="text-center">
            <a href="javascript:void(0);" class="text-primary view_lead_detail" data-data="{{ $leads }}" data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="View Lead Detail"
                data-bs-original-title="View Lead Detail" aria-label="Views"><i
                    class="bi bi-eye-fill"></i></a>
            {{-- <a href="javascript:;" class="text-warning edit-contact" data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="Edit info" data-bs-original-title="Edit info"
                aria-label="Edit" data-data="{{ $leads }}"><i class="bi bi-pencil-fill"></i></a>
            <a class="text-danger" data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="Delete" data-bs-original-title="Delete"
                aria-label="Delete" href="javascript:;" onclick="ajaxRequest(this)" data-url="{{ route('user.leads-management.delete', ['id' => $leads->hashid, 'is_reload' => true]) }}"><i class="bi bi-trash-fill"></i></a> --}}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" align="center">No Lead Found</td>
    </tr>
@endforelse

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
