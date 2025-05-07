@extends('layouts.front')
@section('page-css')
<link href="{{ asset('front') }}/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-center">
                        <h5 class="">Report</h5>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table id="report-table" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th
                                        class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                        Invoice ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Invoice Date</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Total Amount</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Customer ID</th>
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
@endsection
@section('page-scripts')
<script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        getReport();
    });

    function getReport() {
        if ($.fn.DataTable.isDataTable('#report-table')) {
            $('#report-table').DataTable().destroy();
        }
        $('#report-table').DataTable({
            processing: true,
            serverSide: true,
            "order": [
                [0, "desc"]
            ],
            "pageLength": 10,
            "lengthMenu": [10, 50, 100, 150, 500],
            ajax: {
                url: "{{ route('user.report.view') }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val();
                },
            },
            columns: [
                {
                    data: 'invoice_id',
                    name: 'invoice_id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'invoice_date',
                    name: 'invoice_date',
                    orderable: false,
                    searchable: true
                },
                {
                    data: 'total_amount',
                    name: 'total_amount',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'billing_id',
                    name: 'billing_id',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'customer_id',
                    name: 'customer_id',
                    orderable: true,
                    searchable: false
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

</script>
@endsection
