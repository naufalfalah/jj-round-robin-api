
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png" />
  <!--plugins-->
  <link href="{{ asset('front') }}/assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
  <link href="{{ asset('front') }}/assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
  <link href="{{ asset('front') }}/assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
  <!-- Bootstrap CSS -->
  <link href="{{ asset('front') }}/assets/css/bootstrap.min.css" rel="stylesheet" />
  <link href="{{ asset('front') }}/assets/css/bootstrap-extended.css" rel="stylesheet" />
  <link href="{{ asset('front') }}/assets/css/style.css" rel="stylesheet" />
  <link href="{{ asset('front') }}/assets/css/icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <title>{{ @$title }} | {{ config('app.name') }}</title>
  <style>
    .footer {
        position: fixed;
        left: 260px;
        right: 0;
        bottom: 0;
        padding: 0.7rem;
        color: #484444;
        background-color: #f7f8fa;
        border-top: 1px solid #e2e3e4;
        text-align: center;
        transition: .3s all;
        z-index: 10 !important;
    }

    a.back-to-top {
        z-index: 20 !important;
    }
</style>
</head>

<body>
    <div class="card border shadow-none">
        <div class="card-body">
            <div class="d-flex justify-content-between g-3">
                <div>
                    <img src="{{ asset('front') }}/assets/images/google.jpg" width="200" alt="">
                    <h2 style="margin-left: 20px;" class="text-dark">Tax Invoice</h2>
                    <p style="margin-left: 22px; font-size: 15px;" class="text-dark">Invoice number: 00062{{ $report->id }}</p>
                </div>
                <div class="mt-5" style="font-size: 15px;">
                    <p class="text-dark text-end"> <span class="fw-bold">Google Asia Pacific Pte.Ltd</span> <br>
                    70 Pasir Panjang Road, #03-71 <br>
                    Mapletree Business City <br>
                    Singapore 117371 <br>
                    GST reg no.: 200817984R</p>
                </div>
            </div>

            <div class="row align-items-center g-3">
                <div class="col-12 col-lg-6">
                    <p style="margin-left: 20px;" class="text-dark"> <span class="fw-bold">Bill to</span> <br>
                        {{ $report->client->client_name }}<br>
                        {{ $report->client->user_agency->name }} <br>
                        {{ $report->client->user_industry->industries }} <br>
                        {{ $report->client->address }}</p>
                </div>
                <div class="col-12 col-lg-6 text-md-end" style="font-size: 15px;">

                </div>
            </div>


            <div class="row g-3 mt-3">
                <div class="col-md-6 col-lg-6">
                    <p style="margin-left: 20px;" class="text-dark"> <span class="fw-bold">Details</span> <br>
                        Invoice number .................00062{{ $report->id }}<br>
                        Invoice date .................{{ $report->invoice_date }} <br>
                        Billing ID .................{{ convertNumberFormat($report->billing_id) }} <br>
                        Payment ID .................{{ convertNumberFormat($report->client->customer_id) }} <br>
                    </p>
                </div>
                <div class="col-md-6 col-lg-6" style="font-size: 15px;">
                    <p class="text-dark"> <span class="fw-bold">Google Ads</span><hr></p>
                    <div class="row">
                        <div class="col-6 col-lg-6 d-flex justify-space-between">
                            <p class="text-dark">Total in SGD</p>
                        </div>
                        <div class="col-6 col-lg-6 text-md-end">
                            <h5 class="text-dark">SGD{{ number_format($report->total_amount, 2) }}</h5>
                        </div>
                    </div>
                    <p class="text-dark"> <span class="fw-bold">Summary for {{ get_fulltime($report->start_date, 'd M Y')}} - {{ get_fulltime($report->end_date, 'd M Y') }}</span><hr></p>

                    <div class="row">
                        <div class="col-6 col-lg-6 d-flex justify-space-between">
                            <p class="text-dark">Subtotal in SGD</p>
                        </div>
                        <div class="col-6 col-lg-6 text-md-end">
                            <p class="text-dark">SGD{{ number_format($report->total_amount  - $report->gst - ($report->card_charge ?? 0), 2) }}</p>
                        </div>
                    </div>

                    @if ($report->card_charge > 0)
                        <div class="row">
                            <div class="col-6 col-lg-6 d-flex justify-space-between">
                                <p class="text-dark">Card Fee</p>
                            </div>
                            <div class="col-6 col-lg-6 text-md-end">
                                <p class="text-dark">SGD{{ number_format($report->card_charge, 2) }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-6 col-lg-6 d-flex justify-space-between">
                            <p class="text-dark">GST (9%)</p>
                        </div>
                        <div class="col-6 col-lg-6 text-md-end">
                            <p class="text-dark">SGD{{ number_format($report->gst, 2) }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 col-lg-6 d-flex justify-space-between">
                            <p class="text-dark">Total in SGD</p>
                        </div>
                        <div class="col-6 col-lg-6 text-md-end">
                            <p class="text-dark">SGD{{ number_format($report->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row align-items-center g-3 mt-5">
                <div class="col-12 col-lg-6">
                    <p style="margin-left: 20px;" class="text-dark"> You Will be automatically charged for any amount due.</p>
                </div>
                <div class="col-12 col-lg-6 text-md-end" style="font-size: 15px;">

                </div>
            </div>
        </div>
    </div>
</body>
<script src="{{ asset('front') }}/assets/js/bootstrap.bundle.min.js"></script>
  <!--plugins-->
  <script src="{{ asset('front') }}/assets/js/jquery.min.js"></script>
  <script src="{{ asset('front') }}/assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="{{ asset('front') }}/assets/plugins/metismenu/js/metisMenu.min.js"></script>
  <script src="{{ asset('front') }}/assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="{{ asset('front') }}/assets/js/pace.min.js"></script>
  <!--app-->
  <script src="{{ asset('front') }}/assets/js/app.js"></script>


<script>
    // $(document).ready(function(){
    //     window.print();
    // });
</script>
</body>

</html>
