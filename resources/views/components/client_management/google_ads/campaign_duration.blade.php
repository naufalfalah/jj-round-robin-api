<div class="row mb-2">
    <b class="mt-4 mb-2">Campaign duration</b>
    <div class="col-xl-4">
        <div class="form-group mb2">
            <div class="form-check form-switch mb-2">
                <label class="form-check-label" for="campaign-duration-custom">Custom</label>
                <input class="form-check-input campaign-duration" type="checkbox" role="switch" name="campaign_duration" id="campaign-duration-custom" value="custom" @checked(old('campaign_duration') == 'custom')>
            </div>
            <div class="row custom-duration-form">
                <div class="col-6">
                    <label for="campaign_start_date" class="form-label">Start
                        <span class="text-danger fw-bold">*</span>
                    </label>
                    <input type="date" name="campaign_start_date" id="campaign_start_date" 
                        value="{{ old('campaign_start_date') }}" placeholder="" class="form-control">
                </div>
                @error('campaign_start_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
                <div class="col-6">
                    <label for="campaign_end_date" class="form-label">End
                        <span class="text-danger fw-bold">*</span>
                    </label>
                    <input type="date" name="campaign_end_date" id="campaign_end_date"
                        value="{{ old('campaign_end_date') }}" placeholder="" class="form-control">
                </div>
                @error('campaign_end_date')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="form-check form-switch mb-2">
            <label class="form-check-label" for="campaign-duration-week">1 Week</label>
            <input class="form-check-input campaign-duration" type="checkbox" role="switch" name="campaign_duration" id="campaign-duration-week" value="week" @checked(old('campaign_duration', 'week') == 'week')>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="form-check form-switch mb-2">
            <label class="form-check-label" for="campaign-duration-month">1 Month</label>
            <input class="form-check-input campaign-duration" type="checkbox" role="switch" name="campaign_duration" id="campaign-duration-month" value="month" @checked(old('campaign_duration') == 'month')>
        </div>
    </div>
    @error('budget_end')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>