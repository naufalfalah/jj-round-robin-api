@extends('layouts.admin')

@section('page-css')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Export Leads In Excel</h5>
                        </div>
                        <hr/>
                        {{-- @dd($leads)  --}}
                        <form method="POST" action="{{ route('admin.export.excelexport') }}" class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-6">
                                <label for="">All Clients<span class="text-danger">*</span></label>
                                <select name="client" id="client" class="form-control single-select" required>
                                    <option value="">Select Client</option>
                                    @foreach ($leads as $lead)
                                    <option value="{{$lead->id}}">{{$lead->client_name}} - {{ $lead->sub_account->sub_account_name	 ?? 'No Sub Account' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="date_range">Date Range<span class="text-danger">*</span></label>
                                <input type="text" name="daterange" class="form-control" required/>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5">Export</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Import Leads</h5>
                        </div>
                        <hr/>
                        <form method="POST" action="{{ route('admin.export.import_leads') }}" enctype="multipart/form-data" class="row g-3 ajaxFormImport">
                            @csrf
                            <div class="col-md-6">
                                <label for="">All Clients<span class="text-danger">*</span></label>
                                <select name="client_import" id="client_import" class="form-control single-select" required>
                                    <option value="">Select Client</option>
                                    @foreach ($leads as $lead)
                                    <option value="{{$lead->id}}">{{$lead->client_name}} - {{ $lead->sub_account->sub_account_name }} </option>
                                    @endforeach

                                </select>
                                @error('client_import')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="date_range">Upload Excel Sheet<span class="text-danger">*</span></label>
                                <input type="file" name="import_leads" value="{{ old('import_leads') }}" class="form-control"  accept=".xlsx"/>
                                @error('import_leads')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Import</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>

    $(document).ready(function (){
        $('.single-select').select2({
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            placeholder: $(this).data('placeholder'),
            allowClear: Boolean($(this).data('allow-clear')),
        });

    
        $(function() {
            var start = moment().subtract(1, 'months');
            var end = moment();

            $('input[name="daterange"]').daterangepicker({
                startDate: start,
                endDate: end,
                opens: 'left'
            },
            function(start, end, label) {
                console.log("New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')");
            });
        });

        $('.ajaxFormImport').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', function(res) {

            }, true);
        })

    });

</script>

@endsection
