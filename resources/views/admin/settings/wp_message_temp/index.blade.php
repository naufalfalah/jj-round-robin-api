@extends('layouts.admin')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="border p-4 rounded">
                            <div class="card-title d-lg-flex justify-content-between align-items-center">
                                <h5 class="mb-2 mb-lg-0">Whatsapp Message Template</h5>
                                <a href="{{ route('admin.setting.assign_template_to_clients') }}"><button type="button" class="btn btn-primary">Assign Templete to Client</button></a>
                            </div>
                            <hr />
                            <form method="POST" action="{{ route('admin.setting.wp_message_store') }}"
                                class="row g-3 ajaxForm">
                                @csrf
                                <div class="col-md-6">
                                    <label for="">Status<span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="" disabled>Select one</option>
                                        <option value="Enable"
                                            {{ $whatsapp_temp?->status == 'Enable' ? 'selected' : '' }}>
                                            Enable</option>
                                        <option value="Disable"
                                            {{ $whatsapp_temp?->status == 'Disable' ? 'selected' : '' }}>
                                            Disable</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="">From Number<span class="text-danger">*</span></label>
                                    <input name="from_number" id="from_number" cols="30" rows="3"
                                        class="form-control" placeholder="Enter Number"
                                        value="{{ $whatsapp_temp->from_number ?? '' }}">
                                    <input type="hidden" name="id" value="{{ $whatsapp_temp->id ?? '' }}">
                                </div>
                                <div class="col-md-12">
                                    <label for="">WhatsApp Message<span class="text-danger">*</span></label>
                                    <textarea name="wp_message" id="wp_message" cols="30" rows="3" class="form-control"
                                        placeholder="Enter WhatsApp Message">{{ $whatsapp_temp->wp_message ?? '' }}</textarea>
                                </div>
                                <span>
                                    @clientName will be replaced with your client's display name, @email will be replaced
                                    with your client's email, and @phone will be replaced with your client's phone when
                                    sending (insert @clientName, @email, @phone).
                                </span>
                                <div class="form-group mb-3 text-right">
                                    <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            var validations = $(".ajaxForm").validate();
            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                if (validations.errorList.length != 0) {
                    return false;
                }
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', function(res) {
                    if (res.reload) {
                        location.reload();
                    }
                }, true);
            });
        });
    </script>
@endsection
