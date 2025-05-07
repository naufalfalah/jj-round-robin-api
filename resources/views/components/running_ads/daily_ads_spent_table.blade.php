<div class="row">
    <div class="col-xl-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="card-title d-flex align-items-center">
                    <h5 class="">Daily Ads Spent</h5>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12 col-lg-6 col-xl-4">
                        <div class="form-group my-2">
                            <label for="ads_filter">Filter by Ads Title:</label>
                            <select id="ads_filter" class="form-select">
                                <option value="">All</option>
                                @foreach ($ads as $ad)
                                    <option value="{{ $ad->id }}">{{ $ad->adds_title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="daily_ads_spent-template-table" class="table table-striped table-bordered"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Ads Request</th>
                                <th>Date</th>
                                <th>Total Amount</th>
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
        function getDailyAdsSpent() {
            if ($.fn.DataTable.isDataTable('#daily_ads_spent-template-table')) {
                $('#daily_ads_spent-template-table').DataTable().destroy();
            }
            $('#daily_ads_spent-template-table').DataTable({
                processing: true,
                serverSide: true,
                "order": [
                    [0, "desc"]
                ],
                "pageLength": 10,
                "lengthMenu": [10, 50, 100, 150, 500],
                ajax: {
                    url: "{{ route('admin.sub_account.advertisements.get_daily_ads_spent', ['sub_account_id' => '"+sub_account_id "']) }}",
                    data: function(d) {
                        d.ads_id = $('#ads_filter').val();
                        d.search = $('#daily_ads_spent-template-table').DataTable().search();
                    }
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
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'ad_name',
                        name: 'ad_name',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'date',
                        name: 'date',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: true,
                        searchable: true
                    },
                ],
            });
        }

        $('#ads_filter').on('change', function() {
            getDailyAdsSpent();
        });

        $(document).ready(function() {
            getDailyAdsSpent();
        });
    </script>
@endpush