<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Error;
use App\Models\ErrorReason;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ErrorController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/reason/create",
     *     tags={"Error"},
     *     summary="Create reason",
     *     security={{"bearer_token":{}}},
     *     operationId="create reason",
     *
     *     @OA\Parameter(
     *         name="reason",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function reason_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try {
            $reason = new ErrorReason;
            $reason->uuid = Str::uuid()->toString();
            $reason->reason = $request->reason;
            $reason->save();
            return $this->response($reason, 'Reason has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }

    }

     /**
	 *  @OA\Post(
	 *     path="/api/reason/edit",
     *     tags={"Error"},
	 *     summary="Edit reason",
	 *     security={{"bearer_token":{}}},
     *     operationId="edit reason",
     * 
	 *     @OA\Parameter(
	 *         name="reason_id",
	 *         in="query",
	 *         required=true,
	 * 		   description="reason id",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
     *     @OA\Parameter(
     *         name="reason",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function reason_edit(Request $request)
	{
		$validator = Validator::make($request->all(),[
            'reason_id' => 'required|exists:error_reasons,id',
            'reason' => 'required',
		]);
 
		if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,404);
        }

		try {
			$edit_reason = ErrorReason::find($request->reason_id);
			$edit_reason->reason = $request->reason;
			$edit_reason->save();
			return $this->response($edit_reason, 'Reason has been updated!.');

		} catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
		
		
	}

    /**
     *  @OA\Post(
     *     path="/api/reason/delete",
     *     tags={"Error"},
     *     summary="Reason delete",
     *     security={{"bearer_token":{}}},
     *     operationId="reason delete",
     *
     *     @OA\Parameter(
     *         name="reason_id",
     *         in="query",
     *         required=true,
     *            description="reason id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function reason_delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reason_id' => 'required|exists:error_reasons,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,404);
        }

        try {
            $res = ErrorReason::find($request->reason_id);
            if ($res) {
                $res->delete();
                return $this->response([], 'Reason deleted successfully!');
            }
            return $this->response([],'Enter valid reason id!.',false,400);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

     /**
     *  @OA\Get(
     *     path="/api/reason/list",
     *     tags={"Error"},
     *     security={{"bearer_token":{}}},
     *     summary="Get reason List",
     *     security={{"bearer_token":{}}},
     *     operationId="reason List",
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
    public function reason_lists()
    {
        try {
            $res_list = ErrorReason::where('status',1)->get();
            return $this->response($res_list, 'Reason List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }


     /**
     *  @OA\Post(
     *     path="/api/error/create",
     *     tags={"Error"},
     *     summary="Create error",
     *     security={{"bearer_token":{}}},
     *     operationId="create error",
     *
     *     @OA\Parameter(
     *         name="user_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="team_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="project_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="error_detail",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
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
    public function error_create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'error_detail' => 'required',
            'user_id' => 'required|exists:users,id',
            'team_id' => 'required|exists:teams,id',
             'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,401);
        }
        try {
            $error = new Error;
            $error->uuid = Str::uuid()->toString();
            $error->error_detail = $request->error_detail;
            $error->user_id = $request->user_id;
            $error->team_id = $request->team_id;
            $error->project_id = $request->project_id;
            $error->save();
            return $this->response($error, 'Error has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

        /**
     *  @OA\Post(
     *     path="/api/error/status",
     *     tags={"Error"},
     *     security={{"bearer_token":{}}},
     *     summary="Error status approve disapprove",
     *     security={{"bearer_token":{}}},
     *     operationId="error status",
     *
     *    @OA\Parameter(
     *         name="error_id",
     *         in="query",
     *         required=true,
     *            description="error id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         description="1 - approve | 2 - disapprove",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
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
    public function error_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:1,2',
            'error_id' => 'required|exists:errors,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        try {
            $e_status = Error::find($request->error_id);
            $e_status->status = $request->status;
            $e_status->save();
            if ($request->status == 1) {
                $msg = 'Approved successfully!';
            } else {
                $msg = 'Disapproved successfully!';
            }
            return $this->response([], $msg);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    
     /**
     *  @OA\Get(
     *     path="/api/error/list",
     *     tags={"Error"},
     *     security={{"bearer_token":{}}},
     *     summary="Get error List",
     *     security={{"bearer_token":{}}},
     *     operationId="error List",
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
    public function error_lists()
    {
        try {
            $e_list = Error::get();
            return $this->response($e_list, 'Error List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }
}
