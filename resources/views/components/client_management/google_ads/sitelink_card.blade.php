<div id="sitelink-container">
    <div class="card mb-3 sitelink-card">
        <div class="card-body">
            <div class="fw-bold mb-4 card-title">Sitelink {{ $sitelinkNumber }}</div>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-group">
                        <input type="text" name="sitelinks[{{ $sitelinkNumber }}][text]" id="ad_sitelink_{{ $sitelinkNumber }}"
                            placeholder="Sitelink text" class="form-control sitelink-text">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-xl-6">
                    <div class="form-group">
                        <input type="text" name="sitelinks[{{ $sitelinkNumber }}][line1]" id="ad_sitelink_{{ $sitelinkNumber }}_line1"
                            placeholder="Description line 1 (recommended)" class="form-control sitelink-line1">
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="form-group">
                        <input type="text" name="sitelinks[{{ $sitelinkNumber }}][line2]" id="ad_sitelink_{{ $sitelinkNumber }}_line2"
                            placeholder="Description line 2 (recommended)" class="form-control sitelink-line2">
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-12">
                    <div class="form-group">
                        <input type="text" name="sitelinks[{{ $sitelinkNumber }}][url]" id="ad_sitelink_{{ $sitelinkNumber }}_url"
                            placeholder="Final URL" class="form-control sitelink-url">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<button type="button" id="add-sitelink-btn" class="btn btn-info mt-2 border-1 border-primary"><i class="fas fa-plus"></i> Sitelink</button>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#add-sitelink-btn').on('click', function() {
                let sitelinkCount = document.querySelectorAll('.sitelink-card').length;
                
                if (sitelinkCount <= 4) {
                    const sitelinkItem = $('.sitelink-card').last().clone(true);
                    sitelinkItem.find('.card-title').html(`Sitelink ${sitelinkCount + 1}`);
                    sitelinkItem.find('.sitelink-text').attr('id', `ad_sitelink_${sitelinkCount + 1}`);
                    sitelinkItem.find('.sitelink-text').attr('name', `sitelinks[${sitelinkCount + 1}][text]`);
                    sitelinkItem.find('.sitelink-text').val('');

                    sitelinkItem.find('.sitelink-line1').attr('id', `ad_sitelink_${sitelinkCount + 1}_line1`);
                    sitelinkItem.find('.sitelink-line1').attr('name', `sitelinks[${sitelinkCount + 1}][line1]`);
                    sitelinkItem.find('.sitelink-line1').val('');
                    sitelinkItem.find('.sitelink-line2').attr('id', `ad_sitelink_${sitelinkCount + 1}_line2`);
                    sitelinkItem.find('.sitelink-line2').attr('name', `sitelinks[${sitelinkCount + 1}][line2]`);
                    sitelinkItem.find('.sitelink-line2').val('');

                    sitelinkItem.find('.sitelink-url').attr('id', `ad_sitelink_${sitelinkCount + 1}_url`);
                    sitelinkItem.find('.sitelink-url').attr('name', `sitelinks[${sitelinkCount + 1}][url]`);
                    sitelinkItem.find('.sitelink-url').val('');
                    
                    sitelinkItem.appendTo('#sitelink-container');

                    if (sitelinkCount + 1 == 4) {
                        $('#add-sitelink-btn').remove();
                    }
                }
            });
        });
    </script>
@endpush