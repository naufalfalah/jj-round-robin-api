@if (isset($data->amount_out) && !empty($data->amount_out))
<td>
    <p class="text-sm text-center font-weight-bold mb-0">
        <span class="text-danger">-{{number_format($data->amount_out, 2)}}</span>
    </p>
</td>
@else
<td> <p class="text-sm text-center font-weight-bold mb-0">
        <span class="">-</span>
    </p>
</td>
@endif
