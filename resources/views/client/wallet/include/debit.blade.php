@if (isset($data->amount_in) && !empty($data->amount_in))
<td>
    <p class="text-sm text-center font-weight-bold mb-0">
        <span class="text-success">+{{number_format($data->amount_in, 2)}}</span>
    </p>
</td>
@else
<p class="text-sm text-center font-weight-bold mb-0">
    <span>-</span>
</p>
@endif
