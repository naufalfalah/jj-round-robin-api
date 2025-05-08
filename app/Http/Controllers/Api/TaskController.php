<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ChatMessage;
use App\Models\FormData;
use App\Models\FormGroup;
use App\Models\FormRequest;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;

/**
 * @group Tasks
 *
 * @authenticated
 */
class TaskController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Tasks
     */
    public function getTasks()
    {
        $clientId = auth('api')->user()->id;
        $groups = FormGroup::all()->map(function ($group) use ($clientId) {
            $tasks = FormRequest::where([
                'form_group_id' => $group->id,
                'client_id' => $clientId,
            ])->get();

            return [
                'group' => $group->name,
                'tasks' => $tasks,
            ];
        });

        $data = [
            'groups' => $groups,
        ];

        return $this->sendSuccessResponse('Tasks Fetch Successfully', $data);
    }

    /**
     * Get Task
     */
    public function getTask(int $id)
    {
        $userId = auth('api')->user()->id ?? auth('admin')->user()->id;
        $taskId = $id;
        $formRequest = FormRequest::with('form_data', 'assigns', 'assigns.admin', 'subtasks', 'subtasks.admin', 'activities', 'activities.admin', 'activities.target')
            ->find($taskId);

        $formData = FormData::where('form_request_id', $formRequest->id)
            ->where(function ($query) {
                $query->where('f_key', 'upload_refrence')
                    ->orWhere('f_key', 'user_data_file');
            })->get();

        $formRequest['files'] = $formData;

        $type = Admin::class;
        $from = [
            ['form_request_id', $formRequest->id],
            ['user_id', $userId],
            ['user_type', $type],
        ];

        $to = [
            ['form_request_id', $formRequest->id],
            ['to_user_id', (int) $userId],
            ['to_user_type', $type],
        ];

        $messages = ChatMessage::with('user')->where($from);
        $first = $messages;
        if ($first->exists()) {
            ChatMessage::where($from)->update(['read_at' => now()]);
        }
        $messages->where($from)->orWhere($to)->orderBy('created_at', 'asc');
        $messageDatas = $messages->get();
        $groupedMessages = $messageDatas->groupBy(function ($item) {
            // Format the created_at field based on your requirements
            return $item->created_at->isToday()
                ? 'Today'
                : $item->created_at->format('d F Y');
        });
        $formattedMessages = [];
        $count = ChatMessage::where('form_request_id', $formRequest->id)
            ->whereNull('read_at')
            ->count();
        $chats = ChatMessage::with('user')
            ->where('form_request_id', $formRequest->id)
            ->get();
        $chatData = [];

        foreach ($chats as $chat) {
            $formattedCreatedAt = $chat->created_at->isToday()
                ? 'Today'
                : $chat->created_at->format('d F Y');
            $dateTime = Carbon::parse($chat->created_at);
            $timeFormatted = $dateTime->format('h:i A');
            $chatData[] = [
                'id' => $chat->user->id,
                'name' => $chat->user->full_name,
                'email' => $chat->user->email,
                'from_id' => $chat->from_user_id,
                'from_type' => $chat->from_user_type,
                'to_user_id' => $chat->to_user_id,
                'to_user_type' => $chat->to_user_type,
                'message' => $chat->message != '' || $chat->message != null ? $chat->message : '',
                'image' => $chat->attachments != null ? json_decode($chat->attachments) : null,
                'created_at' => $chat->created_at,
                'time' => $timeFormatted,
                'formatted_created_at' => $formattedCreatedAt,
                'type_name' => $chat->user->table_name,
                'count' => $count,
            ];
        }

        foreach ($groupedMessages as $key => $group) {
            $formattedMessages[$key] = $chatData;
        }

        $data = [
            'data' => $formRequest,
            'messages' => $formattedMessages,
        ];

        return $this->sendSuccessResponse('Task Fetch Successfully', $data);
    }
}
