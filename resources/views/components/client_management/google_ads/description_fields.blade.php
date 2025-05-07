<div id="description-container">
    @foreach ($descriptions as $index => $description)
        <div class="form-group mb-1 description-form">
            <input type="text" name="ad_descriptions[]" id="ad_description_{{ $index + 1 }}"
                value="{{ old('ad_descriptions')[$index] ?? '' }}" placeholder="{{ $description['label'] }}"
                class="form-control description-input" {{ $description['required'] ? 'required' : '' }}
                oninput="updateDescriptionCharCount(this, {{ $index + 1 }})">
            <div class="d-flex justify-content-between p-1">
                <small class="form-text text-end text-muted">
                    @if($description['required']) 
                        Required
                    @endif
                </small>
                <small id="description_char_count_{{ $index + 1 }}" class="form-text text-muted char-count">
                    0/90
                </small>
            </div>
        </div>
    @endforeach
</div>
<button type="button" id="add-description-btn" class="btn btn-info mt-2 border-1 border-primary"><i class="fas fa-plus"></i> Description</button>


@push('scripts')
    <script>
        function updateDescriptionCharCount(input, index) {
            const maxChar = 90;
            let charCount = input.value.length;

            if (charCount > maxChar) {
                input.value = input.value.substring(0, maxChar);
                charCount = maxChar;
            }

            const charCountElement = document.getElementById(`description_char_count_${index}`);
            charCountElement.textContent = `${charCount}/${maxChar}`;
        }
        
        $(document).ready(function () {
            $('#add-description-btn').on('click', function() {
                let descriptionCount = document.querySelectorAll('input[name="ad_descriptions[]"]').length;
                
                if (descriptionCount <= 4) {
                    const descriptionItem = $('.description-form').last().clone(true);
                    descriptionItem.find('.description-input').attr('id', `ad_description_${descriptionCount + 1}`);
                    descriptionItem.find('.description-input').attr('placeholder', `Description ${descriptionCount + 1}`);
                    descriptionItem.find('.description-input').val('');
                    descriptionItem.find('.description-input').attr('oninput', `updateDescriptionCharCount(this, ${descriptionCount + 1})`);
                    descriptionItem.find('.char-count').attr('id', `description_char_count_${descriptionCount + 1}`);
                    descriptionItem.find('.char-count').html('0/90');
                    descriptionItem.appendTo('#description-container');

                    if (descriptionCount + 1 == 4) {
                        $('#add-description-btn').remove();
                    }
                }
            });
        });
    </script>
@endpush