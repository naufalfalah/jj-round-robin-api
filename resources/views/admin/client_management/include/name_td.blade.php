@if ($data->status == 'new_lead')
<span class="badge rounded-pill bg-success">New</span> {{ $data->name }}
@elseif ($data->status == 'junk')
<span class="badge rounded-pill bg-danger">Junk</span> {{ $data->name }}
@elseif ($data->status == 'spam')
<span class="badge rounded-pill bg-warning text-dark">Spam</span> {{ $data->name }}
@else
{{ $data->name }}
@endif
