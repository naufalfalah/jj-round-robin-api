<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PostSchedule;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Request Service
 *
 * @subgroup Social Media Marketing
 *
 * @authenticated
 */
class PostScheduleController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Post Schedules
     */
    public function indexPostSchedules(Request $request): JsonResponse
    {
        $clientId = auth('api')->user()->id;
        $user = User::find($clientId);

        $posts = [];
        $postSchedules = new PostSchedule;

        if ($user) {
            $postSchedules = $postSchedules->where('client_id', $user->id);
        }

        if ($request->platform == 'linkedin') {
            $postSchedules = $postSchedules->where('linkedin', 1);
        }
        if ($request->platform == 'facebook') {
            $postSchedules = $postSchedules->where('facebook', 1);
        }
        if ($request->platform == 'instagram') {
            $postSchedules = $postSchedules->where('instagram', 1);
        }
        if ($request->platform == 'google_business') {
            $postSchedules = $postSchedules->where('google_business', 1);
        }
        if ($request->platform == 'youtube') {
            $postSchedules = $postSchedules->where('youtube', 1);
        }
        if ($request->platform == 'tiktok') {
            $postSchedules = $postSchedules->where('tiktok', 1);
        }
        if ($request->platform == 'xiao_hong_shu') {
            $postSchedules = $postSchedules->where('xiao_hong_shu', 1);
        }

        if ($request->start && $request->end) {
            $postSchedules = $postSchedules->whereBetween('date', [$request->start, $request->end]);
        }
        $postSchedules = $postSchedules->get();

        foreach ($postSchedules as $postSchedule) {
            $platforms = [];
            if ($postSchedule->linkedin) {
                $platforms[] = 'linkedin';
            }
            if ($postSchedule->facebook) {
                $platforms[] = 'facebook';
            }
            if ($postSchedule->instagram) {
                $platforms[] = 'instagram';
            }
            if ($postSchedule->google_business) {
                $platforms[] = 'google_business';
            }
            if ($postSchedule->youtube) {
                $platforms[] = 'youtube';
            }
            if ($postSchedule->tiktok) {
                $platforms[] = 'tiktok';
            }
            if ($postSchedule->xiao_hong_shu) {
                $platforms[] = 'xiao_hong_shu';
            }
            $posts[] = [
                'id' => $postSchedule->id,
                'title' => $postSchedule->title,
                'description' => $postSchedule->description,
                'start' => $postSchedule->date.'T'.$postSchedule->time,
                'end' => $postSchedule->date.'T'.$postSchedule->time,
                'published' => $postSchedule->published,
                'platforms' => $platforms,
            ];
        }

        return $this->sendSuccessResponse('Post schedules fetched successfully', $posts);
    }

    /**
     * Create Post Schedule
     */
    public function storePostSchedules(Request $request)
    {
        $postSchedule = new PostSchedule;
        $postSchedule->client_id = (int) $request->input('client_id');
        $postSchedule->title = $request->input('title');
        $postSchedule->description = $request->input('text');
        $postSchedule->date = $request->input('date');
        $postSchedule->time = $request->input('time');
        if ($request->input('linkedin')) {
            $postSchedule->linkedin = true;
            $postSchedule->linkedin_container_type = $request->input('linkedin_container_type');
            $postSchedule->linkedin_container_id = $request->input('linkedin_container_id');
            $postSchedule->linkedin_post_type = $request->input('linkedin_post_type');
            $postSchedule->linkedin_text = $request->input('text') ?? null;
            $postSchedule->linkedin_media_title = $request->input('linkedin_media_title') ?? null;
            $postSchedule->linkedin_media_description = $request->input('linkedin_media_description') ?? null;
            $postSchedule->linkedin_link_url = $request->input('linkedin_link_url') ?? null;
            if ($request->file('linkedin_image')) {
                $file = $request->file('linkedin_image');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->linkedin_image = $filepath.'/'.$filename;
            }
            $postSchedule->linkedin_visibility = $request->input('linkedin_visibility');
        }
        if ($request->input('facebook')) {
            $postSchedule->facebook = true;
            $postSchedule->facebook_container_type = $request->input('facebook_container_type');
            $postSchedule->facebook_container_id = $request->input('facebook_container_id');
            $postSchedule->facebook_post_type = $request->input('facebook_post_type');
            $postSchedule->facebook_message = $request->input('text');
            $postSchedule->facebook_link = $request->input('facebook_link');
            if ($request->file('facebook_media')) {
                $file = $request->file('facebook_media');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->facebook_media = $filepath.'/'.$filename;
            }
        }
        if ($request->input('instagram')) {
            $postSchedule->instagram = true;
            $postSchedule->instagram_post_type = $request->input('instagram_post_type');
            $postSchedule->instagram_caption = $request->input('text');
            $postSchedule->instagram_link = $request->input('instagram_link');
        }
        if ($request->input('google_business')) {
            $postSchedule->google_business = true;
            $postSchedule->google_business_summary = $request->input('text') ?? null;
            if ($request->file('google_business_media')) {
                $file = $request->file('google_business_media');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->google_business_media = $filepath.'/'.$filename;
            }
        }
        if ($request->input('youtube')) {
            $postSchedule->youtube = true;
            if ($request->file('youtube_video')) {
                $file = $request->file('youtube_video');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->youtube_video = $filepath.$filename;
            }
            $postSchedule->youtube_video_title = $request->input('youtube_video_title');
            $postSchedule->youtube_video_description = $request->input('youtube_video_description');
            $postSchedule->youtube_privacy_status = $request->input('youtube_privacy_status');
            $postSchedule->youtube_category_id = $request->input('youtube_category_id');
            $postSchedule->youtube_tags = $request->input('youtube_tags');
        }
        if ($request->input('tiktok')) {
            $postSchedule->tiktok = true;
            $postSchedule->tiktok_title = $request->input('tiktok_title');
            $postSchedule->tiktok_privacy_level = $request->input('tiktok_privacy_level');
            $postSchedule->tiktok_post_type = $request->input('tiktok_post_type');
            if ($request->file('tiktok_video')) {
                $file = $request->file('tiktok_video');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->tiktok_video = $filepath.'/'.$filename;
            }
            if ($request->hasFile('tiktok_content')) {
                $files = $request->file('tiktok_content');
                $filepaths = [];

                foreach ($files as $file) {
                    $filepath = 'uploads/smm/';
                    $filename = time().'-'.$file->getClientOriginalName();
                    $file->move($filepath, $filename);
                    $filepaths[] = $filepath.$filename;
                }
                $postSchedule->tiktok_content = implode(',', $filepaths);
            }
            if ($request->input('xiao_hong_shu')) {
            }
            $postSchedule->tiktok_description = $request->input('text');
            $postSchedule->tiktok_disable_comment = $request->input('tiktok_allow_comment') ? false : true;
            $postSchedule->tiktok_disable_duet = $request->input('tiktok_allow_duet') ? false : true;
            $postSchedule->tiktok_disable_stitch = $request->input('tiktok_allow_stitch') ? false : true;
        }
        $postSchedule->save();

        return $this->sendSuccessResponse('Post schedules stored successfully', $postSchedule);
    }

    /**
     * Get Post Schedule
     */
    public function showPostSchedule(int $id): JsonResponse
    {
        $postSchedule = PostSchedule::find($id);
        if (!$postSchedule) {
            return $this->sendErrorResponse('No Post Schedule Found.', 404);
        }

        return $this->sendSuccessResponse('Post schedule fetched successfully', $postSchedule);
    }

    /**
     * Update Post Schedule
     */
    public function updatePostSchedule(Request $request, int $id)
    {
        $postSchedule = PostSchedule::find($id);
        $postSchedule->client_id = (int) $request->input('client_id');
        $postSchedule->title = $request->input('title');
        $postSchedule->description = $request->input('text');
        $postSchedule->date = $request->input('date');
        $postSchedule->time = $request->input('time');
        if ($request->input('linkedin')) {
            $postSchedule->linkedin = true;
            $postSchedule->linkedin_container_type = $request->input('linkedin_container_type');
            $postSchedule->linkedin_container_id = $request->input('linkedin_container_id');
            $postSchedule->linkedin_post_type = $request->input('linkedin_post_type');
            $postSchedule->linkedin_text = $request->input('text') ?? null;
            $postSchedule->linkedin_media_title = $request->input('linkedin_media_title') ?? null;
            $postSchedule->linkedin_media_description = $request->input('linkedin_media_description') ?? null;
            $postSchedule->linkedin_link_url = $request->input('linkedin_link_url') ?? null;
            if ($request->file('linkedin_image')) {
                $file = $request->file('linkedin_image');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->linkedin_image = $filepath.'/'.$filename;
            }
            $postSchedule->linkedin_visibility = $request->input('linkedin_visibility');
        }
        if ($request->input('facebook')) {
            $postSchedule->facebook = true;
            $postSchedule->facebook_container_type = $request->input('facebook_container_type');
            $postSchedule->facebook_container_id = $request->input('facebook_container_id');
            $postSchedule->facebook_post_type = $request->input('facebook_post_type');
            $postSchedule->facebook_message = $request->input('text');
            $postSchedule->facebook_link = $request->input('facebook_link');
        }
        if ($request->input('instagram')) {
            $postSchedule->instagram = true;
            $postSchedule->instagram_post_type = $request->input('instagram_post_type');
            $postSchedule->instagram_caption = $request->input('text');
            $postSchedule->instagram_link = $request->input('instagram_link');
        }
        if ($request->input('google_business')) {
            $postSchedule->google_business = true;
            $postSchedule->google_business_summary = $request->input('text') ?? null;
            if ($request->file('google_business_media')) {
                $file = $request->file('google_business_media');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->google_business_media = $filepath.'/'.$filename;
            }
        }
        if ($request->input('youtube')) {
            $postSchedule->youtube = true;
            if ($request->file('youtube_video')) {
                $file = $request->file('youtube_video');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->youtube_video = $filepath.$filename;
            }
            $postSchedule->youtube_video_title = $request->input('youtube_video_title');
            $postSchedule->youtube_video_description = $request->input('youtube_video_description');
            $postSchedule->youtube_privacy_status = $request->input('youtube_privacy_status');
            $postSchedule->youtube_category_id = $request->input('youtube_category_id');
            $postSchedule->youtube_tags = $request->input('youtube_tags');
        }
        if ($request->input('tiktok')) {
            $postSchedule->tiktok = true;
            $postSchedule->tiktok_title = $request->input('tiktok_title');
            $postSchedule->tiktok_privacy_level = $request->input('tiktok_privacy_level');
            $postSchedule->tiktok_post_type = $request->input('tiktok_post_type');
            if ($request->file('tiktok_video')) {
                $file = $request->file('tiktok_video');
                $filepath = 'uploads/smm/';
                $filename = time().'.'.$file->getClientOriginalName();
                $file->move($filepath, $filename);
                $postSchedule->tiktok_video = $filepath.'/'.$filename;
            }
            if ($request->hasFile('tiktok_content')) {
                $files = $request->file('tiktok_content');
                $filepaths = [];

                foreach ($files as $file) {
                    $filepath = 'uploads/smm/';
                    $filename = time().'-'.$file->getClientOriginalName();
                    $file->move($filepath, $filename);
                    $filepaths[] = $filepath.$filename;
                }
                $postSchedule->tiktok_content = implode(',', $filepaths);
            }
            if ($request->input('xiao_hong_shu')) {
            }
            $postSchedule->tiktok_description = $request->input('text');
            $postSchedule->tiktok_disable_comment = $request->input('tiktok_allow_comment') ? false : true;
            $postSchedule->tiktok_disable_duet = $request->input('tiktok_allow_duet') ? false : true;
            $postSchedule->tiktok_disable_stitch = $request->input('tiktok_allow_stitch') ? false : true;
        }
        $postSchedule->save();

        return $this->sendSuccessResponse('Post schedules updated successfully', $postSchedule);
    }

    /**
     * Delete Post Schedule
     */
    public function destroyPostSchedule(int $id): JsonResponse
    {
        $postSchedule = PostSchedule::find($id);
        if (!$postSchedule) {
            return $this->sendErrorResponse('No Post Schedule Found.', 404);
        }

        $postSchedule->delete();

        return $this->sendSuccessResponse('Post schedule deleted successfully');
    }
}
