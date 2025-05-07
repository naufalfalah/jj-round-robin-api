<div class="form-group my-2">
    <label for="google_ad_filter">Filter by Google Ads:</label>
    <select id="google_ad_filter" class="form-select">
        <option value="">No Filter</option>
        <option value="all">All</option>
        @foreach ($googleAds as $googleAd)
            <option value="{{ $googleAd->id }}" {{ $googleAd->id == request()->query('filter') ? 'selected' : '' }}>{{ $googleAd->ad_name }} - {{ $googleAd->campaign_resource_name }}</option>
        @endforeach
    </select>
</div>