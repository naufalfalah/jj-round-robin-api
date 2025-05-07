<div class="row mb-2">
    <div class="col-xl-6">
        <div class="form-group mb-2">
            <textarea class="form-control" name="keywords" id="keywords" cols="30" rows="10">{{ old('keywords') }}</textarea>
            @error('keywords')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>
</div>