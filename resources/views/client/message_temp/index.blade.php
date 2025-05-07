@extends('layouts.front')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Whatsapp Message Template</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('user.message_template.wp_message_store') }}" class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-12">
                                <label for="">Message Text <span class="text-danger">*</span></label>
                                <textarea name="wp_message" id="wp_message" cols="30" rows="3" class="form-control" placeholder="Enter WhatsApp Message">{{ $whatsapp_temp->message_template ?? '' }}</textarea>
                                <input type="hidden" name="id" value="{{ $whatsapp_temp->id ?? '' }}">
                            </div>
                            <span>
                                @clientName will be replaced with your client's display name, @email will be replaced with your client's email, and @phone will be replaced with your client's phone when sending (insert @clientName, @email, @phone).
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
</script>
@endsection
