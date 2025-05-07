@foreach ($data->lead_groups as $val)
    <span class="badge bg-{{ $val->group->background_color }}">{{ $val->group->group_title }}</span>
@endforeach
{{ Str::limit($data->note,50,'...') }}
