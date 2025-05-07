<td>
    <p class="text-sm font-weight-bold mb-0">
        <span class="badge bg-{{ $data->status == 'pending' ? 'warning' : ($data->status == 'approve' ? 'success' : 'danger') }}">
            {{ $data->status == 'pending' ? 'Pending' : ($data->status == 'approve' ? 'Approved' : 'Rejected') }}
        </span>
    </p>
</td>
