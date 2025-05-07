<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <label for="ad_request_id" class="form-label">Ad Request
                <span class="text-danger fw-bold">*</span></label>
            <select class="form-select" name="ad_request_id" id="ad_request_id" required>
                <option value="" selected>Select an ad request</option>
                @foreach ($adsRequests as $adsRequest)
                    <option value="{{ $adsRequest->id }}" 
                        data-client-id="{{ $adsRequest->client_id }}"
                        data-google_account_id="{{ $adsRequest->google_account_id }}" 
                        data-customer_id="{{ $adsRequest->customer_id }}" 
                        {{ old('ad_request_id') == $adsRequest->id ? 'selected' : '' }}>
                        {{ $adsRequest->adds_title }} - {{ $adsRequest->client->client_name }} - {{ $adsRequest->client->customer_id }}</option>
                @endforeach
            </select>
            @error('ad_request_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>

<div class="col-xl-6">
    <label for="inputName" class="form-label">Google Account <span></label>
    <select name="google_account_id" id="google_account_id" class="form-control" readonly="readonly" style="pointer-events: none;">
        <option value="">Google account not selected</option>
        @foreach ($googleAccounts as $googleAccount)
            <option value="{{ $googleAccount->id }}">
                {{ $googleAccount->email }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-xl-6">
    <label for="customer_id" class="form-label">Customer ID <span></label>
    <input type="text" class="form-control" id="customer_id" readonly required>
</div>

@push('scripts')
    <script>
        $('#ad_request_id').change(function() {
            // Ambil option yang dipilih
            const selectedOption = $(this).find('option:selected');
            
            // Ambil nilai customer_id dan google_account_id dari data attribute
            const customerID = selectedOption.data('customer_id');
            const googleAccountID = selectedOption.data('google_account_id');
            
            // Set nilai input customer_id
            $('#customer_id').val(customerID);

            // Set nilai select google_account_id
            $('#google_account_id').val(googleAccountID);
        });
    </script>
@endpush