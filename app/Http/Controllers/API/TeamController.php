<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamHasMember;
use App\Models\TeamMember;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/create/team/member",
     *     tags={"Team"},
     *     summary="Create Team Member",
     *     security={{"bearer_token":{}}},
     *     operationId="create team member",
     *
     *     @OA\Parameter(
     *         name="first_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="last_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="phone",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         required=true,
     *         description="1 - Male | 2 - female",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="country_code",
     *         in="query",
     *         required=true,
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
    public function create_team_member(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:team_members',
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'phone' => 'required',
            'gender' => 'required|in:1,2',
            'country_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $team_m = new TeamMember;
            $team_m->uuid = Str::uuid()->toString();
            $team_m->email = $request->email;
            $team_m->first_name = $request->first_name;
            $team_m->last_name = $request->last_name;
            $team_m->gender = $request->gender;
            $team_m->country_code = $request->country_code;
            $team_m->phone = $request->phone;
            $team_m->save();
            return $this->response($team_m, 'Team member has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

    /**
     *  @OA\Post(
     *     path="/api/edit/team/member",
     *     tags={"Team"},
     *     summary="Edit Team Member",
     *     security={{"bearer_token":{}}},
     *     operationId="edit team member",
     *
     *     @OA\Parameter(
     *         name="team_member_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="first_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="last_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="phone",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="gender",
     *         required=true,
     *         description="1 - Male | 2 - female",
     *         in="query",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="country_code",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="status",
     *         required=true,
     *         description="1 - active | 2 - deactive",
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
    public function edit_team_member(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_member_id' => 'required|exists:team_members,id',
            'email' => 'required|email',
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'phone' => 'required',
            'status' => 'required|in:1,2',
            'gender' => 'required|in:1,2',
            'country_code' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $team_m = TeamMember::find($request->team_member_id);
            $team_m->email = $request->email;
            $team_m->first_name = $request->first_name;
            $team_m->last_name = $request->last_name;
            $team_m->gender = $request->gender;
            $team_m->country_code = $request->country_code;
            $team_m->phone = $request->phone;
            $team_m->status = $request->status;
            $team_m->save();
            return $this->response($team_m, 'Team member has been updated!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

    /**
     *  @OA\Post(
     *     path="/api/team/member/status",
     *     tags={"Team"},
     *     security={{"bearer_token":{}}},
     *     summary="Team member status active deactive",
     *     security={{"bearer_token":{}}},
     *     operationId="team member status",
     *
     *    @OA\Parameter(
     *         name="team_member_id",
     *         in="query",
     *         required=true,
     *            description="team member id",
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
    public function team_member_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:1,2',
            'team_member_id' => 'required|exists:team_members,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }
        try {
            $tm_status = TeamMember::find($request->team_member_id);
            $tm_status->status = $request->status;
            $tm_status->save();
            if ($request->status == 1) {
                $msg = 'Status Active successfully!';
            } else {
                $msg = 'Status Deactive successfully!';
            }
            return $this->response([], $msg);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/team/member/list",
     *     tags={"Team"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Team Memeber List",
     *     security={{"bearer_token":{}}},
     *     operationId="team member list",
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
    public function get_team_members()
    {
        try {
            $tm = TeamMember::where('status', 1)->get();
            return $this->response($tm, 'Team Members List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/delete/team/member",
     *     tags={"Team"},
     *     summary="Team member delete",
     *     security={{"bearer_token":{}}},
     *     operationId="team member delete",
     *
     *     @OA\Parameter(
     *         name="team_member_id",
     *         in="query",
     *         required=true,
     *            description="team member id",
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
    public function delete_team_member(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_member_id' => 'required|exists:team_members,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }

        try {
            $tm_delete = TeamMember::find($request->team_member_id);
            if ($tm_delete) {
                $tm_delete->delete();
                return $this->response([], 'Team member deleted successfully!');
            }
            return $this->response([], 'Enter valid team member id!.', false, 400);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/create/team",
     *     tags={"Team"},
     *     summary="Create Team",
     *     security={{"bearer_token":{}}},
     *     operationId="create team",
     *
     *     @OA\Parameter(
     *         name="name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="team_member_id",
     *         in="query",
     *         description="value pass like 1,2",
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
    public function create_team(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_member_id' => 'required|exists:team_members,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $team = new Team;
            $team->uuid = Str::uuid()->toString();
            $team->name = $request->name;
            $team->save();

            if (!empty($request->team_member_id)) {
                $team_member_id = explode(",", $request->team_member_id);
                foreach ($team_member_id as $tm) {
                    $team_m = new TeamHasMember;
                    $team_m->team_id = $team->id;
                    $team_m->team_member_id = $tm;
                    $team_m->save();
                }
            } else {
                return $this->response([], "Team member id required!", false, 404);
            }

            return $this->response($team, 'Team has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

     /**
     *  @OA\Get(
     *     path="/api/team/list",
     *     tags={"Team"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Team List",
     *     security={{"bearer_token":{}}},
     *     operationId="team list",
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
    public function teams_list()
    {
        try {
            $team_list = Team::with('TeamMembers')->where('status', 1)->OrderBy('id','desc')->get();
            return $this->response($team_list, 'Teams List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

     /**
     *  @OA\Post(
     *     path="/api/delete/team",
     *     tags={"Team"},
     *     summary="Team delete",
     *     security={{"bearer_token":{}}},
     *     operationId="team delete",
     *
     *     @OA\Parameter(
     *         name="team_id",
     *         in="query",
     *         required=true,
     *            description="team id",
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
    public function delete_team(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 404);
        }

        try {
            $team_delete = Team::find($request->team_id);
            if ($team_delete) {
                $team_delete->delete();
                return $this->response([], 'Team deleted successfully!');
            }
            return $this->response([], 'Enter valid team id!.', false, 400);
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    
}
