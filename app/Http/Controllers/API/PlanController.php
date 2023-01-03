<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanDuration;
use App\Models\PlanHasUser;
use App\Http\Resources\PlanResource;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class PlanController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/plan",
     *     tags={"Plan"},
     *     summary="Create Plan",
     *     security={{"bearer_token":{}}},
     *     operationId="create plan",
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
     *         name="amount",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="duration_in_month",
     *         required=true,
     *         in="query",
     *         description="enter month duration",
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
    public function plan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'amount' => 'required|max:255',
            // 'duration_id' => 'required|exists:plan_durations,id',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try {
            $plan = new Plan;
            $plan->uuid = Str::uuid()->toString();
            $plan->title = $request->title;
            $plan->amount = $request->amount;
            $plan->duration_in_month = $request->duration_in_month;
            $plan->description = $request->description;
            $plan->save();
            return $this->response($plan, 'Plan has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }

    }

    /**
     *  @OA\Get(
     *     path="/api/plan/list",
     *     tags={"Plan"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Plan List",
     *     security={{"bearer_token":{}}},
     *     operationId="Plan",
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
    public function get_plans()
    {
        try {
            $plans = Plan::get();
            return $this->response($plans, 'Plans List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/plan/delete",
     *     tags={"Plan"},
     *     summary="Plan delete",
     *     security={{"bearer_token":{}}},
     *     operationId="plan delete",
     *
     *     @OA\Parameter(
     *         name="plan_id",
     *         in="query",
     *         required=true,
     *            description="plan id",
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
    public function delete_plan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,404);
        }

        try {
            $plan_delete = Plan::find($request->plan_id);
            if ($plan_delete) {
                $plan_delete->delete();
                return $this->response([], 'Plan deleted successfully!');
            }
            return $this->response([],'Enter valid plan id!.',false,400);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

    /**
	 *  @OA\Post(
	 *     path="/api/plan/edit",
     *     tags={"Plan"},
	 *     summary="Edit Plan",
	 *     security={{"bearer_token":{}}},
     *     operationId="edit plan",
     * 
	 *     @OA\Parameter(
	 *         name="plan_id",
	 *         in="query",
	 *         required=true,
	 * 		   description="plan id",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="duration_in_month",
     *         required=true,
     *         in="query",
     *         description="duration in month",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="description",
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
    public function edit_plan(Request $request)
	{
		$validator = Validator::make($request->all(),[
            'plan_id' => 'required|exists:plans,id',
            'duration_in_month' => 'required',
            // 'duration_id' => 'required|exists:plan_durations,id',
            'status' => 'nullable|in:1,2',
		]);
 
		if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,404);
        }

		try {
			$plan_edit = Plan::find($request->plan_id);
			$plan_edit->title = $request->title;
			$plan_edit->amount = $request->amount;
			$plan_edit->duration_in_month = $request->duration_in_month;
			$plan_edit->description = $request->description;
			$plan_edit->save();
			return $this->response($plan_edit, 'Plan has been updated!.');

		} catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
		
		
	}

      /**
     *  @OA\Post(
     *     path="/api/plan/status",
     *     tags={"Plan"},
     *     security={{"bearer_token":{}}},  
     *     summary="Plan status active deactive",
     *     security={{"bearer_token":{}}},
     *     operationId="plan status",
     * 
     *    @OA\Parameter(
	 *         name="plan_id",
	 *         in="query",
	 *         required=true,
	 * 		   description="plan id",
	 *         @OA\Schema(
	 *             type="string"
	 *         )
	 *     ),
     * 
     *     @OA\Parameter(
     *         name="status",
     *         description="1 - active | 2 - deactive",
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
   public function plan_status(Request $request)
   {
    $validator = Validator::make($request->all(),[
        'status' => 'required|in:1,2',
        'plan_id' => 'required|exists:plans,id',
    ]);

    if ($validator->fails()) {
        return $this->response([], $validator->errors()->first(), false,404);
    }
       try{
            $p_status = Plan::find($request->plan_id);
            $p_status->status = $request->status;
            $p_status->save();
            if($request->status == 1){
                $msg = 'Status Active successfully!';
            }else{
                $msg = 'Status Deactive successfully!';
            }
            return $this->response([],$msg);
       }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
       }
   }

     /**
     *  @OA\Post(
     *     path="/api/plan/has/user",
     *     tags={"Plan"},
     *     summary="Plan has user",
     *     security={{"bearer_token":{}}},
     *     operationId="plan has user",
     *
     *     @OA\Parameter(
     *         name="plan_id",
     *         required=true,
     *         in="query",
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
    public function plan_has_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required|exists:plans,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try {
            $plan_has_users = new PlanHasUser;
            $plan_has_users->user_id = Auth::id();
            $plan_has_users->plan_id = $request->plan_id;
            $plan_has_users->save();
            return $this->response($plan_has_users, 'User is starting plan!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }

    }

     /**
     *  @OA\Get(
     *     path="/api/plan/active/user/count/list",
     *     tags={"Plan"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Plan active user count List",
     *     security={{"bearer_token":{}}},
     *     operationId="Plan active user count list",
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
    public function plan_active_user_lists()
    {
        try {
            $plan_has_users = Plan::withCount('activeUser')->where('status',1)->get();
            return $this->response($plan_has_users, 'Plans active users count List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }
}
