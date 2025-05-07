
<div class="card shadow-none border mb-0 radius-15 mb-3">
    <div class="card-header">
        <small>Event snippets help measure users’ actions based on your conversion goals. You need to set up an event snippet for each goal.
        </small>
    </div>
    <div class="card-body">
        <div class="row mb-2">
            <div class="col-xl-12">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                Add to basket
                            </button>
                        </h2>
                        <div id="collapse1" class="accordion-collapse collapse show" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                Begin checkout
                            </button>
                        </h2>
                        <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                Book appointment
                            </button>
                        </h2>
                        <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                Contact
                            </button>
                        </h2>
                        <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                Other
                            </button>
                        </h2>
                        <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading6">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                Outbond Click
                            </button>
                        </h2>
                        <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading7">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                Page View
                            </button>
                        </h2>
                        <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {'send_to': 'AW-{{ session('conversionActionId') }}'});
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading8">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                Purchase
                            </button>
                        </h2>
                        <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <small>The event snippet will work with the Google tag to measure actions that should be counted as conversions. </small>
                                <pre>
                                    <code class="code-block">
&lt;!-- Event snippet for {{ session('conversionActionName') }} --&gt;
&lt;script&gt;
    gtag('event', 'conversion', {
        'send_to': 'AW-{{ session('conversionActionId') }}'
        'transaction_id': ''
    });  
&lt;script&gt;
                                    </code>
                                </pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>