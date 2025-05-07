<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="objective" class="form-label">Objective</label>
            <select class="form-select" name="objective" disabled>
                <option value="LEADS">Leads</option>
            </select>
            @error('objective')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="campaign_name" class="form-label">Campaign Name
                <span class="text-danger fw-bold">*</span></label>
            <input type="text" name="campaign_name" id="campaign_name" placeholder="Campaign Name"
                class="form-control" value="{{ old('campaign_name') }}" required>
            @error('campaign_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    {{-- <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="campaign_target_url" class="form-label">Target URL
                <span class="text-danger fw-bold">*</span></label>
            <input type="text" name="campaign_target_url" id="campaign_target_url" placeholder="Target URL"
                class="form-control" value="{{ old('campaign_target_url') }}" required>
            @error('campaign_target_url')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div> --}}
</div>
<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="campaign_type" class="form-label">Campaign Type
                <span class="text-danger fw-bold">*</span></label>
            <select class="form-select" name="campaign_type" required>
                <option value="" selected>Select a campaign type</option>
                <option value="SEARCH" {{ old('campaign_type') == 'SEARCH' ? 'selected' : '' }}>Search</option>
                <option value="PERFORMANCE_MAX" {{ old('campaign_type') == 'PERFORMANCE_MAX' ? 'selected' : '' }}>Performance Max</option>
            </select>
            @error('campaign_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    {{-- <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="inputName" class="form-label">Settings
                <span class="text-danger fw-bold">*</span></label>
            <div class="form-group">
                <div class="form-check form-check-inline setting-networks">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input setting" type="checkbox" role="switch" id="setting-networks">
                        <label class="form-check-label" for="setting-networks">Networks</label>
                    </div>
                </div>
                <div class="form-check form-check-inline setting-partners">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input setting" type="checkbox" role="switch" id="setting-partners">
                        <label class="form-check-label" for="setting-partners">Partners</label>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
</div>