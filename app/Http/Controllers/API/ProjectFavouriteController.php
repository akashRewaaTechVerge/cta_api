<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectHasOwner;
use App\Models\ProjectHasTeamMember;
use App\Models\ProjectProcess;
use App\Models\ProjectFavourite;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ProjectFavouriteController extends Controller
{
    use ApiTrait;
    
     /**
     *  @OA\Post(
     *     path="/api/project/add/remove/favourite",
     *     tags={"ProjectFavourite"},
     *     security={{"bearer_token":{}}},
     *     summary="Project add remove favourite",
     *     security={{"bearer_token":{}}},
     *     operationId="project add remove favourite",
     *
     *    @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         required=true,
     *         description="project id",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         description="1 - Add favourite | 2 - Remove favourite",
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
    public function project_add_remove_favourite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:1,2',
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        try {
            if($request->status == 1){
                $pro_fav = ProjectFavourite::where('project_id', $request->project_id)->where('user_id', Auth::id())->first();
                if($pro_fav)
                {
                    return $this->response('','Already Added to favourite!');
                }
                $pro_fav = new ProjectFavourite;
                $pro_fav->uuid = Str::uuid()->toString();
                $pro_fav->user_id = Auth::id();
                $pro_fav->project_id = $request->project_id;
                $pro_fav->save();
                return $this->response([], 'Project has been added to Favourite!');
            }
            if($request->status == 2){
                $pro_fav = ProjectFavourite::where('project_id', $request->project_id)->where('user_id', Auth::id())->first();
                if($pro_fav)
                {
                    $pro_fav->delete();
                    return $this->response('','Project has been remove from Favourite');
                }
                return $this->response('','Project has been remove from Favourite');
            }
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

     /**
     *  @OA\Get(
     *     path="/api/project/favourite/list",
     *     tags={"ProjectFavourite"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Project Favourite List",
     *     security={{"bearer_token":{}}},
     *     operationId="Project Favourite List",
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
    public function project_favourite_list()
    {
        try {
            $p_favourite = ProjectFavourite::where('user_id',Auth::id())->orderBy('id', 'desc')->get();
            return $this->response($p_favourite, 'Project Favourite List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false,400);
        }
    }

}
