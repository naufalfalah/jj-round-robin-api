<td>
    <p class="text-sm font-weight-bold mb-0">
        @if ($data->e_wallet)
            <span class="badge bg-info">
                {{ snake_to_sentence_case($data->e_wallet) }}
            </span>
        @endif
    </p>
</td>
