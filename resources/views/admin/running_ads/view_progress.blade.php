@extends('layouts.admin')

@section('page-css')
    <link href="{{ asset('front/assets/plugins/fileUpload/fileUpload.css') }}" rel="stylesheet" />
    <link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css" />
@endsection
@section('content')

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-3 row-cols-xxl-3">
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Ads Budget</p>
                            <h4 class="mb-0 text-primary">{{get_price($ads_budget->spend_amount)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">Remaining Balance</p>
                            <h4 class="mb-0 text-primary">{{get_price($remaining_amount)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
         <div class="col">
            <div class="card radius-10 border-0 border-start border-pink border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">EST Monthly Payment</p>
                            <h3 class="mb-0 text-pink">{{ get_price(0)}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-pink text-white">
                            <<i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-pink border-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1">PPC Leads Generated</p>
                            <h3 class="mb-0 text-pink">{{$total_ppc_leads}}</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-pink text-white">
                            <i class="fa-solid fa-people-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">Leads</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">

                      

                        <table id="ppc_leads-template-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>phone Number</th>
                                    <th>Qualifying Questions</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
        $(document).ready(function() {
            get_ppc_leads();
        });

        function get_ppc_leads() {
            var ppc = 'ppc';
            var ads_id = "{{$ads_id}}";
            var client_id = "{{$client_id}}";
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
                    url: "{{ route('admin.sub_account.advertisements.get_leads_data', ['sub_account_id' => session()->get('sub_account_id')]) }}",
                    data: function(d) {
                        d.search = $('#ppc_leads-template-table').DataTable().search();
                        d.ppc = ppc;
                        d.ads_id = ads_id;
                        d.client_id = client_id;
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
                "{{ route('admin.sub_account.advertisements.lead_admin_status', ['sub_account_id' => session()->get('sub_account_id')]) }}",
                data, 'POST', true,
                function(res) {
                    if (res.success) {
                        toast(res.success, "Success!", 'success', 3000);
                    }
                });
        });
    </script>
@endsection
