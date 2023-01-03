<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationDuration;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class NotificationController extends Controller
{
    use ApiTrait;
  
    /**
     *  @OA\Post(
     *     path="/api/notification/create",
     *     tags={"Notification"},
     *     summary="Create Notification",
     *     security={{"bearer_token":{}}},
     *     operationId="create notification",
     *
     *     @OA\Parameter(
     *         name="title",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="description",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="duration_in_day",
     *         required=true,
     *         in="query",
     *         description="enter duration",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="description",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *   @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="photo",
     *                      description="photo",
     *                      type="file",
     *                   ),
     *               ),
     *           ),
     *       ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
     **/
    public function notification_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required|max:255',
            'duration_in_day' => 'required',
            // 'notification_duration_id' => 'required|exists:notification_durations,id',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $filename = null;
            if($request->hasFile('photo'))
            {
                $file = $request->file('photo');
                $filename = time().$file->getClientOriginalName();
                $file->move(public_path().'/notifications/', $filename);  
            }
            $notification = new Notification;
            $notification->uuid = Str::uuid()->toString();
            $notification->title = $request->title;
            $notification->description = $request->description;
            $notification->duration_in_day = $request->duration_in_day;
            if($request->hasFile('photo'))
            {
                $notification->photo = $filename;
            }
            $notification->save();
            return $this->response($notification, 'Notification has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

     /**
     *  @OA\Get(
     *     path="/api/notification/list",
     *     tags={"Notification"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Notification List",
     *     security={{"bearer_token":{}}},
     *     operationId="Notification List",
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity"
     *     ),
     * )
     **/
    public function list_notifications()
    {
        try {
            $notification = Notification::get();
            return $this->response($notification, 'Notifications List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

}
