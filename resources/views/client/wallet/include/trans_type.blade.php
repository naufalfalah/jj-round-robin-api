@if (isset($data->amount_in) && !empty($data->amount_in))
<td>
    <p class="text-sm text-center font-weight-bold mb-0">
        Top Up
    </p>
</td>
@elseif (isset($data->amount_out) && !empty($data->amount_out))
<td>
    <p class="text-sm text-center font-weight-bold mb-0">
            Advertisement
    </p>
</td>
@else
<td> - </td>
@endif
