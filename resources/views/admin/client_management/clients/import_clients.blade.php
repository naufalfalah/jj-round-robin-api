@extends('layouts.admin')

@section('page-css')
<style>
     .loader-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999;
        opacity: 0.7;
        transition: opacity 0.3s ease-in-out;
    }

    .loader {
        border: 8px solid #f1f1f1;
        border-top: 10px solid #39548A;
        border-radius: 50%;
        width: 80px;
        height: 80px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection

@section('content')

<div class="loader-container" id="loader-container" style="display: none;">
    <div class="loader"></div>
</div>

<form method="post" action="" class="row g-3">
    <div class="row">
        <div class="form-group col-md-6 my-2">
            <h6 class="mb-2 text-uppercase">Select User For Import</h6>
            <select name="user_id" id="user_id" class="form-control mb-2 single-select dropup" required>
                <option value="" disabled selected>Select User For Import</option>
                @foreach ($users as $val)
                    <option value="{{ $val->hashid }}" {{ $val->id == @$clone_client->id ? 'selected' : '' }}>
                        {{ $val->client_name }} - {{ $val->email }}
                    </option>
                @endforeach
            </select>            
            
        </div>
    </div>
</form>
<div class="user-profile-page">
    <div class="card radius-15">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 col-lg-6 border-right">
                    <div class="d-md-flex align-items-center">

                        <div class="mb-md-0 mb-3">
                            @if (isset($clone_client->image))
                                <img src="{{ check_file($clone_client->image,) }}"
                                    class="rounded-circle shadow" width="130" height="130" alt="" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->

            <h5 class="mb-2 mt-2">Add Client</h5>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="Edit-Profile">
                        <div class="card shadow-none border mb-0 radius-15">
                            <div class="card-body">
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12 col-lg-12 border-right">
                                            <form method="POST" action="{{ route('admin.sub_account.client-management.save', ['sub_account_id' => $sub_account_id ]) }}"
                                                class="row g-3 ajaxForm" enctype="multipart/form-data" id="first-form">
                                                @csrf
                                                <input type="hidden" value="1" name="sub_account_id">

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Client Name <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="client_name" id="client_name"
                                                        value="{{ $clone_client->client_name ?? '' }}" placeholder="Enter Name"
                                                        class="form-control" required
                                                        oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '');">
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Mobile <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="phone_number" id="name"
                                                        value="{{ $clone_client->phone_number ?? '' }}" placeholder="Enter Number"
                                                        class="form-control" required
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                                        max="12">
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Agency <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select class="form-control" name="agency_id" id="agency_address" required>
                                                        <option value="" disabled selected>Select Agency</option>
                                                        @foreach ($agencies as $agency)
                                                            <option value="{{ $agency->id }}"
                                                                @if (isset($clone_client) && $clone_client->agency_id == $agency->id) selected @endif>
                                                                {{ $agency->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Package <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <select name="package" class="form-control">
                                                        <option value="Package 1"
                                                            @if (isset($clone_client) && $clone_client->package == 1) selected @endif>
                                                            Package 1
                                                        </option>
                                                        <option value="Package 2"
                                                            @if (isset($clone_client) && $clone_client->package == 2) selected @endif>
                                                            Package 2
                                                        </option>
                                                        <option value="Package 3"
                                                            @if (isset($clone_client) && $clone_client->package == 3) selected @endif>
                                                            Package 3
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Email <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="email" name="email" id="email"
                                                        value="{{ $clone_client->email ?? '' }}" placeholder="Enter email"
                                                        class="form-control" required>
                                                </div>


                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Address <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="text" name="address" id="address"
                                                        value="{{ $clone_client->address ?? '' }}" placeholder="Enter address"
                                                        class="form-control" required>
                                                </div>

                                                <div class="col-12 col-lg-6">
                                                    <label for="inputName" class="form-label">Industry <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <div class="ms-auto position-relative">

                                                        <select name="industry_id" id="industry" class="form-control"
                                                            required>
                                                            <option value="">Select Industry</option>
                                                            @foreach ($industries as $industry)
                                                                <option value="{{ $industry->id }}"
                                                                    @if (isset($clone_client) && $clone_client->industry_id == $industry->id) selected @endif>
                                                                    {{ $industry->industries }}
                                                                </option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-6">
                                                    <label for="inputName" class="form-label">Professional Image <span
                                                            class="text-danger fw-bold">* </span><span class="text-danger">(Image Type jpg,jpeg,png)</span></label>
                                                    <input type="file" id="profile_image" name="image" accept=".jpg, .png, .jpeg"
                                                        class="form-control" @if (empty($clone_client->image)) required @endif>
                                                    
                                                </div>
                                                <input type="hidden" value="{{ $clone_client->image ?? ""}}" name="client_image">
                                                <div class="col-md-6">
                                                    <label for="inputName" class="form-label">Password <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="password" value="" name="password"
                                                        class="form-control" placeholder="Enter Password" required>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Confirm Password </label> <span
                                                            class="text-danger fw-bold">*</span></label>
                                                    <input type="password" value="" name="confirm_password"
                                                        class="form-control" placeholder="Enter Confirm Password" required>
                                                </div>

                                                <div class="form-group mb-3 text-right">
                                                    <button type="submit" class="btn btn-primary px-5 form-submit-btn">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

@if(isset($clone_client->email))
<script>
    var id = {{ $last_inserted_id }};
    var email = $("#email").val();
    var atIndex = email.indexOf('@');
    var newId = id + 1;
    var modifiedEmail = email.substring(0, atIndex) + newId + email.substring(atIndex);
    $("#email").val(modifiedEmail);
</script>
@endif

<script>

    $(document).ready(function() {
        $('.ajaxForm').submit(function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var param = new FormData(this);
            var files = $('#profile_image')[0].files[0] ?? '';
            param.append('profile_image', files);
            my_ajax(url, param, 'post', function(res) {

            }, true);
        }); 
    });


    $(document).on('change', '#agency_address', function () {

        var url = "{{ route('admin.sub_account.client-management.get_agency_address', ['sub_account_id' => $sub_account_id ]) }}"
        var agency_id = $(this).val();

        $.ajax({
            url: url,
            method: "post",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                agency_id : agency_id,
            },
            dataType: "json",
            success: function (response) {
                if (response['success'] !== undefined && response['address'] !== undefined) {
                    $('#address').val("");
                    $('#address').val(response.address);
                }
                
            },
            error: function(xhr, status, error) {
                $('#address').val("");
                console.error(xhr.responseText);
            }
        });
    });

    $(document).on('change', '#user_id', function () {
        var user_id = $(this).val();
        
        var url = "{{ route('admin.sub_account.client-management.all_clients', ['sub_account_id' => $sub_account_id ]) }}";
        $('#loader-container').show();
        $.ajax({
            url: url,
            method: "get",
            data: {
                user_id : user_id,
            },
            dataType: "json",
            success: function(response) {
                $('#loader-container').hide();
                if(response.redirect_url) {
                    window.location.href = response.redirect_url;
                } else {
                    $('#loader-container').hide();
                }
            },
            error: function(xhr, status, error) {
                $('#loader-container').hide();
            }
        });
    });

</script>

@endsection