@extends('layouts.admin')
@section('content')
<div class="page-breadcrumb d-flex align-items-center mb-3 mt-5">
    <div class="breadcrumb-title">{{@$breadcrumb_main}}</div>
    <div class="">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item active" aria-current="page">
                    <h4>{{@$breadcrumb}}</h4>
                </li>
            </ol>
        </nav>
    </div>

</div>

<div class="row mt-4">
    <div class="col-12 col-md-8">
        <h5><strong> Client Info </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row g-0">
                    <div class="col-12 col-md-6">
                        <div class="col-12 col-md-12">
                            <span><strong>DISPLAY NAME</strong></span>
                            <p>{{ $data->name }}</p>
                        </div>
                        <div class="col-12 col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>MOBILE NUMBER</strong></span>
                                    <p>+65{{ $data->mobile_number }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="tel:65{{ $data->mobile_number }}" class="save_timeline" data-type="phone">
                                        <i class="fa-solid fa-phone-flip" style="color: #17a2b8;font-size: 1.5em;
                                        line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>WHATSAPP NUMBER</strong></span>
                                    <p>+65{{ $data->mobile_number }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="https://api.whatsapp.com/send?phone=65{{ $data->mobile_number }}" target="_blank" class="save_timeline" data-type="message"> <i
                                            class="fa-brands fa-whatsapp" style="color: #17a2b8;font-size: 1.5em;
                                    line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-8">
                                    <span><strong>EMAIL ADDRESS</strong></span>
                                    <p>{{ $data->email }}</p>
                                </div>
                                <div class="col-4">
                                    <a href="mailto:{{ $data->email }}" class="save_timeline" data-type="emall">
                                        <i class="fa-solid fa-envelope" style="color: #17a2b8;font-size: 1.5em;
                                        line-height: .75em;"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        {{-- <div class="col-md-12 mb-3">
                            <label class="form-label"><strong>GROUPS</strong></label>
                            <input type="hidden" name="group_lead_id" id="group_lead_id" value="{{ $data->hashid }}">
                            <select class="form-control multiple-select2 add_group_lead" multiple="multiple">
                                @foreach ($client_groups as $group)
                                <option value="{{ $group->id }}" {{ in_array($group->id, $data->lead_groups()->pluck('group_id')->toArray()) ? 'selected' : '' }}> {{ $group->group_title }}</option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-md-12">
                            <span><strong>NOTES</strong></span>
                            <p>{{ $data->note ?? 'Click to add notes about your client...' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <h5><strong> Timeline </strong></h5>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <!-- timeline item 1 left dot -->
                    <div class="col-auto text-center flex-column d-sm-flex">
                        <div class="row h-50">
                            <div class="col">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                        <h5 class="">
                            <i class="fa-solid fa-circle-plus"></i>
                        </h5>
                        <div class="row h-50">
                            <div class="col border-end">&nbsp;</div>
                            <div class="col">&nbsp;</div>
                        </div>
                    </div>
                </div>
                @foreach ($data->activity()->latest()->get() as $k => $item)
                    <div class="row">
                        @if ($item->type == 'add')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-user-plus" style=" font-size: 16px; "></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'message')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                                @if ((isset($item->message_template_id) && !empty($item->message_template_id)))
                                    <h5 class="">
                                        <i class="fa-solid fa-paper-plane" style=" font-size: 16px;"></i>
                                    </h5>
                                @else
                                    <h5 class="">
                                        <i class="fa-regular fa-comment edit-activity" data-data="{{ $item }}" style=" font-size: 16px; cursor: pointer;"></i>
                                    </h5>
                                @endif
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'email')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                                @if ((isset($item->email_template_id) && !empty($item->email_template_id)))
                                    <h5 class="">
                                        <i class="fa-solid fa-paper-plane" style=" font-size: 16px;"></i>
                                    </h5>
                                @else
                                    <h5 class="">
                                        <i class="fa-regular fa-comment edit-activity" data-data="{{ $item }}" style=" font-size: 16px; cursor: pointer;"></i>
                                    </h5>
                                @endif
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'phone')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-phone-flip edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'meeting')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-calendar-day edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @elseif ($item->type == 'note')
                        <div class="col-auto text-center flex-column d-sm-flex">
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                            <h5 class="">
                                <i class="fa-solid fa-note-sticky edit-activity" data-data="{{ $item }}" style="cursor: pointer;"></i>
                            </h5>
                            <div class="row h-50">
                                <div class="col border-end">&nbsp;</div>
                                <div class="col">&nbsp;</div>
                            </div>
                        </div>
                        @endif
                        <div class="col py-2">
                            <div class="row">
                                <div class="col-12 col-lg-12 d-flex">
                                    <div class="card shadow radius-10 w-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center">
                                                <h6 class="mb-0">{{ $item->title }}</h6>
                                                @if ($item->type !== 'add')
                                                <div class="fs-5 ms-auto dropdown">
                                                    <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></div>
                                                    <ul class="dropdown-menu">
                                                        @if (isset($item->message_template_id) && !empty($item->message_template_id))
                                                            <li><a class="dropdown-item text-success float-end" href="{{ route('user.message-template.temp_details', hashids_encode($item->message_template_id)) }}">Go To Message Detail</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @elseif(isset($item->email_template_id) && !empty($item->email_template_id))
                                                            <li><a class="dropdown-item text-success float-end" href="{{ route('user.email-template.temp_details', hashids_encode($item->email_template_id)) }}">Go To Email Detail</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @else
                                                            <li><a class="dropdown-item text-success edit-activity float-end" data-data="{{ $item }}" href="Javascript:void(0);">Edit</a></li>
                                                            <li><a class="dropdown-item text-danger float-end" onclick="ajaxRequest(this)" data-url="{{route('user.leads-management.activity_delete',$item->id)}}" href="Javascript:void(0);">Delete</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                                @endif

                                            </div>
                                            <div class="d-flex w-100 justify-content-between">
                                                <small class="text-muted">{{ $item->created_at->format('M d, Y h:i a') }}</small>
                                            </div>
                                            <small class="text-muted">{{ Str::limit($item->description, 100, '...') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection
@section('page-scripts')

@endsection
