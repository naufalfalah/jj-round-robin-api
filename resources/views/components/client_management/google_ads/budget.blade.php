<div class="row mb-2">
    <div class="col-6">
        <div class="form-group">
            <label for="campaign_budget_type" class="form-label">Budget</label>
            <select name="campaign_budget_type" id="campaign_budget_type" class="form-select">
                <option value="DAILY" {{ old('campaign_budget_type') == 'DAILY' ? 'selected' : '' }}>Daily</option>
                {{-- <option value="LIFETIME" {{ old('campaign_budget_type') == 'LIFETIME' ? 'selected' : '' }}>Lifetime</option> --}}
            </select>
            @error('campaign_budget_type')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="col-6">
        <div class="form-group">
            <label for="campaign_budget_amount" class="form-label">Amount
                <span class="text-danger fw-bold">*</span>
            </label>
            <input type="number" name="campaign_budget_amount" id="campaign_budget_amount" placeholder="Min 1.00$ SGD"
                class="form-control" value="{{ old('campaign_budget_amount') }}" required>
            @error('campaign_budget_amount')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>
