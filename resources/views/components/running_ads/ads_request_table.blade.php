<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Ad Requests</h5>
                </div>
                <hr>
                <div class="table-responsive">
                    <table id="ads-template-table" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Ads Title</th>
                                <th>Spend Type</th>
                                <th>Spend Amount</th>
                                <th>E-Wallet</th>
                                <th>Status</th>
                                <th>Google Account</th>
                                <th>Customer ID</th>
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

@push('scripts')
    <script>
        function getAllAds() {
            var client = $('#client').val();
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
                    url: "{{ route('admin.sub_account.advertisements.get_ads', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.search = d.search = $('#ads-template-table').DataTable().search();
                        d.client = client
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
                        data: 'spend_amount',
                        name: 'spend_amount',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'e_wallet',
                        name: 'e_wallet',
                    },
                    // {
                    //     data: 'domain',
                    //     name: 'domain',
                    //     orderable: true,
                    //     searchable: false,

                    // },

                    // {
                    //    data: 'domain',
                    //    name: 'domain',
                    //    orderable: true,
                    //    searchable: false,
                    //    render: function (data, type, row)
                    //    {
                    //     return data === 1 ? 'Requested' : 'Own Domain';
                    //    }
                    // },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: 'google_account',
                        name: 'google_account',
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

        $(document).ready(function() {
            getAllAds();
        });
    </script>
@endpush