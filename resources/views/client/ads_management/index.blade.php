@extends('layouts.front')
@section('page-css')
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
    @media screen and (max-width: 767px) {
        button#\34 00_btn{
            margin-top: 9px !important;
        }
        div.dataTables_wrapper div.row div.col-sm-12.col-md-6:nth-child(2) {
            margin: 0px !important;
            padding: 0px !important;
            margin-top: 7px !important;
            padding-top: 10px !important;
        }
        div.dataTables_length select.form-select {
            width: 100% !important;
        }
    }
    #ads_add_all{
        padding-left: 0 !important;
        padding-right: 0 !important;
        padding-top: 0 !important;
    }
    #swal2-title{
        font-size: 19px;
    }
    </style>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/intro.js/introjs.css">
@endpush

@push('scripts-head')
    <script src="https://unpkg.com/intro.js/intro.js"></script>
@endpush

@section('content')
    <div class="container-fluid py-4" id="ads_add_all">
    <div class="row mt-5">
    <div class="col-12 col-xl-12">
        <div class="card shadow radius-10 w-100" id="status-card">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-6">
                        <h5 class="mb-0">Status definitions</h5>
                    </div>
                </div>
                <hr>
                <div class="table-responsive mt-2">
                    <table class="table align-middle mb-0" id="step-1">
                        <tbody>
                            <tr>
                                <td><span class="badge bg-secondary">Created and Approved</span> &nbsp;&nbsp;
                                Your request has been received and approved. We’ll begin processing it shortly.
                                </td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning">Pending Creation</span> &nbsp;&nbsp;
                                Your request is currently being created. Please wait while we finalize the details.
                                </td>
                            </tr>

                            <tr>
                                <td><span class="badge bg-success">Live</span>&nbsp;&nbsp;
                                Your ad is now live and active, running as scheduled.
                                </td>
                            </tr>

                            <tr>
                                <td><span class="badge bg-dark">Stopped</span>&nbsp;&nbsp;
                                The ad has been manually stopped and is no longer running.

                                </td>
                            </tr>

                            <tr>
                                <td><span class="badge bg-danger">Out of Funds</span> &nbsp;&nbsp;
                                The ad has been stopped because there are no remaining funds in your account.
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

        <div class="row">
            <div class="col-lg-12 mx-auto pb-2" id="btn-align">
                <a href="{{ route('user.ads.add') }}" class="btn btn-primary float-end new-message"  id="step-1">+ NEW ADS</a>
            </div>
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="">All Ads</h5>
                        </div>
                        <hr>
                        <div class="table-responsive" id="step-2">
                            <table id="ads-template-table" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                            S NO:</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Ads Request</th>
                                        <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Type</th> -->
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Amount Spend</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Date of Launch</th>    
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            No Leads</th>    
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="adsTestDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="formEdit">
                    @csrf
                    @method('put')
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Ads</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_lead">No Lead:</label>
                                <input type="number" class="form-control" name="no_lead" id="no_lead" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adsDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ads Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Title:</label>
                            <p id="title"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Spend Amount:</label>
                            <p id="amount"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type">Status:</label>
                            <p id="status"></p>

                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="">Discord Link:</label>
                            <p id="discordLink" style="word-wrap: break-word; white-space: pre-line;"></p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Domain Is :</label>
                            <p id="domain_is"></p>

                        </div>
                        <div class="col-md-6">
                            <label for="">Domain name :</label>
                            <p id="domain_name" style="word-wrap: break-word; white-space: pre-line;"></p>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="">Hosting Is:</label>
                            <p id="hosting_is"></p>

                        </div>
                        <div class="col-md-6">
                            <label for="">Hosting details :</label>
                            <p id="hosting_details" style="word-wrap: break-word; white-space: pre-line;"></p>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Website URL :</label>
                            <p id="web_url" style="word-wrap: break-word; white-space: pre-line;"></p>

                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>




@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            getAllAds();
            validations = $(".ajaxForm").validate();
            $('.ajaxForm').submit(function(e) {

                e.preventDefault();
                var url = $(this).attr('action');
                validations = $(".ajaxForm").validate();
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', function(res) {

                }, true);
            })
        });

        $(document).on('click', '.edit_no_lead', function() {
            let data = $(this).data('data');
            let no_lead = data.no_lead ?? 0;
            let actionUrl = "{{ route('user.ads.update', ['id' => ':id']) }}".replace(':id', data.id);

            $("#formEdit").attr('action', actionUrl);
            $('#no_lead').html(no_lead);

            $('#adsTestDetailModal').modal({ backdrop: 'static', keyboard: false});
            $('#adsTestDetailModal').modal('show');
        });

        $(document).on('click', '.view_detail', function() {
            let data = $(this).data('data');
            let title = data.adds_title;
            let email = data.email;
            let amount = data.spend_amount;
            let discord_link = data.discord_link;
            let type = data.type;
            let status = data.status;
            let description = data.description;
            let domain_name = data.domain_name;
            let web_url = data.website_url;
            let domain = data.domain_is;
            let hosting_is = data.hosting_is;
            let hosting_details = data.hosting_details;

            let formattedDomain = domain.replace(/_/g, " ");
            formattedDomain = formattedDomain.charAt(0).toUpperCase() + formattedDomain.slice(1);

            let formattedHosting = hosting_is.replace(/_/g, " ");
            formattedHosting = formattedHosting.charAt(0).toUpperCase() + formattedHosting.slice(1);
            if (data.spend_type === 'daily') {
                let day = 30;
                if (data.status === 'running') {
                    let days = Math.floor(data.spend_amount / data.daily_budget);
                    day = Math.min(days, 30);
                }

                if (data.spend_amount !== 0 && data.status === 'running') {
                    budget = data.daily_budget * day;
                } else {
                    budget = data.daily_budget * 30;
                }
            } else {
                budget = data.daily_budget;
            }

            $('#title').html(title);
            $('#amount').html('SGD '+budget);
            $('#discordLink').html(discord_link);
            // $("#type").html(ads_type_text(type));
            $("#status").html(ads_status_text(status));
            $("#domain_name").html(domain_name);
            $('#web_url').html(web_url);
            $('#domain_is').html(formattedDomain);
            $('#hosting_is').html(formattedHosting);
            $('#hosting_details').html(hosting_details);
            $('#adsDetailModal').modal({ backdrop: 'static', keyboard: false});
            $('#adsDetailModal').modal('show');
        });

        function getAllAds() {
            if ($.fn.DataTable.isDataTable('#ads-template-table')) {
                $('#ads-template-table').DataTable().destroy();
            }
            $('#ads-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('user.ads.all') }}",
                    data: function(d) {
                        d.search = $('input[type="search"]').val();
                    },
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'adds_request',
                        name: 'adds_request',
                        orderable: false,
                        searchable: false
                    },
                    // {
                    //     data: 'type',
                    //     name: 'type',
                    //     orderable: true,
                    //     searchable: false
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'spend_amount',
                        name: 'spend_amount',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'launch_date',
                        name: 'launch_date',
                        orderable: true,
                        searchable: false 
                    },
                    {
                        data: 'no_lead',
                        name: 'no_lead',
                        orderable: true,
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: false
                    },
                ],
            });
        }

        function ads_status_text(status){
            let text = '';
            if(status == "pending"){
                text = 'Pending';
            }else if(status == "running"){
                text = 'Running';
            }
            else if(status == "complete"){
                text = 'Complete';
            }else{
                text = 'Rejected';
            }
            return text;
        }

        function ads_type_text(type){
            let texts = [];

            type.split(",").forEach(function(item) {
                switch (item.trim()) {
                    case "3in1_valuation":
                        texts.push('3 in 1 Valuation');
                        break;
                    case "hbd_valuation":
                        texts.push('HBD Valuation');
                        break;
                    case "condo_valuation":
                        texts.push('Condo Valuation');
                        break;
                    case "landed_valuation":
                        texts.push('Landed Valuation');
                        break;
                    case "rental_valuation":
                        texts.push('Rental Valuation');
                        break;
                    case "post_launch_generic":
                        texts.push('Post Launch Generic');
                        break;
                    case "executive_launch_generic":
                        texts.push('Executive Launch Generic');
                        break;
                }
            });

            return texts;
        }
    </script>
@endsection

@push('scripts')
    <script>
        const tour = '{{ $tour ?? null }}';
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

        $(document).ready(function() {
            if (tour && !clientTour) {
                const steps = [];
                let step1 = document.querySelector('#status-card');

                if (tourCode == 'FINISH_2') {
                    step1 = document.querySelector('#status-card');
                }
            
                if (step1) {
                    steps.push({ 
                        element: step1,
                        intro: `<strong>Ads Request Status</strong><br><br>
                            Here, you can see the status of your ads. Use the legend above to understand the different status indicators and stay updated on your ad requests.`,
                        position: 'bottom',
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
                    overlayOpacity: 0.3,
                    autoPosition: false,
                    keyboardNavigation: false,
                    showBullets: false,
                });

                intro.start();

                intro.oncomplete(function() {
                    sendAjaxRequest('completed');
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
                if (tour && !clientTour) {
                    startTour();
                }
            });
        });
    </script>
@endpush