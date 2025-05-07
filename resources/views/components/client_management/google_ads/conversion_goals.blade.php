<div class="row mb-2">
    <div class="col-12">
        <div class="table-responsive">
            <table id="low_bls-template-table" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Conversion goals</th>
                        <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Conversion source</th>
                        <th class="text-uppercase ps-2 text-secondary text-xxs font-weight-bolder opacity-7">Conversion Actions</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actione</th>
                    </tr>
                </thead>
                <tbody>
                    <tbody id="conversion-table-body">
                    </tbody>
                </tbody>
            </table>
        </div>
        <div class="row d-none" id="goal-section">
            <div class="col-xl-4">
                {{-- <label for="goals" class="form-label">Add Goal</label> --}}
                <select class="form-select" name="goal" aria-label="Default select example">
                    <option value="">Add Goal</option>
                    <optgroup label="Sales categories">
                        <option value="PURCHASE" class="d-none">Purchase</option>
                        <option value="ADD_TO_CART" class="d-none">Add to basket</option>
                        <option value="BEGIN_CHECKOUT" class="d-none">Begin checkout</option>
                        <option value="SUBSCRIBE_PAID" class="d-none">Subscribe checkout</option>
                    </optgroup>
                    <optgroup label="Leads categories">
                        <option value="CONTACT" class="d-none">Contact</option>
                        <option value="SUBMIT_LEAD_FORM" class="d-none">Submit lead form</option>
                        <option value="BOOK_APPOINTMENT" class="d-none">Book appointment</option>
                        <option value="SIGNUP" class="d-none">Sign-up</option>
                        <option value="REQUEST_QUOTE" class="d-none">Request quote</option>
                        <option value="GET_DIRECTIONS" class="d-none">Get directions</option>
                        <option value="OUTBOUND_CLICK" class="d-none">Outbound click</option>
                        <option value="PHONE_CALL_LEAD" class="d-none">Phone call lead</option>
                    </optgroup>
                    <optgroup label="More categories">
                        <option value="PAGE_VIEW" class="d-none">Page view</option>
                        <option value="DEFAULT" class="d-none">Default</option>
                        <option value="UNSPECIFIED" class="d-none">Other</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="row d--none">
            <input type="hidden" name="conversion_goals" value="">
        </div>
    </div>
</div>