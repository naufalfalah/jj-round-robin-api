<div id="callout-container">
    @foreach ($callouts as $index => $callout)
        <div class="form-group mb-2 callout-form">
            <input type="text" name="ad_callouts[]" id="ad_callout_{{ $index + 1 }}"
                value="{{ old('ad_callouts')[$index] ?? '' }}" placeholder="{{ $callout['label'] }}"
                class="form-control callout-input" {{ $callout['required'] ? 'required' : '' }}
                oninput="updateCalloutCharCount(this, {{ $index + 1 }})">
            <div class="d-flex justify-content-end p-1">
                <small id="callout_char_count_{{ $index + 1 }}" class="form-text text-end text-muted char-count">
                    0/25
                </small>
            </div>
        </div>
    @endforeach
</div>
<button type="button" id="add-callout-btn" class="btn btn-info mt-2 border-1 border-primary"><i class="fas fa-plus"></i> Callout</button>

@push('scripts')
    <script>
        function updateCalloutCharCount(input, index) {
            const maxChar = 25;
            let charCount = input.value.length;

            if (charCount > maxChar) {
                input.value = input.value.substring(0, maxChar);
                charCount = maxChar;
            }

            const charCountElement = document.getElementById(`callout_char_count_${index}`);
            charCountElement.textContent = `${charCount}/${maxChar}`;
        }

        $(document).ready(function () {
            $('#add-callout-btn').on('click', function() {
                let calloutCount = document.querySelectorAll('input[name="ad_callouts[]"]').length;
                
                if (calloutCount <= 4) {
                    const calloutItem = $('.callout-form').last().clone(true);
                    calloutItem.find('.callout-input').attr('id', `ad_callout_${calloutCount + 1}`);
                    calloutItem.find('.callout-input').attr('placeholder', `Callout text ${calloutCount + 1}`);
                    calloutItem.find('.callout-input').val('');
                    calloutItem.find('.callout-input').attr('oninput', `updateCalloutCharCount(this, ${calloutCount + 1})`);
                    calloutItem.find('.char-count').attr('id', `callout_char_count_${calloutCount + 1}`);
                    calloutItem.find('.char-count').html('0/25');
                    calloutItem.appendTo('#callout-container');

                    if (calloutCount + 1 == 4) {
                        $('#add-callout-btn').remove();
                    }
                }
            });
        });
    </script>
@endpush