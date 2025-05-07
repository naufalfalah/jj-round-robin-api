@extends('layouts.admin')

@push('styles')
    <style>
        pre {
            white-space: pre-wrap;
            word-break: break-word;
            overflow: auto;
            margin: 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        code {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 2px 4px;
            border-radius: 4px;
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
                            <h5 class="mb-2">Google Ads Conversion Action</h5>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->

            <div class="tab-content mt-3" id="page-1">
                <div class="tab-pane fade show active" id="Edit-Profile">
                    <div class="card shadow-none border mb-0 radius-15">
                        <div class="card-header">
                            <div class="form-group mb-2">
                                <label for="inputName" class="form-label">Client</label>
                                <select class="form-control form-select" aria-label="Default select example" name="client_id" id="client_id" required readonly>
                                    {{-- <option value="" selected>Select a client</option> --}}
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->client_name }} - {{ $client->customer_id }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Conversion action</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Action optimisasion</th>
                                            <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">
                                                Conversion source</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="conversion-table-body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const clientId = `{{ $client->id ?? null }}`;

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }

        function getAdConversions() {
            $('#conversion-table-body').empty()

            if (clientId) {
                $.ajax({
                    url: "{{ route('google_ads.conversion_action') }}",
                    method: 'GET',
                    data: {
                        client_id: clientId,
                    },
                    success: function(data) {
                        if (data.results) {
                            for (const conversion of data.results) {
                                $('#conversion-table-body').append(`
                                    <tr>
                                        <td>${conversion.conversionAction.name}</td>
                                        <td>Primary</td>
                                        <td>Website</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                ${capitalize(conversion.conversionAction.status)}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary google-tag-btn" 
                                                    data-conversion-id="${conversion.conversionAction.id}">
                                                Google Tag
                                            </button>
                                        </td>
                                    </tr>
                                `);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }
        }

        $(document).ready(function() {
            getAdConversions();
            
            // Handle click event for Google Tag button
            $(document).on('click', '.google-tag-btn', function() {
                // Get the current row
                var currentRow = $(this).closest('tr');

                // Get data from the button
                var conversionId = $(this).data('conversion-id');

                // Check if the tag row already exists
                var existingTagRow = currentRow.next('tr.google-tag-row');

                if (existingTagRow.length) {
                    // Remove the existing tag row if it exists
                    existingTagRow.remove();
                } else {
                    // Create a new row to insert
                    var newRow = `
                        <tr class="google-tag-row">
                            <td colspan="5">
                                <div class="row px-4">
                                    <div class="col-xl-12">
                                        <pre>
                                            <code>
<!-- Google tag (gtag.js) -->
&lt;script async src="https://www.googletagmanager.com/gtag/js?id=AW-${conversionId}"&gt;&lt;/script&gt;
&lt;script&gt;
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'AW-${conversionId}');
&lt;/script&gt;
                                            </code>
                                        </pre>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    `;

                    // Insert the new row after the current row
                    currentRow.after(newRow);
                }
            });
        });
    </script>
@endpush
