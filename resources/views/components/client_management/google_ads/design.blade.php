<div class="row mb-2" id="ad-search">
    <div class="col-xl-6" id="form-section">
        <div class="row mb-2">
            <div class="col-xl-12">
                <button type="button" class="btn btn-primary d-none ml-auto" id="show-preview">Show Preview</button>
            </div>
        </div>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active">Ad Name</a>
            </li>
        </ul>
        <div class="row mb-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="ad_name" class="form-label">Ad Name
                        <span class="text-danger fw-bold">*</span>
                    </label>
                    <input type="text" name="ad_name" id="ad_name" placeholder=""
                        class="form-control" value="{{ old('ad_name') }}" required>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <div class="form-group">
                    <label for="ad_url" class="form-label">Website URL
                        <span class="text-danger fw-bold">*</span>
                    </label>
                    <input type="url" name="ad_url" id="ad_url" placeholder=""
                        class="form-control" value="{{ old('ad_url') }}" required>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <p class="ad-url">Display Path</p>
            <div class="col-6 d-flex">
                <span class="me-2">/</span>
                <input type="text" name="ad_url_1" id="ad_url_1" placeholder=""
                    class="form-control" value="{{ old('ad_url_1') }}">
            </div>
            <div class="col-6 d-flex">
                <span class="me-2">/</span>
                <input type="text" name="ad_url_2" id="ad_url_2" placeholder=""
                    class="form-control" value="{{ old('ad_url_2') }}">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-6">
                <div class="fw-bolder mb-2">Headlines</div>
                <x-client_management.google_ads.headline_fields :headlines="[
                    ['label' => 'Heading 1', 'required' => true],
                    ['label' => 'Heading 2', 'required' => true],
                    ['label' => 'Heading 3', 'required' => true],
                    ['label' => 'Heading 4', 'required' => false],
                ]" />
            </div>
            <div class="col-xl-6">
                <div class="fw-bolder mb-2">Descriptions</div>
                <x-client_management.google_ads.description_fields :descriptions="[
                    ['label' => 'Description 1', 'required' => true],
                    ['label' => 'Description 2', 'required' => true],
                    ['label' => 'Description 3', 'required' => false],
                ]" />
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-6">
                <div class="fw-bolder">Sitelinks</div>
                <div class="mb-2">
                    <small>Add four or more to maximise performance</small>
                </div>
                <x-client_management.google_ads.sitelink_card sitelinkNumber="1" />
            </div>
            <div class="col-xl-6">
                <div class="fw-bolder">Callout</div>
                <div class="mb-2">
                    <small>Select how many conversions to count per click or interaction</small>
                </div>
                <x-client_management.google_ads.callout_fields :callouts="[
                    ['label' => 'Callout text 1', 'required' => false],
                ]" />
            </div>
        </div>
    </div>
    <div class="col-xl-6 p-4" style="background-color:rgb(230, 230, 230)" id="preview-section">
        <div class="row mb-2">
            <div class="col-xl-12">
                <button type="button" class="btn btn-secondary" id="hide-preview">Hide Preview</button>
            </div>
        </div>
        <div class="d-flex justify-content-center align-items-center py-5">
            <div class="card align-self-center" style="width: 42rem;">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <p class="ad-name"></p>
                        </div>
                        <div class="col-10">
                            <p>&bull; <span class="ad-url"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <p class="ad-headline-1"></p>
                        </div>
                        <div class="col-4">
                            <div class="ps-2 border-left-2">
                                <p class="ad-headline-2"></p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="ps-2 border-left-2">
                                <p class="ad-headline-3"></p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <p class="ad-description-1"></p>
                        </div>
                        <div class="col-6">
                            <div class="ps-2 border-left-1">
                                <p class="ad-description-2"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#hide-preview').on('click', function () {
                $('#preview-section').addClass('d-none');
                $('#form-section').removeClass('col-xl-6').addClass('col-xl-12');
                $('#hide-preview').addClass('d-none');
                $('#show-preview').removeClass('d-none');
            });

            $('#show-preview').on('click', function () {
                $('#preview-section').removeClass('d-none');
                $('#form-section').addClass('col-xl-6').removeClass('col-xl-12');
                $('#hide-preview').removeClass('d-none');
                $('#show-preview').addClass('d-none');
            });
        });
    </script>
@endpush