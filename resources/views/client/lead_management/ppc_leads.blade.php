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
    </style>
@endsection
@section('content')
    <form method="get" action="" class="row g-3 ajaxFormClient">
        <div class="row">
            <div class="form-group col-md-6 my-2">
                <label>All Ads <span class="text-danger">*</span></label>
                <input type="hidden" value="{{ request()->input('ads_id') }}" id="ads_id">
                <select name="ads_id" id="ads_id" class="form-control single-select" required>
                    <option value="">All Ads</option>
                    @foreach ($sub_accounts as $val)
                        <option value="{{ $val->hashid }}"
                            {{ request()->input('ads_id') == $val->hashid ? 'selected' : '' }}>
                            {{$val->adds_title}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card shadow radius-10 w-100">
                <div class="card-body">
                <div class="card-title d-flex align-items-center">
                        <h5 class="">Leads</h5>
                    </div>
                    <hr>
                    <div class="table-responsive mt-2">
                        <table class="table align-middle mb-0" id="ppc_leads-template-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Ads Name</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Qualifying Questions</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- view lead details  --}}
    <div class="modal fade" id="view_lead_details" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lead Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-1">
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Name</h5>
                            <p id="name">Name</p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Email</h5>
                            <p id="email"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Phone Number</h5>
                            <p id="mobile_number"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Status</h5>
                            <p id="admin_status"></p>
                        </div>
                    </div>
                    <h5>Qualifying Questions</h5>

                    <div class="row">
                        <div class="col-6 col-md-12 col-lg-12">
                            <div id="leadData_body"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
{{-- view lead details  --}}


{{-- view lead details info  --}}
    <div class="modal fade" id="view_lead_details_info" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agent Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-1">
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Salesperson Name</h5>
                            <p id="salesperson_name">Name</p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Registration No</h5>
                            <p id="registration_no"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Registration Start Date</h5>
                            <p id="registration_start_date"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Registration End Date</h5>
                            <p id="registration_end_date"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Estate Agent Name</h5>
                            <p id="estate_agent_name"></p>
                        </div>
                        <div class="col-6 col-md-6 col-lg-6">
                            <h5>Estate Agent License No</h5>
                            <p id="estate_agent_license_no"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
                </form>
            </div>
        </div>
    </div>
{{-- view lead details info  --}}
@endsection
@section('page-scripts')
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>
        var leadDataCount = 0;
        $(document).ready(function() {
            get_ppc_leads()
        });
        function get_ppc_leads() {
            var ppc = 'ppc';
            var ads_id = $('#ads_id').val();
            if ($.fn.DataTable.isDataTable('#ppc_leads-template-table')) {
                $('#ppc_leads-template-table').DataTable().destroy();
            }

            $('#ppc_leads-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],

                ajax: {
                    type: 'POST',
                    url: "{{ route('user.leads-management.get_leads_all') }}",
                    data: function(d) {
                        d.search = $('#ppc_leads-template-table').DataTable().search();
                        d.ppc = ppc;
                        d.ads_id = ads_id;
                        d._token = "{{ csrf_token() }}";
                    },
                },

                columns: [
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ads_name',
                        name: 'ads_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'mobile_number',
                        name: 'mobile_number',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'lead_data',
                        name: 'lead_data',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'admin_status',
                        name: 'admin_status',
                        orderable: true,
                        searchable: false
                    },

                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: true,
                        searchable: false
                    },

                ],
            });
        }

        $(document).on('click', ".view_lead_detail_id", function() {
            let data = $(this).data('data');
            // alert(data.name);
            $("#name").html(data.name);
            $("#email").html(data.email);
            $("#mobile_number").html(data.mobile_number);
            if (data.admin_status != null) {
                $("#admin_status").html(data.admin_status.toUpperCase());
            }
            $("#leadData_body").html('');
            leadDataCount = data.lead_data ? data.lead_data.length : 0;
            for (let index = 0; index < leadDataCount; index++) {
                let leadData = data.lead_data[index];
                let _html = `
                                <p>${leadData.key}: ${leadData.value}</p>`;
                $("#leadData_body").append(_html);
            }
            $('#view_lead_details').modal('show');
        });

        $(document).on('click', ".agent_specific_action", function() {

            let data = $(this).data('data');
            // alert(data.name);
            $("#salesperson_name").html(data.salesperson_name);
            $("#registration_no").html(data.registration_no);
            $("#registration_start_date").html(data.registration_start_date);
            $("#registration_end_date").html(data.registration_end_date);
            $("#estate_agent_name").html(data.estate_agent_name);
            $("#estate_agent_license_no").html(data.estate_agent_license_no);

            $('#view_lead_details_info').modal('show');
        });


        $(document).on('change', 'select[name="admin_status"]', function() {
            let id = $(this).data('id');
            let status = $(this).val();
            var data = {
                '_token': "{{ csrf_token() }}",
                lead_id: id,
                admin_status: status
            };

            getAjaxRequests(
                "{{ route('user.ads.lead_admin_status') }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        toast(res.success, "Success!", 'success', 3000);
                    }
                });
        });


        $(document).on('change', '#ads_id', function() {
            $('.ajaxFormClient').submit();
        });
    </script>
@endsection
