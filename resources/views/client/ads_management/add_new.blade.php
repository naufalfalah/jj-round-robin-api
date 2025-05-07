@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
<style>
    .error {
        color: red;
    }

    #ads_add_form {
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0 !important;
    }
</style>
@endsection
@section('content')


@push('styles')
<link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
<style>
    .introjs-tooltip {
        max-width: 600px;
    }

    .introjs-tooltip-header,
    .introjs-tooltipbuttons .introjs-skipbutton,
    .introjs-tooltipbuttons .introjs-prevbutton {
        display: none !important;
    }

    .introjs-tooltipReferenceLayer {
        z-index: 25 !important;
    }

    .introjs-showElement {
        z-index: 23 !important;
    }

    .introjs-overlay {
        z-index: 22 !important;
    }

    .introjs-helperLayer {
        z-index: 21 !important;
    }

    @media (max-width: 768px) {
        .introjs-tooltiptext {
            font-size: 12px;
        }
    }

    .swal2-popup {
        width: 692px !important;
    }
</style>
@endpush

@push('scripts-head')
<script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush

@include('components.client_nav_tabs')

<div class="container-fluid py-4 w mt-5" id="ads_add_form">
    <div class="p-2 mt-3 campaing_summary" style="background-color: rgb(229, 238, 255); {{isset($edit) && $edit->daily_budget < 100 ? '' : 'display:none'}}">
        <div style="font-weight: bolder; font-size: 12px; color: #39548a;"
            class="mt-1 d-flex justify-content-between px-2">
            <span id="s_camp_text"><i class="bi bi-info-circle-fill"></i> Note: <br> If your daily advertising budget
                falls to $100 or below, we strongly recommend consulting with your account manager to discuss your ad
                performance. <br> Based on our experience, a daily budget of $100-$150 is the minimum recommended to
                achieve better ad performance and visibility.</span>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-xl-7 mx-auto">
            <div class="card" id="section-1">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{$breadcrumb}}</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('user.ads.save') }}" class="row g-3 ajaxForm">
                            @csrf
                            <input type="hidden" name="id" value="{{ $edit->id ?? ''}}">
                            <input type="hidden" id="is_popup" value='{{@$edit->daily_budget < 100 ? 1 : 0}}'>
                            <div class="col-md-12" id="step-1">
                                <label for="">Title<span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" value="{{ $edit->adds_title ?? ''}}"
                                    placeholder="Enter Ads Title" class="form-control">
                            </div>


                            <div class="col-md-6" id="step-2">
                                <h6>How would you like to spend your budget? <span class="text-danger">*</span></h6>
                                <div class="d-flex flex-lg-row flex-md-row flex-column gap-2">
                                    <div>
                                        <input class="form-check-input spend_type" id="daily" type="radio" value="daily"
                                            aria-label="Radio button for following text input" name="spend_type" {{
                                            @$edit->spend_type == 'daily' ? 'checked' : '' }} required>
                                        <label for="daily">Daily</label>
                                    </div>
                                    <div>
                                        <input class="form-check-input spend_type" id="monthly" type="radio"
                                            value="monthly" aria-label="Radio button for following text input"
                                            name="spend_type" {{ @$edit->spend_type == 'monthly' ? 'checked' : '' }}
                                        required>
                                        <label for="monthly">Monthly</label>
                                    </div>
                                </div>
                                <label id="spend-error" class="error" for="spend_type" style="display: none">This field
                                    is required.</label>
                            </div>
                            <div class="col-md-6" id="step-3">
                                <label for="">How much you want to spend?<span class="text-danger">*</span></label>
                                <input type="hidden" name="old_spend_amount" Value="{{ $edit->spend_amount ?? ''}}">
                                <input type="number" name="spend_amount" id="spend" min="1" 
                                    value="{{ $edit->daily_budget ?? ''}}" placeholder="Enter amount you want to spend"
                                    class="form-control" required>
                            </div>


                            <div class="col-md-6" id="step-4">
                                <h6>Domain Name: <span class="text-danger">*</span></h6>
                                <div class="d-flex flex-lg-row flex-md-row flex-column gap-2">
                                    <div>
                                        <input class="form-check-input domain_is" id="i_have_my_own_domain" type="radio"
                                            value="i_have_my_own_domain"
                                            aria-label="Radio button for following text input" name="domain_is" {{
                                            @$edit->domain_is == 'i_have_my_own_domain' ? 'checked' : '' }} {{ @$edit->is_domain_pay == 1 ? 'disabled' : '' }} required>
                                        <label for="i_have_my_own_domain">I have my own domain </label>
                                    </div>
                                </div>
                                <div class="d-flex flex-lg-row flex-md-row flex-column gap-2">
                                    <div>
                                        <input class="form-check-input domain_is" id="request_to_purchase" type="radio"
                                            value="request_to_purchase"
                                            aria-label="Radio button for following text input" name="domain_is" {{
                                            @$edit->domain_is == 'request_to_purchase' ? 'checked' : '' }} {{ @$edit->is_domain_pay == 1 ? 'disabled' : '' }} required>
                                        <label for="request_to_purchase">Request to Purchase</label>
                                    </div>
                                </div>
                                @if(@$edit->is_domain_pay == 1)
                                <input type="hidden" value="{{$edit->domain_is}}" name="domain_is">
                                @endif                
                                <label id="spend-error" class="error" for="domain_is" style="display: none">This field
                                    is required.</label>
                            </div>

                            <div class="col-md-6" id="step-5">
                                <label for="">Website domain name <span class="text-muted">(optional)</span></label>
                                <input type="text" name="domain_name" id="domain_name"
                                    value="{{ $edit->domain_name ?? ''}}" placeholder="Enter desire domain name"
                                    class="form-control" {{@$edit->is_domain_pay == 1 ? 'readonly' : ''}} style="{{@$edit->is_domain_pay == 1 ? 'background-color: #e9ecef;' : ''}}">
                            </div>

                            {{-- for hsoting --}}
                            <div class="col-md-6" id="step-4">
                                <h6>Do you have hosting? <span class="text-danger">*</span></h6>
                                <div class="d-flex flex-lg-row flex-md-row flex-column gap-2">
                                    <div>
                                        <input class="form-check-input hosting_is" id="i_have_my_own_hosting" type="radio"
                                            value="i_have_my_own_hosting"
                                            aria-label="Radio button for following text input" name="hosting_is" {{
                                            @$edit->hosting_is == 'i_have_my_own_hosting' ? 'checked' : '' }}  {{ @$edit->is_hosting_pay == 1 ? 'disabled' : '' }} required>
                                        <label for="i_have_my_own_hosting">I have my own Hosting </label>
                                    </div>
                                </div>
                                <div class="d-flex flex-lg-row flex-md-row flex-column gap-2">
                                    <div>
                                        <input class="form-check-input hosting_is" id="request_to_purchase_hosting" type="radio"
                                            value="request_to_purchase_hosting"
                                            aria-label="Radio button for following text input" name="hosting_is" {{
                                            @$edit->hosting_is == 'request_to_purchase_hosting' ? 'checked' : '' }} {{ @$edit->is_hosting_pay == 1 ? 'disabled' : '' }} required>
                                        <label for="request_to_purchase_hosting">Request to Purchase Hosting</label>
                                    </div>
                                </div>
                                @if(@$edit->is_hosting_pay == 1)
                                <input type="hidden" value="{{$edit->hosting_is}}" name="hosting_is">
                                @endif
                                <label id="spend-error" class="error" for="hosting_is" style="display: none">This field
                                    is required.</label>
                            </div>
                            {{-- for hosting --}}


                            <div class="col-md-6" id="hosting_txt" style="{{isset($edit->hosting_is) && $edit->hosting_is == 'i_have_my_own_hosting' ? '' : 'display: none'}}">
                                <label for="hosting_name">Website Hosting <span class="text-muted">(optional)</span></label>
                                <textarea name="hosting_name" id="hosting_name" placeholder="Enter desired hosting URL, hosting email or username, and hosting password" class="form-control" rows="5">{{ $edit->hosting_details ?? '' }}</textarea>
                            </div>


                            <!-- <div class="col-md-6" >
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type[]" class="multiple-select" multiple="multiple" id="type" required>
                                    <option value="3in1_valuation" {{ isset($edit) && in_array('3in1_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>3 in 1 Valuation</option>
                                    <option value="hbd_valuation" {{ isset($edit) && in_array('hbd_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>HBD Valuation</option>
                                    <option value="condo_valuation" {{ isset($edit) && in_array('condo_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>Condo Valuation</option>
                                    <option value="landed_valuation" {{ isset($edit) && in_array('landed_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>Landed Valuation</option>
                                    <option value="rental_valuation" {{ isset($edit) && in_array('rental_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>Rental Valuation</option>
                                    <option value="post_launch_generic" {{ isset($edit) && in_array('post_launch_generic', explode(',', $edit->type)) ? 'selected' : '' }}>Post Launch Generic</option>
                                    <option value="executive_launch_generic" {{ isset($edit) && in_array('executive_launch_generic', explode(',', $edit->type)) ? 'selected' : '' }}>Executive Condo Generic</option>
                                </select>
                            </div> -->


                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mx-auto">
            <div class="card" id="section-2">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h4 class="mb-0" style=" font-weight: 800; ">Summary</h4> <br>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-2">
                                <label for="">
                                    <h5 style=" color: #39548a; font-size:16px; margin-top:16px;">Ad Name</h5>
                                </label>
                                <input type="text" class="form-control" id="title_text"
                                    style="height: 55px; border-left: 8px solid blue; border-radius: 10px;"
                                    placeholder="Ad Name" disabled>
                            </div>
                            <div class="col-md-6 mt-2">
                                <label for="">
                                    <h5 style=" color: #39548a; font-size:16px; margin-top:16px;" id="spend_type_title">
                                        Daily Budget</h5>
                                </label>
                                <input type="text" class="form-control" id="spend_amt"
                                    style="height: 55px; border-left: 8px solid blue; border-radius: 10px;"
                                    placeholder="100" disabled>
                                <p style=" font-size: 12px; {{ @$edit->spend_type == 'monthly' ? '' : 'display:none' }}"
                                    id="monthly_msg" data-toggle="tooltip" data-placement="bottom"
                                    title="You can expect Google to spend according to your daily budget or stay within your total budget limit. This helps ensure your ad campaigns run smoothly and within your allocated funds.">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>You can expect Google....
                                </p>
                            </div>
                            <div class="col-md-6 mt-2">
                                <label for="">
                                    <h5 style=" color: #39548a; font-size:16px; margin-top:16px;">Total Monthly Budget
                                    </h5>
                                </label>
                                <input type="text" class="form-control" id="total_budget"
                                    style="height: 55px; border-left: 8px solid blue; border-radius: 10px;"
                                    placeholder="3000" disabled>
                                <p style=" font-size: 12px; {{ @$edit->spend_type == 'daily' ? '' : 'display:none' }}"
                                    id="daily_msg" data-toggle="tooltip" data-placement="bottom"
                                    title="You can expect Google to spend according to your daily budget or stay within your total budget limit. This helps ensure your ad campaigns run smoothly and within your allocated funds.">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>You can expect Google....
                                </p>
                            </div>
                            <div class="col-md-12 mt-2">
                                <label for="">
                                    <h5 style=" color: #39548a; font-size:16px; margin-top:16px;">Domain Name</h5>
                                </label>
                                <input type="text" class="form-control" id="domain_name_text"
                                    style="height: 55px; border-left: 8px solid blue; border-radius: 10px;"
                                    placeholder="example.com (Puchased Already)" disabled>
                                <span id="domain_error" style="color:red"></span>
                            </div>
                            <div class="col-md-6 mt-1" id="domain_charges" style="display:none; ">
                                <h5 style=" color: #39548a; font-size:16px; margin-top:16px;">Domain
                                    Charges 20$</h5>
                            </div>

                            <div class="col-md-6 mt-1" id="hosting_charges" style="display:none;">
                                <h5 style=" color: #39548a; font-size:16px; margin-top:16px;">Hosting
                                    Charges 15$</h5>
                            </div>                    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    function getFormValues() {
        var title_text = $('#title').val();
        var spend_amt = $('#spend').val();
        var domain_name = $('#domain_name').val();
        var spend_type = $('input[name="spend_type"]:checked').val();
        var domain_is = $('input[name="domain_is"]:checked').val();
        var hosting_is = $('input[name="hosting_is"]:checked').val();
        var domain_is_text = '';
        var domain_charges;
        var hosting_charges;
        var total_budget = '';
        var daily_budget = '';

        if(domain_is == 'i_have_my_own_domain'){
            domain_is_text = '(I have my own domain)';

           $('#domain_charges').hide();

        }  if(domain_is == 'request_to_purchase') {
            $('#domain_charges').show();
        }

        if(domain_is == 'request_to_purchase'){
            domain_is_text = '(Request to Purchase)';
            $('#domain_charges').show();
        }
        if(hosting_is == 'request_to_purchase_hosting'){
            $('#hosting_charges').show();
            $('#hosting_name').val('');
        }
        if(spend_type == 'daily'){
            total_budget = (spend_amt*30);
            daily_budget = spend_amt;
        }if(spend_type == 'monthly'){
            total_budget = spend_amt;
            daily_budget = (spend_amt/30).toFixed();
        }
        $('#title_text').val(title_text);
        if (daily_budget) {
            $('#spend_amt').val(daily_budget);
        }
        $('#domain_name_text').val(domain_name+' '+domain_is_text);
        $('#total_budget').val(total_budget);
    }

    $('#title').keyup(function() {
        getFormValues();
    })
    $('.spend_type').click(function() {
        spend_text = $(this).val();
        amount = $('#spend').val();
        if(spend_text == 'daily'){
            $('#monthly_msg').hide();
            $('#daily_msg').show();
            if(amount < 100 && amount != ''){
                $('.campaing_summary').show();
                $('#is_popup').val(1);
            }else{
                $('.campaing_summary').hide();
                $('#is_popup').val(0);
            }
        }else{
            $('#daily_msg').hide();
            $('#monthly_msg').show();
            if((amount/30) < 100 && amount != ''){
                $('.campaing_summary').show();
                $('#is_popup').val(1);
            }else{
                $('.campaing_summary').hide();
                $('#is_popup').val(0);
            }
        }
        getFormValues();
    })
    $('#spend').keyup(function() {
        getFormValues();
    })
    $('#spend').keyup(function() { 
        amount = $(this).val();
        spend_type = $('input[name="spend_type"]:checked').val();
        if(amount != ''){
            if(spend_type == 'daily'){
                if(amount < 100){
                    $('.campaing_summary').show();
                    $('#is_popup').val(1);
                }else{
                    $('.campaing_summary').hide();
                    $('#is_popup').val(0);
                }
            }else{
                if((amount/30) < 100){
                    $('.campaing_summary').show();
                    $('#is_popup').val(1);
                }else{
                    $('.campaing_summary').hide();
                    $('#is_popup').val(0);
                }
            }
        }else{
            $('.campaing_summary').hide();
        }    
    })
    $('.domain_is').click(function() {
        domain_is = $('input[name="domain_is"]:checked').val();
        if(domain_is == 'request_to_purchase'){
            domain = $('#domain_name').val();
            if(domain != ''){
                check_domain(domain);
            }

        }else{
            $('.form-submit-btn').prop('disabled', false).removeClass('disabled');
            $("#domain_error").html('');
        }
        getFormValues();
    })


    $('.hosting_is').click(function() {
        hosting_is = $('input[name="hosting_is"]:checked').val();
        if(hosting_is === "request_to_purchase_hosting"){
            $('#hosting_charges').show();
            $('#hosting_txt').hide();
        }else{
            $('#hosting_charges').hide();
            $('#hosting_name').val('');
            $('#hosting_txt').show();
        }       
    })

    $('#domain_name').keyup(function() {
        $('.form-submit-btn').prop('disabled', false).removeClass('disabled');
        $("#domain_error").html('');
        $('#domain_charges').show();
        getFormValues();
    })
    $('#domain_name').on('blur', function() {
        domain_is = $('input[name="domain_is"]:checked').val();
        if(domain_is == 'request_to_purchase'){
            check_domain($('#domain_name').val())
        }
    });
</script>
<script>
    function check_domain(domain) {

            var domain = domain;
            if(domain) {
                $.ajax({
                    url: '{{route("user.ads.check_domain")}}', // The path to your PHP script
                    method: 'GET',
                    data: { domain_name: domain },
                    success: function(response) {
                        if(response){
                            $('.form-submit-btn').prop('disabled', true).addClass('disabled');
                            $("#domain_error").html('The domain is not available.');
                            $('#domain_charges').hide();
                        }

                        // console.log(response.message);
                        // Handle the response here
                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            }

    }
</script>
@endsection

@push('scripts')
<script>
    const tour = '{{ $tour ?? null }}'
        const tourId = '{{ $tour->id ?? null }}';
        const tourCode = '{{ $tour->code ?? null }}';
        const clientTour = '{{ $client_tour ?? null }}';
        const clientId = `{{ auth('web')->user()->id }}`;
        const intro = introJs();

        function goToNextStep() {
            if (intro) {
                intro.nextStep();
            }
        }

        function startTour() {
            const steps = [];
            const step1 = document.querySelector('#section-1');
            const step2 = document.querySelector('#section-2');

            let device = checkDevice();

            if (step1) {
                steps.push({
                    element: step1,
                    intro: `<strong>Enter Your Ad Details</strong><br><br>
                        Give your ad campaign a name, choose whether you'd like a daily or monthly budget, and indicate if you need a domain.`,
                    position: device == 'mobile' ? 'top' : 'right',
                });
            }

            if (step2) {
                steps.push({
                    element: step2,
                    intro: `<strong>Summary ads request</strong><br><br>
                        View an overview of your ad name, daily budget, total budget, and domain name.<br>
                        You’ll also see a breakdown of your daily spending, the expected monthly total, and any domain costs.`,
                    position: device == 'mobile' ? 'top' : 'left',
                });
            }

            intro.setOptions({
                steps,
                tooltipClass: 'customTooltipClass',
                disableInteraction: false,
                exitOnOverlayClick: false,
                exitOnEsc: false,
                showStepNumbers: false,
                hidePrev: true,
                hideNext: false,
                doneLabel: 'Next',
                overlayOpacity: 0.3,
                autoPosition: false,
                keyboardNavigation: false,
                showBullets: false,
            });

            intro.start();

            intro.onafterchange(function(targetElement) {
                if (intro._currentStep === 1) {
                    $('.introjs-tooltipbuttons').show();
                    $('.introjs-nextbutton').show();
                }
            });

            intro.oncomplete(function() {
                sendAjaxRequest('completed');
                setTimeout(function() {
                    window.location.href = `{{ route('user.wallet.add') }}`;
                }, 1000);
            });
        }

        function sendAjaxRequest(action) {
            $.ajax({
                url: `{{ route('tour.store') }}`,
                method: 'POST',
                data: {
                    client_id: clientId,
                    tour_id: tourId,
                    action: action,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('AJAX request successful:', response);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX request failed:', status, error);
                }
            });
        }

        function checkDevice() {
            if ($(window).width() < 768) {
                // Mobile
                console.log('Mobile device detected');
                return 'mobile';
            } else {
                // Desktop
                console.log('Desktop device detected');
                return 'desktop';
            }
        }

        $(window).resize(function() {
            checkDevice();
        });

        $(document).ready(function() {
            @if(isset($edit->id))
                getFormValues();
            @endif
            validations = $(".ajaxForm").validate();

            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                spend_type = $('input[name="spend_type"]:checked').val();
                var url = $(this).attr('action');
                is_popup = $('#is_popup').val();
                amount = $('#spend').val();
                if(spend_type == 'daily'){
                     if(amount < 100 && amount != ''){
                        $('.campaing_summary').show();
                        $('#is_popup').val(1);
                    }else{
                        $('.campaing_summary').hide();
                        $('#is_popup').val(0);
                    }
                }else{
                    if((amount/30) < 100 && amount != ''){
                        $('.campaing_summary').show();
                        $('#is_popup').val(1);
                    }else{
                        $('.campaing_summary').hide();
                        $('#is_popup').val(0);
                    }
                }

                validations = $(".ajaxForm").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                if(is_popup == 1){
                    Swal.fire({
                        title: "Do you still want to proceed?",
                        html: `<div style="text-align: left;"> <ol>
                                <li><strong>Ad Performance:</strong> A lower daily budget may result in limited ad exposure, affecting the overall performance and effectiveness of your campaigns.</li> <br>
                                <li><strong>Limited Impressions:</strong> With a budget of $100 or less, your ads are likely to receive fewer impressions, which can reduce the reach and impact of your marketing efforts.</li> <br>
                                <li><strong>Higher Cost Per Lead (CPL):</strong> Due to limited impressions and reduced ad performance, a lower daily budget can lead to a higher cost per lead (CPL), making your campaigns less cost-effective.</li>
                            </ol> </div>
                            <p style="text-align: left;">If you choose to proceed with a budget of $100 or less, please be aware of these potential limitations.</p>`,
                        type: "warning",
                        showCancelButton: !0,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes Proceed",
                        cancelButtonText: "No, Change Budget"
                    }).then(function (t) {
                        if (t.value){
                            my_ajax(url, formData, 'post', function(res) {
                                if (res.success) {
                                    goToNextStep();
                                }
                            }, true);
                        }
                    })
                }else{
                    my_ajax(url, formData, 'post', function(res) {
                        if (res.success) {
                            goToNextStep();
                        }
                    }, true);
                }
            })

            if (tour && !clientTour) {
                startTour();
                $('.introjs-skipbutton').hide();
                $('.introjs-backbutton').hide();

                // Step 0
                if (intro._currentStep == 0) {
                    $('.introjs-nextbutton').hide();
                }
            }
        });

        $(function () {
            $('[data-toggle="tooltip"]').tooltip()
        })
</script>
@endpush
