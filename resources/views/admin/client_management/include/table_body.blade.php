@foreach ($folder_files as $key => $files)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td><a href="{{ asset($files->file_path) }}" target="_blank">{{ Str::limit($files->file_name, 40, '...') ?? '' }}</a></td>
        <td>{{ $files->created_at->format('Y-m-d H:i:s') ?? '' }}</td>
        <td>
            <a href="javascript:void(0)" class="text-success copy-button" data-clipboard-text="{{ asset($files->file_path) }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Copy File Url">
                <i class="fadeIn animated bx bx-copy"></i>
            </a>
        </td>
    </tr>
@endforeach

<script>
    $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    })
</script>
