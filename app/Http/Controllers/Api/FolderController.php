<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\ClientFolder;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group File Manager
 *
 * @subgroup Folder
 *
 * @authenticated
 */
class FolderController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
        $this->middleware('check.menu:file_manager');
    }

    /**
     * Get Folders
     */
    public function getFolders(Request $request)
    {
        $userId = auth('api')->id();

        $clientFolders = ClientFolder::with('client_files', 'client_main_folder_files')
            ->where('client_id', $userId)
            ->orderBy('id', $request->get('order', 'desc'))
            ->get()
            ->map(function ($folder) {
                $folderFiles = $folder->client_files->merge($folder->client_main_folder_files)
                    ->map(function ($file) use ($folder) {
                        return [
                            'id' => $file->id,
                            'type' => 'file',
                            'file_name' => $file->file_name,
                            'file_path' => asset($file->file_path),
                            'folder_name' => $folder->folder_name,
                            'created_at' => $file->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $file->updated_at->format('Y-m-d H:i:s'),
                        ];
                    });

                return [
                    'id' => $folder->id,
                    'type' => 'folder',
                    'parent_folder_id' => $folder->parent_folder_id,
                    'folder_name' => $folder->folder_name,
                    'created_at' => $folder->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $folder->updated_at->format('Y-m-d H:i:s'),
                    'files' => $folderFiles->isNotEmpty() ? $folderFiles : ['This Folder Is Empty'],
                ];
            });

        ActivityLogHelper::save_activity($userId, 'View All Folders & Files', 'ClientFolder', 'app');

        if ($clientFolders->isEmpty()) {
            return $this->sendErrorResponse('No Folders found.', 404);
        }

        return $this->sendSuccessResponse('Client Folders Fetch Successfully', ['clients_folders' => $clientFolders]);
    }

    /**
     * Create Folder
     */
    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_name' => 'required|string|max:255',
            'parent_folder_id' => 'nullable|integer|exists:client_folders,id',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $clientFolder = ClientFolder::create([
            'folder_name' => $request->folder_name,
            'client_id' => auth('api')->id(),
            'parent_folder_id' => $request->parent_folder_id,
        ]);

        ActivityLogHelper::save_activity(auth('api')->id(), 'Create Folder', 'ClientFolder', 'app');

        return $this->sendSuccessResponse('Folder created successfully', $clientFolder);
    }

    public function renameFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folder_id' => 'required|integer|exists:client_folders,id',
            'folder_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first(), 400);
        }

        $clientFolder = ClientFolder::find($request->folder_id);
        $clientFolder->update(['folder_name' => $request->folder_name]);

        ActivityLogHelper::save_activity(auth('api')->id(), 'Rename Folder', 'ClientFolder', 'app');

        return $this->sendSuccessResponse('Folder renamed successfully', $clientFolder);
    }
}
