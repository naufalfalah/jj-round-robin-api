@extends('layouts.admin')

@push('styles')
    <style>
        .error {
            color: red;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">All Ads</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="ads-template-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th
                                        class="text-uppercase text-center ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        S NO:</th>
                                    <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Client Name</th>
                                        <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                            Ads Title</th>
                                    <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Type</th>
                                    <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


{{-- modal  --}}

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
                            <label for="">Title</label>
                            <input type="text" readonly id="titleer" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type">status</label>
                            <input type="text" readonly id="statuser" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Discord Link</label>
                            <input type="url" readonly id="discordLink" class="form-control" required>
                        </div>
            
                        <div class="col-md-6 mb-3">
                            <label for="type">Type</label>
                            <input type="text" readonly id="typeer" class="form-control" required>
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

@push('scripts')
    <script src="{{ asset('front') }}assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/select2/js/select2.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/select2/js/select2.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/form-select2.js"></script>

    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>

        $(document).ready(function() {
            $('.table').DataTable();
        });

        $(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
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
                    url: "{{ route('admin.sub_account.client-management.all_ads',['sub_account_id' => $sub_account_id]) }}",
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
                        data: 'client_name',
                        name: 'client_name',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'adds_title',  
                        name: 'adds_title',  
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                    // {
                    //     data: 'spend_amount',
                    //     name: 'spend_amount',
                    //     orderable: true,
                    //     searchable: false
                    // },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: true,
                        searchable: false
                    },
                ],
            });
        }

        // $(document).on('click', '.view_detail', function() {
        //     // alert("hello"); 
        //     // $('#adsDetailModal').modal('show');  
        //     let data = $(this).data('data');
        //     // alert(data);   
        //     // console.log(data);   
        //     // let title = data.adds_title;
        //     // // let email = data.email;
        //     // // let amount = data.spend_amount;
        //     // // let discord_link = data.discord_link;
        //     // let type = data.type;
        //     // let status = data.status;
        //     // let description = data.description;

        //     $('#title').html(data.adds_title);
        //     // $('#amount').val(amount);
        //     // $('#discordLink').val(discord_link);
        //     $("#type").html(ads_type_text(data.type));
        //     $("#status").html(ads_status_text(data.status));
        //     $('#adsDetailModal').modal('show');
           
        // });

        $(document).on('click', '.view_detail', function() {
            event.preventDefault();
            let data = $(this).data('data');

            console.log(data);  

            let title = data.adds_title;
            // // let email = data.email;
            // // let amount = data.spend_amount; 
            let discord_link = data.discord_link; 
            let type = data.type;
            let status = data.status;
            // let description = data.description; 

            $('#titleer').val(title);
            // $('#amount').val(amount);
            $('#discordLink').val(discord_link);
            $("#typeer").val(ads_type_text(type));
            $("#statuser").val(ads_status_text(status));
            $('#adsDetailModal').modal({
                backdrop: 'static',  
                keyboard: false
            });
            $('#adsDetailModal').modal('show');
        });



        function ads_status_text(status) {
            let text = '';
            if (status == "pending") {
                text = 'Pending';
            } else if (status == "running") {
                text = 'Running';
            } else if (status == "complete") {
                text = 'Complete';
            } else {
                text = 'Rejected';
            }
            return text;
        }



        function ads_type_text(type) {
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
@endpush
