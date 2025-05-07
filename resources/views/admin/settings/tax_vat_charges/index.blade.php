@extends('layouts.admin')
@section('page-css')
<style>
    .error{
        color: red;
    }
</style>
@endsection
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <!-- tax code -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Set Tax & Vat Charges</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('admin.setting.tax_store') }}" class="row g-3 ajaxForm">
                            @csrf
                            <div class="col-md-12">
                                <label for="">Tax Charges<span class="text-danger">*</span></label>
                                <input type="text" name="charges" id="charges" value="{{ $upd_chages->charges ?? '' }}"
                                    placeholder="Enter Tax Charges" class="form-control" required>
                                    <input type="hidden" name="charges_upd" value="{{ $upd_chages->id ?? '' }}">
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                
            </div>
            <!-- tax code -->
            <!-- topup setting code -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="border p-4 rounded">
                        <div class="card-title d-flex align-items-center">
                            <h5 class="mb-0">Topup Setting</h5>
                        </div>
                        <hr />
                        <form method="POST" action="{{ route('admin.setting.topup_store') }}" class="row g-3 ajaxFormTopup">
                            @csrf
                        
                            <div class="col-md-6"> 
                                <label for="min_topup">Minimum Topup Value<span class="text-danger">*</span></label>
                                <input type="number" name="min_topup" id="min_topup" value="{{ $topup->min_topup ?? '' }}"
                                       placeholder="Enter Minimum Topup Value" class="form-control" min="1" required>
                                       <input type="hidden" name="topup_upd" value="{{ $topup->id ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label for="default_topup">By Default Topup Value<span class="text-danger">*</span></label>
                                <input type="number" name="default_topup" id="default_topup" value="{{ $topup->default_topup ?? '' }}"
                                       placeholder="Enter Default Topup Value" class="form-control" min="1" required>
                            </div>
                        
                            <div class="col-md-12">
                                <label for="toggle">Can Client Enter</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="toggle" value="0">
                                    <input class="form-check-input" type="checkbox" name="toggle" id="toggle" value="1" {{ isset($topup->is_fillable) && $topup->is_fillable ? 'checked' : '' }}>
                                    <label class="form-check-label" for="toggle"></label>
                                </div>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit" class="btn btn-primary px-5 form-submit-btn-topup">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
             <!-- topup setting code -->
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

    $(document).ready(function() {
        validations = $(".ajaxFormTopup").validate();
        $('.ajaxFormTopup').submit(function(e) {
            e.preventDefault();

            var btn = $('.form-submit-btn-topup');
            btn.prop('disabled',true);
            btn.html('<span class="d-flex align-items-center"><div class="spinner-border text-dark" role="status"><span class="sr-only">Loading...</span> </div> Saving...</span>');
            
            var url = $(this).attr('action');
            validations = $(".ajaxFormTopup").validate();
            if (validations.errorList.length != 0) {
                return false;
            }
            var formData = new FormData(this);
            my_ajax(url, formData, 'post', '', false, function(res) {

            }, true);
        })
    });
</script>
@endsection