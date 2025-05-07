@extends('layouts.admin')
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .dropdown-toggle::after {
        display: none !important;
    }
</style>
@section('page-css')
@section('content')
    <!-- <div class="row">
        <div class="col-12 col-lg-12">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="add_round_robin">Add Sub Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->


    <div class="row">
        @foreach ($sub_account as $sub)
        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 card-container" id="subAccountCard_{{ $sub->id }}">
                <div class="card radius-10 border-3 border-tiffany border-start border-0 shadow" id="subAccountCard_{{ $sub->id }}">
                    <div class="dropdown" style="position: absolute; top: 5px; right: 5px;">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 10px;">
                            <i class="fas fa-ellipsis-h" style="font-size: 30px; margin-top: -13px;"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item" href="#" data-url="{{ route('admin.update_sub_account_status',['id'=>$sub->id,'status'=>$sub->status === 'Active' ? 'Inactive' : 'Active']) }}" onclick="ajaxRequest(this)">
                                    {{ $sub->status === 'Active' ? 'Inactive' : 'Active' }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body d-flex flex-column justify-content-between" style="height: 130px; margin-bottom: 5px">
                        <div>
                            <a href="{{ route('admin.sub_account.advertisements.running_ads', ['sub_account_id' => $sub->hashid ]) }}">
                                <h5 class="mb-0 text-tiffany text-truncate">{!! preg_replace('/(\()/', ' <br> (', $sub->sub_account_name, 1); !!}</h5>
                            </a>
                            <p class="mb-1 text-muted" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{$sub->sub_account_url}}">{{ Str::limit($sub->sub_account_url, 25, "...") }}</p>
                        </div>
                        <div>
                            <p class="mb-0">
                                Status:
                                <span id="statusSpan" style="padding: 2px 6px; border-radius: 5px; {{ $sub->status === 'Active' ? 'background-color: #66bb6a; color: white;' : ($sub->status === 'Inactive' ? 'background-color: #ef5350; color: white;' : '') }}">
                                    {{ $sub->status }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
        </div>
        @endforeach
    </div>
    <!-- round robin modal -->
    <div class="modal fade" id="roundRobinModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Sub Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <form action="{{ route('admin.save_sub_account') }}" method="POST"
                        class="ajaxForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 col-lg-12 mb-3">
                                <label for="">Enter Name (optional)</label>
                                <input type="text" class="form-control" placeholder="Enter Sub Account Name" name="sub_account_name" id="sub_account_name">
                            </div>

                            <div class="col-md-12 col-lg-12 mb-3">
                                <label for="">Enter URL<span
                                    class="text-danger fw-bold">*</span></label>
                                <input type="url" class="form-control" placeholder="Enter Sub Account URL" name="sub_account_url" id="sub_account_url" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="float-end btn btn-primary form-submit-btn mb-2">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>

        $(document).ready(function () {

            $(document).on('click', '#add_round_robin', function(){
                $('#roundRobinModal').modal({backdrop: 'static', keyboard: false});
                $('#roundRobinModal').modal('show');
            });

            $('.ajaxForm').submit(function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var formData = new FormData(this);
                my_ajax(url, formData, 'post', function(res) {

                }, true);
            });

            $('#searchInput').keyup(function () {
                var query = $(this).val();

            });

            document.getElementById('searchInput').addEventListener('input', function() {
                var searchValue = this.value.trim().toLowerCase();
                var cardContainers = document.getElementsByClassName('card-container');

                for (var i = 0; i < cardContainers.length; i++) {
                    var cardContainer = cardContainers[i];
                    var card = cardContainer.getElementsByClassName('card')[0]; // Assuming only one card per container
                    var cardName = card.getElementsByClassName('text-tiffany')[0].innerText.toLowerCase();

                    if (cardName.includes(searchValue)) {
                        cardContainer.style.display = 'block';
                    } else {
                        cardContainer.style.display = 'none';
                    }
                }
            });
        });

    </script>
@endsection
