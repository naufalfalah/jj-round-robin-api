<div id="headline-container">
    @foreach ($headlines as $index => $headline)
        <div class="form-group mb-1 headline-form">
            <input type="text" name="ad_headlines[]" id="ad_headline_{{ $index + 1 }}"
                value="{{ old('ad_headlines')[$index] ?? '' }}" placeholder="{{ $headline['label'] }}"
                class="form-control headline-input" {{ $headline['required'] ? 'required' : '' }}
                oninput="updateCharCount(this, {{ $index + 1 }})">
            <div class="d-flex justify-content-between p-1">
                <small class="form-text text-end text-muted">
                    @if($headline['required']) 
                        Required
                    @endif
                </small>
                <small id="char_count_{{ $index + 1 }}" class="form-text text-end text-muted char-count">
                    0/30
                </small>
            </div>
        </div>
    @endforeach
</div>
<button type="button" id="add-headline-btn" class="btn btn-info mt-2 border-1 border-primary"><i class="fas fa-plus"></i> Headline</button>

@push('scripts')
    <script>
        function updateCharCount(input, index) {
            const maxChar = 30;
            let charCount = input.value.length;

            if (charCount > maxChar) {
                input.value = input.value.substring(0, maxChar);
                charCount = maxChar;
            }

            const charCountElement = document.getElementById(`char_count_${index}`);
            charCountElement.textContent = `${charCount}/${maxChar}`;
        }
        
        $(document).ready(function () {
            $('#add-headline-btn').on('click', function() {
                let headlineCount = document.querySelectorAll('input[name="ad_headlines[]"]').length;
                
                if (headlineCount <= 15) {
                    const headlineItem = $('.headline-form').last().clone(true);
                    headlineItem.find('.headline-input').attr('id', `headline_${headlineCount + 1}`);
                    headlineItem.find('.headline-input').attr('placeholder', `Headline ${headlineCount + 1}`);
                    headlineItem.find('.headline-input').val('');
                    headlineItem.find('.headline-input').attr('oninput', `updateCharCount(this, ${headlineCount + 1})`);
                    headlineItem.find('.char-count').attr('id', `char_count_${headlineCount + 1}`);
                    headlineItem.find('.char-count').html('0/30');
                    headlineItem.appendTo('#headline-container');

                    if (headlineCount + 1 == 15) {
                        $('#add-headline-btn').remove();
                    }
                }
            });
        });
    </script>
@endpush