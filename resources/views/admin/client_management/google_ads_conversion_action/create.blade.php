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
            <div class="row mb-2">
                <div class="col-md-6 border-right">
                    <div class="d-md-flex align-items-center">
                        <div class="ms-md-6 flex-grow-1">
                            <h5 class="mb-2">Create Google Ads Conversion Action</h5>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
                <div class="card shadow-none border mb-0 radius-15 mb-3">
                    <div class="card-header">
                        Google Tag
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-xl-12">
                                <pre>
                                    <code id="code-block">
&lt;!-- Google tag (gtag.js) --&gt;
&lt;script async src="https://www.googletagmanager.com/gtag/js?id=AW-{{ session('conversionActionId') }}"&gt;&lt;/script&gt;
&lt;script&gt;
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'AW-{{ session('conversionActionId') }}');
&lt;/script&gt;
                                    </code>
                                </pre>
                                <button type="button" class="btn btn-secondary copy-button mt-3" id="copy-button">Copy Code</button>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('admin.sub_account.client-management.google_ads_conversion_action', ['sub_account_id' => $sub_account_id ]) }}" method="post" class="">
                @csrf
                <div class="card shadow-none border mb-0 radius-15 mb-3">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-xl-6">
                                <div class="form-group mb-2">
                                    <label for="client_id" class="form-label fw-bolder mb-2">Client
                                        <span class="text-danger fw-bold">*</span></label>
                                    <select class="form-select" aria-label="Default select example" name="client_id" required readonly>
                                        {{-- <option value="" selected>Select a client</option> --}}
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" @selected($loop->first)>{{ $client->client_name }} - {{ $client->customer_id }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Create conversion actions from Web and App events</h6>
                <div class="row px-4">
                    <div class="col-xl-3">
                        <img src="{{ asset('front/assets/images/Attribution - Success spot.svg') }}" class="w-100" alt="success">
                    </div>
                    <div class="col-xl-6 d-flex align-self-center">
                        <p>You can create conversion action from website events and Google Analytics App events, such as checkouts or sign-ups, without having to make changes to your website's code.</p>
                    </div>
                </div>

                <div class="conversion-item-container">
                    <div class="card shadow-none border mb-0 radius-15 mb-3 conversion-item">
                        <div class="card-body">
                            <div class="card shadow-none border mb-0 radius-15 mb-3">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            {{-- FEEDBACK: Only show submit lead form option --}}
                                            <div class="form-group mb-2">
                                                <label for="category" class="form-label fw-bolder">Goal and action optimisation</label>
                                                <div class="mb-2">
                                                    <small>Select the Goal category for this action</small>
                                                </div>
                                                <select class="form-select" aria-label="Default select example" name="category[]" required>
                                                    <optgroup label="Leads categories">
                                                        <option value="SUBMIT_LEAD_FORM" selected>Submit lead form</option>
                                                    </optgroup>
                                                </select>
                                                @error('category')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="card shadow-none border mb-0 radius-15 mb-3">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            <div class="form-group mb-2">
                                                <label for="name" class="form-label fw-bolder">Conversion Name
                                                    <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" name="name[]" id="name" value="" placeholder=""
                                                    class="form-control name-input" required
                                                    oninput="updateCharCount(this)">
                                                <div class="d-flex justify-content-between p-1">
                                                    <small class="form-text text-end text-muted"></small>
                                                    <small id="name_char_count" class="form-text text-end text-muted name-char-count">
                                                        0/100
                                                    </small>
                                                </div>
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="card shadow-none border mb-0 radius-15 mb-3">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            <div class="form-group mb-2">
                                                <label for="type" class="form-label fw-bolder">Type</label>
                                                <select class="form-select" aria-label="Default select example" name="type[]" required>
                                                    <option value="WEBPAGE">Web</option>
                                                </select>
                                                @error('type')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
    
                                            <label class="form-label fw-bolder mt-2">Event</label>
                                            <div class="mb-2">
                                                <small>Choose how to measure conversions on your web page.</small>
                                            </div>
                                            <div class="form-group mb-2 mt-2">
                                                <label for="website_url" class="form-label">Website URL
                                                    <span class="text-danger fw-bold">*</span></label>
                                                <input type="text" name="website_url[]" id="website_url" placeholder=""
                                                    class="form-control website-url-input" value="" required>
                                                    <div id="error-message" class="invalid-feedback" style="display:none;"></div>
                                                @error('website_url')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            {{-- <div class="form-group mb-2">
                                <label for="inputName" class="form-label">Event Type
                                    <span class="text-danger fw-bold">*</span></label>
                                <select class="form-select" aria-label="Default select example" name="objective" required>
                                    <option value="" selected>Select a type</option>
                                    <option value="">Page Load</option>
                                </select>
                            </div> --}}
    
                            {{-- FEEDBACK: Remove --}}
                            {{-- <div class="form-group mb-2">
                                <label for="inputName" class="form-label">Value</label>
                                <div class="row">
                                    <div class="col-xl-6">
                                        <select class="form-select" aria-label="Default select example" name="value_currency" required>
                                            <option value="SGD">SG Dollar (SGD SG$)</option>
                                        </select>
                                    </div>
                                    <div class="col-xl-6">
                                        <input type="number" name="value" id="value"
                                            value="1" placeholder=""
                                            class="form-control" required>
                                    </div>
                                </div>
                            </div> --}}
    
                            <div class="card shadow-none border mb-0 radius-15 mb-3">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            <div class="form-group mb-2">
                                                <label class="form-label fw-bolder">Counting</label>
                                                {{-- <div class="mb-2">
                                                    <small>Select how many conversions to count per click or interaction.</small>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="counting_type[]" id="counting-type-every" value="MANY_PER_CLICK">
                                                    <label class="form-check-label" for="counting-type-every">
                                                        Every
                                                    </label>
                                                    <div>
                                                        <small>Recommended for purchases because every purchase is valuable.</small>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="counting_type[]" id="counting-type-one" value="ONE_PER_CLICK" checked>
                                                    <label class="form-check-label" for="counting-type-one">
                                                        One
                                                    </label>
                                                    <div>
                                                        <small>Recommended for leads, sign-ups and other conversions because only the first interaction is valuable.</small>
                                                    </div>
                                                </div> --}}
                                                <select class="form-select" aria-label="Default select example" name="counting_type[]">
                                                    <option value="ONE_PER_CLICK">One</option>
                                                    <option value="MANY_PER_CLICK">Every</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="card shadow-none border mb-0 radius-15 mb-1">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            <div class="form-group mb-2">
                                                <label for="click_through_days" class="form-label">Click-through conversion window</label>
                                                <select class="form-select" aria-label="Default select example" name="click_through_days[]">
                                                    <option value="90">90 Days</option>
                                                    <option value="60">60 Days</option>
                                                    <option value="45">45 Days</option>
                                                    <option value="30">30 Days</option>
                                                    <option value="28">4 Weeks</option>
                                                    <option value="21">3 Weeks</option>
                                                    <option value="14">2 Weeks</option>
                                                    <option value="7">1 Week</option>
                                                    <option value="3">3 Days</option>
                                                    <option value="1">1 Days</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                            <div class="card shadow-none border mb-0 radius-15 mb-1">
                                <div class="card-body">
                                    <div class="row mb-2">
                                        <div class="col-xl-6">
                                            <div class="form-group mb-2">
                                                <label for="view_through_days" class="form-label">View-through conversion window</label>
                                                <select class="form-select" aria-label="Default select example" name="view_through_days[]">
                                                    <option value="30">30 Days</option>
                                                    <option value="28">4 Weeks</option>
                                                    <option value="21">3 Weeks</option>
                                                    <option value="14">2 Weeks</option>
                                                    <option value="7">1 Week</option>
                                                    <option value="3">3 Days</option>
                                                    <option value="1" selected>1 Days</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-start mt-2 mb-2">
                                <button type="button" class="btn btn-danger remove-button"><i class="bi bi-x-circle-fill"></i> Remove Conversion</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4 mb-2">
                    <div class="col-xl-6 d-flex justify-content-end">
                        <button type="button" class="btn btn-outline ms-2 border-secondary" id="add-button"><span class="fas fa-plus"></span> Conversion Action</button>
                        <button type="submit" class="btn btn-dark ms-2">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function updateCharCount(input) {
            const maxChar = 100;
            let charCount = input.value.length;

            if (charCount > maxChar) {
                input.value = input.value.substring(0, maxChar);
                charCount = maxChar;
            }

            const charCountElement = document.getElementById(`name_char_count`);
            charCountElement.textContent = `${charCount}/${maxChar}`;
        }

        function validateDomainWithSegment(input) {
            const regex = /^[a-zA-Z0-9-]+\.[a-zA-Z]{2,}(\/[a-zA-Z0-9-]+)$/;
            return regex.test(input);
        }

        function hasDuplicates(array) {
            return new Set(array).size !== array.length;
        }
        
        $(document).ready(function() {
            $('form').on('submit', function(event) {
                const nameInputs = $('input[name="name[]"]');
                const urlInputs = $('input[name="website_url[]"]');
                const nameValues = [];
                const urlValues = [];
                let hasError = false;

                // Check for duplicate names
                nameInputs.each(function() {
                    const value = $(this).val().trim();
                    if (value) {
                        nameValues.push(value);
                    }
                });

                if (hasDuplicates(nameValues)) {
                    hasError = true;
                    alert('Duplicate names are not allowed.');
                }

                // Check for duplicate URLs
                // urlInputs.each(function() {
                //     const value = $(this).val().trim();
                //     if (value) {
                //         urlValues.push(value);
                //     }
                // });

                if (hasDuplicates(urlValues)) {
                    hasError = true;
                    alert('Duplicate URLs are not allowed.');
                }

                // Validate domain format
                // urlInputs.each(function() {
                //     const input = $(this).val();
                //     if (!validateDomainWithSegment(input)) {
                //         hasError = true;
                //         $('#error-message').text('The URL must be a domain with one segment after the domain.');
                //         $('#error-message').show();
                //         $(this).addClass('is-invalid');
                //     } else {
                //         $('#error-message').hide();
                //         $(this).removeClass('is-invalid');
                //     }
                // });

                if (hasError) {
                    event.preventDefault();
                }
            });

            $('#add-button').on('click', function() {
                const conversionItem = $('.conversion-item').first().clone(true);
                conversionItem.find('.name-input').val('');
                conversionItem.find('.website-url-input').val('');
                conversionItem.find('.name-char-count').text('0/100');
                conversionItem.appendTo('.conversion-item-container');
                conversionItem.find('select').each(function() {
                    $(this).val($(this).find('option').first().val()); // Set default value
                });
            });

            $('.remove-button').on('click', function() {
                const totalItems = $('.conversion-item').length;
                if (totalItems > 1) {
                    $(this).closest('.conversion-item').remove();
                } else {
                    alert('At least one conversion item is required.');
                }
            });

            $('#copy-button').click(function() {
                // Get the code text
                var codeText = $('#code-block').text();
                console.log(codeText)
                
                // Create a temporary textarea element
                var $textarea = $('<textarea>').val(codeText).appendTo('body').select();
                
                // Execute the copy command
                document.execCommand('copy');
                
                // Remove the temporary textarea
                $textarea.remove();
                
                // Optional: Notify user that text was copied
                alert('Code copied to clipboard!');
            });
        });
    </script>
@endpush