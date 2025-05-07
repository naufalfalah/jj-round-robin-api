@extends('layouts.admin')

@push('styles')
    <style>
        .border-left-2 {
            border-left: solid black 2px;
        }

        .border-left-1 {
            border-left: solid black 1px;
        }
    </style>
@endpush

@section('content')
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-md-6 flex-grow-1">
                            <h5 class="mb-2">Create Google Ads</h5>
                        </div>
                    </div>
                </div>
            </div>
            

            @if (session('success'))
                <div class="alert alert-success my-2" role="alert">
                    {{ session('success') }}
                </div>
                <!-- <x-client_management.google_ads.website_conversion /> -->
            @elseif (session('error'))
                <div class="alert alert-danger my-2" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.sub_account.client-management.google_ads.store', ['sub_account_id' => $sub_account_id ]) }}" method="post" class="">
                @csrf
                <div class="tab-content mt-3" id="page-1">
                    <div class="tab-pane fade show active" id="Edit-Profile">
                        <div class="card shadow-none border mb-0 radius-15">
                            <div class="card-body">
                                <div class="form-body">
                                    <h3 class="mt-4 mb-2">Client</h3>
                                    <x-client_management.google_ads.ad_request :adsRequests="$ads_requests" :googleAccounts="$google_accounts" />

                                    <hr>
                                    <h3 class="mt-4 mb-2">Configuration</h3>
                                    <x-client_management.google_ads.configuration />

                                    <hr>
                                    <h3 class="mt-4 mb-2">Conversion Goals</h3>
                                    <small class="mb-2">Conversion goals labelled as account default will use data from all of your campaigns to improve your bid strategy and campaign performance, even if they don't seem directly related to Leads.</small>
                                    <x-client_management.google_ads.conversion_goals />

                                    <hr>
                                    <h3>Location</h3>
                                    <x-client_management.google_ads.location />

                                    <hr>
                                    <h3>Language</h3>
                                    <small class="mb-2">Select locations for this campaign</small>
                                    <x-client_management.google_ads.language />

                                    {{-- <hr>
                                    <h3>Audience</h3> --}}

                                    <hr>
                                    <h3>Keywords</h3>
                                    <x-client_management.google_ads.keywords />

                                    <hr>
                                    <h3 class="mt-4 mb-2">Budget</h3>
                                    <x-client_management.google_ads.budget />
                                    <x-client_management.google_ads.bid_cap />
                                    <x-client_management.google_ads.campaign_duration />

                                    <hr>
                                    <h3 class="mt-4 mb-2">Design</h3>
                                    <x-client_management.google_ads.design />

                                    <div class="row mt-4 mb-2">
                                        {{-- <div class="col-12 d-flex justify-content-end">
                                            <button type="button" class="btn btn-dark" id="button-continue">Continue</button>
                                        </div> --}}
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-dark" id="button-submit">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <x-client_management.google_ads.location_modal />
@endsection

@push('scripts')
    <script>
        let clientId = {{ $client->id ?? null }}
        let hiddenGoals = 0;
        console.log('clientId', clientId);
        
        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
        }

        function goalToggle() {
            // console.log("hiddenGoals", hiddenGoals);
            if (hiddenGoals > 0) {
                $('#goal-section').removeClass('d-none');
            } else {
                $('#goal-section').addClass('d-none');
            }
        }
        
        function getVisibleCategories() {
            var categories = [];
            $('#conversion-table-body tr:not(.d-none)').each(function() {
                var category = $(this).attr('id');
                categories.push(category);
            });

            let visibleCategories = categories.join(', ');
            $('input[name="conversion_goals"]').val(visibleCategories);
        }
        
        $(document).ready(function () {
            if (clientId) {
                $('#conversion-table-body').empty()
                $.ajax({
                    url: "{{ route('google_ads.conversion_action') }}",
                    method: 'GET',
                    data: {
                        client_id: clientId,
                    },
                    success: function(data) {
                        // console.log('data.results', data.results);
                        if (data.results) {
                            const groupedByCategory = data.results.reduce((acc, item) => {
                                const category = item.conversionAction.category;
                                if (!acc[category]) {
                                    acc[category] = [];
                                }
                                acc[category].push(item.conversionAction);
                                return acc;
                            }, {});
    
                            let categories = Object.keys(groupedByCategory);
    
                            for (const category of categories) {
                                let items = groupedByCategory[category].length;
                                $('#conversion-table-body').append(`
                                    <tr id="${category}">
                                        <td>${category.toLowerCase().replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</td>
                                        <td>Primary</td>
                                        <td>${items} ${items > 1 ? 'actions': 'action'}</td>
                                        <td>
                                            <button type="button" class="btn btn-dark" data-category="${category}">Remove</button>
                                        </td>
                                    </tr>
                                `);
                            }
                            
                            getVisibleCategories();
    
                            /* console.log(data.results);
                            for (const conversion of data.results) {
                                $('#conversion-table-body').append(`
                                    <tr>
                                        <td>${conversion.conversionAction.name}</td>
                                        <td>Primary</td>
                                        <td>Website</td>
                                        <td>
                                            <button type="button" class="btn btn-dark">Remove</button>
                                        </td>
                                    </tr>
                                `);
                            }
                            */
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
                
                $('#conversion-table-body').on('click', '.btn-dark', function() {
                    var category = $(this).data('category');

                    $(this).closest('tr').addClass('d-none');
                    $('select.form-select option[value="' + category + '"]').removeClass('d-none');

                    hiddenGoals += 1;
                    goalToggle();
                    getVisibleCategories();
                });

                $('select[name="goal"]').on('change', function() {
                    var selectedCategory = $(this).val();
                    // console.log(selectedCategory);
                    if (selectedCategory) {
                        $(this).find('option[value="' + selectedCategory + '"]').addClass('d-none');
                        $('#conversion-table-body').find(`tr#${selectedCategory}`).removeClass('d-none');
                    }

                    $(this).val('');
                    
                    hiddenGoals -= 1;
                    goalToggle();
                    getVisibleCategories();
                });

                $('.location-select').empty()
                $.ajax({
                    url: "{{ route('google_ads.geo_target_constant') }}",
                    method: 'GET',
                    data: {
                        client_id: clientId,
                    },
                    success: function(response) {
                        // console.log('response.results', response.results);
                        $('.location-select').append(`
                            <option value="">Enter a location</option>
                        `);
                        if (response.results) {
                            for (const data of response.results) {
                                $('.location-select').append(`
                                    <option value="${data.geoTargetConstant.resourceName}" data-location-name="${data.geoTargetConstant.name}">
                                        ${data.geoTargetConstant.name}
                                    </option>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            $('#locationModal').modal({ backdrop: 'static', keyboard: false});
            $('#locationModal').modal();

            // $('#ad-search').hide();
            $('#ad-display').hide();

            // $('.automatic-form').hide();
            // $('.manual-form').hide();
    
            $('.custom-duration-form').hide();

            $('#page-2').hide();
            $('.p-budget-max-cpc').hide();
            $('.p-budget-start').hide();
            $('.p-budget-end').hide();

            $('select[name="campaign_type"]').change(function () {
                const value = $(this).val();
                if (value === 'DISPLAY') {
                    // $('.setting-partners').closest('.form-check-inline').hide();
                    $('#ad-search').hide();
                    // $('#ad-display').show();
                } else if (value === 'SEARCH' || value === 'PERFORMANCE_MAX') {
                    $('.setting-partners').closest('.form-check-inline').show();
                    $('#ad-search').show();
                    // $('#ad-display').hide();
                }

                $('a.nav-link.active').text('Ad Name');
            });

            $('input[name="campaign_name"]').on('input', function () {
                var campaignName = $(this).val();
                $('.campaign-name').text(campaignName);
            });

            $('input[name="target_url"]').on('input', function () {
                var targetUrl = $(this).val();
                $('.target-url').text(targetUrl);
            });
            
            $('.setting').change(function () {
                updateSettings();
            });

            function updateSettings() {
                var settings = [];
                if ($('#setting-networks').is(':checked')) {
                    settings.push('Networks');
                }
                if ($('#setting-partners').is(':checked')) {
                    settings.push('Partners');
                }
                $('.settings').text(settings.join(', '));
            }

            $('input[name="ad_name"]').on('input', function () {
                var adName = $(this).val();
                $('a.nav-link.active').text(adName);
                $('.ad-name').text(adName);
                $('.ad-name').text(adName);
            });

            $('input[name="ad_headline"]').on('input', function () {
                var value = $(this).val();
                $('.ad-headline-1').text(value);
            });

            $('#ad_headline_1').on('input', function () {
                var adHeadline1 = $(this).val();
                $('.ad-headline-1').text(adHeadline1);
            });

            $('#ad_headline_2').on('input', function () {
                var adHeadline2 = $(this).val();
                $('.ad-headline-2').text(adHeadline2);
            });

            $('#ad_headline_3').on('input', function () {
                var adHeadline3 = $(this).val();
                $('.ad-headline-3').text(adHeadline3);
            });

            $('#ad_description_1').on('input', function () {
                var adDescription1 = $(this).val();
                $('.ad-description-1').text(adDescription1);
            });

            $('#ad_description_2').on('input', function () {
                var adDescription2 = $(this).val();
                $('.ad-description-2').text(adDescription2);
            });

            // Preview uploaded logo image
            $('input[name="logo"]').on('change', function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.img-logo').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            // Preview uploaded multimedia
            $('input[name="multimedia_1"]').on('change', function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.img-multimedia').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            $('select[name="budget_type"]').change(function () {
                var budgetType = $(this).val();
                if (budgetType === 'DAILY') {
                    $('.budget-type').text('Daily')
                } else if (budgetType === 'LIFETIME') {
                    $('.budget-type').text('Lifetime')
                }

                $('a.nav-link.active').text('Ad Name');
            });

            $('input[name="budget_amount"]').on('input', function () {
                var budgetAmount = $(this).val();
                $('.budget-total').text(budgetAmount);
            });

            $('input[name="bid_cap_amount"]').on('input', function () {
                var value = $(this).val();
                $('.budget-max-cpc').text(value);
            });

            // $('input[name="budget_start"]').on('input', function () {
            //     var value = $(this).val();
            //     $('.budget-start').text(value);
            // });

            // $('input[name="budget_end"]').on('input', function () {
            //     var value = $(this).val();
            //     $('.budget-end').text(value);
            // });

            // Initial check for bid cap on page load
            var bidCapManualChecked = $('#bid-cap-manual').is(':checked');
            var bidCapAutomaticChecked = $('#bid-cap-automatic').is(':checked');
            if (bidCapManualChecked) {
                $('.manual-form').show();
                $('.automatic-form').hide();
            } else if (bidCapAutomaticChecked) {
                $('.manual-form').hide();
                $('.automatic-form').show();
            }

            // Event listener for bid cap toggle
            $('.bid-cap').change(function () {
                if (this.checked) {
                    $('.bid-cap').not(this).prop('checked', false);

                    // Show/Hide forms based on selected bid cap
                    if ($(this).attr('id') === 'bid-cap-manual') {
                        $('.manual-form').show();
                        $('.automatic-form').hide();
                        $('.budget-bid-cap').text('Manual');
                        $('.p-budget-max-cpc').show();
                    } else if ($(this).attr('id') === 'bid-cap-automatic') {
                        $('.manual-form').hide();
                        $('.automatic-form').show();
                        $('.budget-bid-cap').text('Automatic');
                        $('.p-budget-max-cpc').hide();
                    }
                } else {
                    // Hide all forms if none are selected
                    $('.manual-form').hide();
                    $('.automatic-form').hide();
                }
            });

            // Event listener for campaign duration toggle
            $('.campaign-duration').change(function () {
                if (this.checked) {
                    $('.campaign-duration').not(this).prop('checked', false);
                }

                if ($(this).attr('id') === 'campaign-duration-custom') {
                    $('.custom-duration-form').show();
                    $('.p-budget-start').show();
                    $('.p-budget-end').show();
                } else {
                    $('.custom-duration-form').hide();
                    $('.p-budget-start').hide();
                    $('.p-budget-end').hide();
                }
            });

            // Initial check on page load
            $('.campaign-duration:checked').trigger('change');
            
            function toggleAdvancedSearch() {
                if ($('#location-another').is(':checked')) {
                    $('#advanced-search-container').show();
                } else {
                    $('#advanced-search-container').hide();
                }
            }

            // Initial check on page load
            toggleAdvancedSearch();

            // Check on change
            $('input[name="location"]').change(function() {
                toggleAdvancedSearch();
            });

            $('#location-name-section').hide();
            $('#location-radius-section').hide();

            // Function to check which radio button is selected and show/hide elements accordingly
            function checkLocationType() {
            if ($('#location-advance-name').is(':checked')) {
                $('#location-name-section').show();
                $('#location-radius-section').hide();
            } else if ($('#location-advance-radius').is(':checked')) {
                $('#location-name-section').hide();
                $('#location-radius-section').show();
            }
            }

            // Initial check to set the correct visibility
            checkLocationType();

            // Add change event listener to radio buttons
            $('input[name="location_advance"]').on('change', function() {
                checkLocationType();
            });

            $('#button-continue').click(function () {
                $('#page-1').hide();
                $('#page-2').show();
            });

            $('#button-previous').click(function () {
                $('#page-1').show();
                $('#page-2').hide();
            });

            $('.js-example-basic-multiple').select2();
            $('.js-example-basic-multiple-2').select2();
        });

        $('form').on('submit', function(e) {
            if ($('#customer_id').val() === '') {
                e.preventDefault();
                alert('Customer ID is required.');
                $('#customer_id').addClass('is-invalid');
            }
        });
    </script>
@endpush
