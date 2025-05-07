<select class="form-control change_admin_lead_status" name="change_lead_status" data-id="{{ $data->id }}">
    <option value="">Select Status</option>
    <option value="contacted" {{ $data->admin_status == 'contacted' ? 'selected' : '' }}>Contacted</option>
    <option value="appointment_set" {{ $data->admin_status == 'appointment_set' ? 'selected' : '' }}>Appointment Set</option>
    <option value="burst" {{ $data->admin_status == 'burst' ? 'selected' : '' }}>Burst</option>
    <option value="follow_up" {{ $data->admin_status == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
    <option value="call_back" {{ $data->admin_status == 'call_back' ? 'selected' : '' }}>Call Back</option>
</select>
