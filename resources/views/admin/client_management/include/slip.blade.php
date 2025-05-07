<center>
    @foreach(explode(',', $data->proof) as $key => $imagePath)
    @if (isset($imagePath) && !empty($imagePath))
        <td>
            <a href="{{ asset($imagePath) }}" target="_blank" class="text-primary"
                onmouseover="this.style.textDecoration='underline'"
                onmouseout="this.style.textDecoration='none'"
                data-bs-toggle="tooltip"
                data-bs-placement="bottom" title="View Deposit Slip {{ $key + 1 }}" aria-label="View Deposit Slip {{ $key + 1 }}">
                Proof Image {{ $key + 1 }}
            </a><br>
        </td>
    @else
    <td>-</td>
    @endif
@endforeach
</center>




<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
