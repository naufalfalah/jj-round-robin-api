<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\ClientFiles;
use App\Models\ClientFolder;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group File Manager
 *
 * @subgroup File
 *
 * @authenticated
 */
class FileController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('check.menu:file_manager');
    }

    public function getFiles(Request $request)
    {
        $userId = auth('api')->id();
        $client = User::find($userId);

        $clientFiles = ClientFiles::where('client_id', $userId)
            ->orderBy('id', $request->get('order', 'desc'))
            ->get()
            ->map(function ($file) use ($client) {
                return [
                    'id' => $file->id,
                    'file_name' => $file->file_name,
                    'folder' => $file->folder,
                    'file_path' => asset($file->file_path),
                    'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $file->updated_at->format('Y-m-d H:i:s'),
                    'trackable_link' => route('client.file_view', [$file->hashid, $client->hashid]),
                ];
            });

        ActivityLogHelper::save_activity($userId, 'View All Files', 'ClientFiles', 'app');

        if ($clientFiles->isEmpty()) {
            return $this->sendErrorResponse('No Files found.', 404);
        }

        return $this->sendSuccessResponse('Client Files Fetch Successfully', ['clients_files' => $clientFiles]);
    }

    /**
     * Upload File
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_id' => [Rule::exists('client_folders', 'id')],
            'choose_file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $userId = auth('api')->id();
        $folder = ClientFolder::where([
            'id' => $request->folder_id,
            'client_id' => $userId,
        ])->first();

        if (!$folder) {
            return $this->sendErrorResponse('Folder not found.', 404);
        }

        $filename = $request->file_name ?? pathinfo($request->file('choose_file')->getClientOriginalName(), PATHINFO_FILENAME);
        $folderPath = $folder ? "uploads/{$folder->folder_name}/" : 'uploads/client_files/';

        $filePath = uploadSingleFile($request->file('choose_file'), $folderPath, 'pdf,doc,docx,jpeg,png,jpg,webp,zip,mp3,mp4,xls,xlsx,doc');

        if (is_array($filePath)) {
            return response()->json($filePath);
        }

        $clientFile = ClientFiles::create([
            'file_name' => $filename,
            'client_id' => $userId,
            'main_folder_id' => $folder?->id,
            'file_path' => $filePath,
        ]);

        ActivityLogHelper::save_activity($userId, 'Upload File', 'ClientFile', 'app');

        return $this->sendSuccessResponse('File Uploaded Successfully', $clientFile);
    }

    public function renameFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file_name' => 'required',
            'file_id' => 'required|integer|exists:client_files,id',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $clientFile = ClientFiles::find($request->file_id);
        $newFilePath = dirname($clientFile->file_path).'/'.$request->file_name.'.'.pathinfo($clientFile->file_path, PATHINFO_EXTENSION);

        if (!@rename($clientFile->file_path, $newFilePath)) {
            return $this->sendErrorResponse('Could not rename the file.', 500);
        }

        $clientFile->update(['file_name' => $request->file_name, 'file_path' => $newFilePath]);
        ActivityLogHelper::save_activity(auth('api')->id(), 'Rename File', 'ClientFile', 'app');

        return $this->sendSuccessResponse('File renamed successfully', $clientFile);
    }

    public function deleteFile(Request $request)
    {
        $validator = Validator::make($request->all(), ['file_id' => 'required|integer|exists:client_files,id']);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $clientFile = ClientFiles::find($request->file_id);

        if (file_exists($clientFile->file_path)) {
            @unlink($clientFile->file_path);
        }

        $clientFile->delete();
        ActivityLogHelper::save_activity(auth('api')->id(), 'Delete File', 'ClientFiles', 'app');

        return $this->sendSuccessResponse('File Deleted Successfully');
    }
}
