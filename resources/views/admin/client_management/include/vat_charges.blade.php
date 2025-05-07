@if (isset($data->vat_charges) && !empty($data->vat_charges))
<td>
    <p class="text-sm text-center font-weight-bold mb-0">
        <span class="text-danger">-{{ $data->vat_charges }}%</span>
    </p>
</td>
@else
<td> <p class="text-sm text-center font-weight-bold mb-0">
        <span class="">-</span>
    </p>
</td>
@endif
