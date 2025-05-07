@if (isset($data->address))
<td>
    <a href="javascript:void(0);" class="text-sm text-dark font-weight-bold mb-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ $data->address }}">{{ Str::limit($data->address, 30, "...") ?? "No Address Found"}}</a>
</td> 
@else
    <td>-</td>
@endif


<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>