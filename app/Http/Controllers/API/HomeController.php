<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectHasOwner;
use App\Models\ProjectHasTeamMember;
use App\Models\ProjectProcess;
use App\Traits\ApiTrait;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    use ApiTrait;

     /**
     *  @OA\Get(
     *     path="/api/admin/dashboard/project/process/count",
     *     tags={"Dashboard"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Admin Project process Count List",
     *     security={{"bearer_token":{}}},
     *     operationId="Get Admin Project process Count List",
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
    public function admin_dashboard_project_process_count()
    {
        try {
            $fd_count = ProjectProcess::where('project_status',1)->count();
            $rc_count = ProjectProcess::where('project_status',2)->count();
            $fc_count = ProjectProcess::where('project_status',3)->count();
            $cf_count = ProjectProcess::where('project_status',4)->count();

            $data['Total pending files'] = $fd_count;
            $data['Total pending review'] = $rc_count;
            $data['Total pushed'] = $fc_count;
            $data['Total competed files'] = $cf_count;

            return $this->response([$data], 'Admin Dashboard Project Process Count');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }
 

    /**
     *  @OA\Get(
     *     path="/api/admin/dashboard/recent/project",
     *     tags={"Dashboard"},
     *     security={{"bearer_token":{}}},
     *     summary="Get all recent project list admin",
     *     operationId="Get all recent Project List admin",
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
    public function admin_all_recent_projects()
    {
        try {
            $recent_project = project::with('projectOwner')->latest('created_at')->get();
            return $this->response($recent_project, 'All recent projects');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }
 
     /**
     *  @OA\Get(
     *     path="/api/user/dashboard/recent/project",
     *     tags={"Dashboard"},
     *     security={{"bearer_token":{}}},
     *     summary="Get all recent project list user",
     *     operationId="Get all recent Project List user",
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
    public function user_all_recent_projects()
    {
        try {
            $recent_project = project::where('user_id',Auth::id())->with('projectOwner')->latest('created_at')->get();
            return $this->response($recent_project, 'All recent projects');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/user/dashboard/project/process/count",
     *     tags={"Dashboard"},
     *     security={{"bearer_token":{}}},
     *     summary="Get User Project process Count List",
     *     security={{"bearer_token":{}}},
     *     operationId="Get User Project process Count List",
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
    public function user_dashboard_project_process_count()
    {
        try {
            $fd_count = ProjectProcess::where('created_by_user_id',Auth::id())->where('project_status',1)->count();
            $rc_count = ProjectProcess::where('created_by_user_id',Auth::id())->where('project_status',2)->count();
            $fc_count = ProjectProcess::where('created_by_user_id',Auth::id())->where('project_status',3)->count();
            $cf_count = ProjectProcess::where('created_by_user_id',Auth::id())->where('project_status',4)->count();

            $data['Total pending files'] = $fd_count;
            $data['Total pending review'] = $rc_count;
            $data['Total pushed'] = $fc_count;
            $data['Total competed files'] = $cf_count;

            return $this->response([$data], 'User Dashboard Project Process Count');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

    
    /**
     *  @OA\Get(
     *     path="/api/search/project/by/date",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},
     *     summary="Search Project By date",
     *     security={{"bearer_token":{}}},
     *     operationId="Search project by date",
     *
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=true,
     *         description="start date",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=true,
     *         description="end date",
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
    public function search_project_by_date(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $result = Project::whereDate('created_at', '>=', $start_date)
                                ->whereDate('created_at', '<=', $end_date)
                                ->get();
            return $this->response($result, 'Project List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }
 
}
