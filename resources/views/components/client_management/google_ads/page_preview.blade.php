<div class="tab-content mt-3" id="page-2">
    <div class="tab-pane fade show active" id="Edit-Profile">

        <div class="row">
            <div class="col-6">
                <div class="card shadow-none border mb-0 radius-15 mb-3">
                    <div class="card-header">
                        <div class="card-title">Configuration</div>
                    </div>
                    <div class="card-body">
                        <div class="form-body">
                            <p><b>Campaign:</b> <span class="campaign-name"></span></p>
                            <p><b>Target URL:</b> <span class="target-url"></span></p>
                            <p><b>Campaign Type:</b> <span class="campaign-type"></span></p>
                            <p><b>Platforms:</b> <span class="settings"></span></p>
                        </div>
                    </div>
                </div>

                <div class="card shadow-none border mb-0 radius-15">
                    <div class="card-header">
                        <div class="card-title">Budget</div>
                    </div>
                    <div class="card-body">
                        <p><b>Type:</b> <span class="budget-type"></span></p>
                        <p><b>Total:</b> <span class="budget-total"></span></p>
                        <p><b>Bid Cap:</b> <span class="budget-bid-cap"></span></p>
                        <p class="p-budget-max-cpc"><b>Max CPC:</b> <span class="budget-max-cpc"></span></p>
                        <p class="p-budget-start"><b>Start:</b> <span class="budget-start"></span></p>
                        <p class="p-budget-end"><b>End:</b> <span class="budget-end"></span></p>
                    </div>
                </div>
            </div>

            <div class="col-6">
                <div class="card shadow-none border mb-0 radius-15">
                    <div class="card-header">
                        <div class="card-title">Design</div>
                    </div>
                    <div class="card-body">
                        <div class="form-body">
                            <div class="row mb-2">
                                <div class="col-2">
                                    <img class="card-img-top rounded img-logo" src="{{  asset('front/assets/images/no-image.jpg') }}" alt="Card image cap">
                                </div>

                                <div class="col-10">
                                    <p><b class="ad-name">Ad Title</b></p>
                                    <p class="ad-headline-1">Heading</p>
                                    <p class="ad-url">URL</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-2">
                <div class="col-6 d-flex justify-content-start">
                    <button type="button" class="btn btn-outline border" id="button-previous">Previous</button>
                </div>
                <div class="col-6 d-flex justify-content-end">
                    <button type="submit" class="btn btn-dark">Submit</button>
                </div>
            </div>
        </div>

    </div>
</div>