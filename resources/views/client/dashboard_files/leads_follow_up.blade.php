@forelse ($client_leads_follow_up as $key => $leads)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $leads->name ?? '' }}</td>
        <td>{{ $leads->follow_up_date_time ?? '' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="12" align="center">No FollowUp Found</td>
    </tr>
@endforelse

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
