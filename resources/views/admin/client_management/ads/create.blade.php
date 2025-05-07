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
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">{{ isset($edit) ? $title : 'Add Ads' }}</h5>
                        </div>
                        <hr />
                        <form method="POST"
                            action="{{ route('admin.sub_account.client-management.ads_save', ['sub_account_id' => $sub_account_id]) }}"
                            class="row g-3 ajaxForm">
                            @csrf

                            <div class="col-md-4">
                                <label for="">Client<span class="text-danger">*</span></label>
                                <select name="client_id" id="client_id" class="form-control" required>
                                    <option value="">Select A Client</option>
                                    @foreach ($all_users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($edit) && $edit->client_id == $user->id) selected @endif>
                                            {{ $user->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="">Title (optional)</label>
                                <input type="text" name="title" id="title" value="{{ $edit->adds_title ?? '' }}"
                                    placeholder="Enter Ads Title" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label for="type">Type<span class="text-danger">*</span></label>
                                <select name="type[]" class="multiple-select form-control" multiple="multiple"
                                    id="type" required>
                                    <option value="3in1_valuation"
                                        {{ isset($edit) && in_array('3in1_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        3 in 1 Valuation</option>
                                    <option value="hbd_valuation"
                                        {{ isset($edit) && in_array('hbd_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        HBD Valuation</option>
                                    <option value="condo_valuation"
                                        {{ isset($edit) && in_array('condo_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        Condo Valuation</option>
                                    <option value="landed_valuation"
                                        {{ isset($edit) && in_array('landed_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        Landed Valuation</option>
                                    <option value="rental_valuation"
                                        {{ isset($edit) && in_array('rental_valuation', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        Rental Valuation</option>
                                    <option value="post_launch_generic"
                                        {{ isset($edit) && in_array('post_launch_generic', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        Post Launch Generic</option>
                                    <option value="executive_launch_generic"
                                        {{ isset($edit) && in_array('executive_launch_generic', explode(',', $edit->type)) ? 'selected' : '' }}>
                                        Executive Condo Generic</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="status">Status<span class="text-danger">*</span></label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ isset($edit) && $edit->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="running" {{ isset($edit) && $edit->status == 'running' ? 'selected' : '' }}>Running</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label for="">Discord Link<span class="text-danger">*</span></label>
                                <input type="url" name="descord_link" id="descord_link"
                                    value="{{ $edit->discord_link ?? '' }}" placeholder="Enter Descord Link"
                                    class="form-control" required>
                            </div>

                            <div class="form-group mb-3 text-right">
                                <input type="hidden" name="id" value="{{ $edit->hashid ?? null }}">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/select2/js/select2.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/form-select2.js"></script>

    <script src="{{ asset('front') }}/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('front') }}/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('front') }}/assets/js/table-datatable.js"></script>
    <script>
        $(document).ready(function() {
            $('.table').DataTable();
            // getAllAds();
            // validations = $(".ajaxForm").validate();

            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                // validations = $(".ajaxForm").validate();
                // if (validations.errorList.length != 0) {
                //     return false;
                // }
                var url = $(this).attr('action');
                var formData = new FormData(this);
                
                my_ajax(url, formData, 'post', function(res) {

                }, true);
            })
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
