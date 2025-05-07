<div class="row">
    @switch($type)
        @case('due_today')
            <div class="col col-lg-10">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                    <div class="bg-light-info rounded py-2">
                        <i class="fa-regular fa-calendar" style="
                        margin-left: 10px;
                    "></i>
                        <span><strong>Due Today {{ $due_today }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                    <div class="d-flex justify-content-around align-items-center text-dark bg-light rounded py-2">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>{{ $up_coming }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                    <div class="d-flex justify-content-around align-items-center text-danger bg-light rounded py-2">
                        <i class="fa-regular fa-calendar-xmark"></i>
                        <span><strong>{{ $over_due }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                    <div class="d-flex justify-content-around align-items-center text-secondary bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_someday }}</strong></span>
                    </div>
                </a>
            </div>
            @php
                $type = 'due today';
            @endphp
            @break
        @case('up_comming')
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                    <div class="d-flex justify-content-around align-items-center text-info bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_today }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col col-lg-10">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                    <div class="bg-light-info rounded py-2">
                        <i class="fa-solid fa-calendar-day" style="margin-left: 10px;"></i>
                        <span><strong>Upcomming {{ $up_coming }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                    <div class="d-flex justify-content-around align-items-center text-danger bg-light rounded py-2">
                        <i class="fa-regular fa-calendar-xmark"></i>
                        <span><strong>{{ $over_due }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                    <div class="d-flex justify-content-around align-items-center text-secondary bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_someday }}</strong></span>
                    </div>
                </a>
            </div>
            @php
                $type = 'up comming';
            @endphp
            @break
        @case('over_due')
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                    <div class="d-flex justify-content-around align-items-center text-info bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_today }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                    <div class="d-flex justify-content-around align-items-center text-dark bg-light rounded py-2">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>{{ $up_coming }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col col-lg-10">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                    <div class="bg-light-danger text-danger rounded py-2">
                        <i class="fa-regular fa-calendar-xmark" style="margin-left: 10px;"></i>
                        <span><strong>Overdue {{ $over_due }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                    <div class="d-flex justify-content-around align-items-center text-secondary bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_someday }}</strong></span>
                    </div>
                </a>
            </div>
            @php
                $type = 'over due';
            @endphp
            @break
        @case('due_someday')
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_today">
                    <div class="d-flex justify-content-around align-items-center text-info bg-light rounded py-2">
                        <i class="fa-regular fa-calendar"></i>
                        <span><strong>{{ $due_today }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="up_comming">
                    <div class="d-flex justify-content-around align-items-center text-dark bg-light rounded py-2">
                        <i class="fa-solid fa-calendar-day"></i>
                        <span><strong>{{ $up_coming }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="over_due">
                    <div class="d-flex justify-content-around align-items-center text-danger bg-light rounded py-2">
                        <i class="fa-regular fa-calendar-xmark"></i>
                        <span><strong>{{ $over_due }}</strong></span>
                    </div>
                </a>
            </div>
            <div class="col col-lg-10">
                <a href="javascript:void(0)" class="nav-link get-follow-up" data-type="due_someday">
                    <div class="bg-light text-secondary rounded py-2">
                        <i class="fa-regular fa-calendar" style="margin-left: 10px;"></i>
                        <span><strong>Due Someday {{ $due_someday }}</strong></span>
                    </div>
                </a>
            </div>
            @php
                $type = 'due someday';
            @endphp
            @break
        @default

    @endswitch
</div>
<div class="row mt-3">
    <div class="col">

        <div class="table-responsive" id="followup-responsive">
            <table class="table table-hover mb-0" id="followup-table">
                <thead>
                    <tr>
                        <th scope="col">FOLLOW UPS</th>
                        <th scope="col">NAME</th>
                        <th scope="col">DETAILS</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <br>
        </div>

        <div class="text-center p-5" id="followup_empty" style="display: none;">
            <i class="fa-regular fa-calendar" style="
            font-size: 25px;
        "></i>
            <h5>No follow ups {{ $type }}</h5>
            <p>Set follow ups to plan what's next for each client - have coffee, schedule a meeting, or anything else to keep things going</p>
        </div>
    </div>
</div>
