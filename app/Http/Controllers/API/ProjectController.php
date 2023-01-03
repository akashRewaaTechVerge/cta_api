<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectHasDocument;
use App\Models\ProjectHasOwner;
use App\Models\ProjectHasTeamMember;
use App\Models\ProjectProcess;
use App\Http\Resources\ProjectResource;
use App\Traits\ApiTrait;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class ProjectController extends Controller
{
    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/project/create",
     *     tags={"Project"},
     *     summary="Create Project",
     *     security={{"bearer_token":{}}},
     *     operationId="create project",
     *
     *     @OA\Parameter(
     *         name="company_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="company_address",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="formation_type",
     *         required=true,
     *         in="query",
     *         description="1 - US Jurisdiction | 2 - Foreign Formation",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="state_of_formation",
     *         in="query",
     *         description="If formation type 1 then enter state of formation",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="tin_ein_number",
     *         in="query",
     *         description="If formation type 1 then enter tin ein number",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="foreign_based_company_us",
     *         in="query",
     *         description="If formation type 1 then enter foreign base company",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="company_registration_number_or_code",
     *         in="query",
     *         description="If formation type 1 then enter reg num or code",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),

     *    @OA\Parameter(
     *         name="country_of_formation",
     *         in="query",
     *         description="If formation type 2 then enter country of formation",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="foreign_state_of_formation",
     *         in="query",
     *         description="If formation type 2 then enter foreign state of formation",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="foreign_tin_ein_number",
     *         in="query",
     *         description="If formation type 2 then enter foreign tin ein num",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="foreign_based_company",
     *         in="query",
     *         description="If formation type 2 then enter foreign based company",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="foreign_company_registration_number_or_code",
     *         in="query",
     *         description="If formation type 2 then enter foreign reg num or code",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="unique_type",
     *         required=true,
     *         in="query",
     *         description="1 - US Jurisdiction | 2 - Foreign Formation",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="owner_name",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="dob",
     *         required=true,
     *         in="query",
     *         description="yyyy-mm-dd",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="address",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *  *    @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="document[]",
     *                      description="document",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                   ),
     *                   @OA\Property(
     *                      property="license_and_passport[]",
     *                      description="license and passport",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                   ),
     *                  @OA\Property(
     *                      property="passport[]",
     *                      description="passport",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                   ),
     *               ),
     *           ),
     *       ),
     *     @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *
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
    public function create_project(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_address' => 'required',
            'company_name' => 'required|max:255',
            'formation_type' => 'required|in:1,2',
            'state_of_formation' => 'required_if:formation_type,==,1',
            'tin_ein_number' => 'required_if:formation_type,==,1|min:9|max:11',
            'foreign_based_company_us' => 'required_if:formation_type,==,1',
            'company_registration_number_or_code' => 'required_if:formation_type,==,1|min:9',
            'country_of_formation' => 'required_if:formation_type,==,2',
            'foreign_state_of_formation' => 'required_if:formation_type,==,2',
            'foreign_tin_ein_number' => 'required_if:formation_type,==,2|min:9|max:11',
            'foreign_based_company' => 'required_if:formation_type,==,2',
            'document' => 'required_if:formation_type,==,2',
            'foreign_company_registration_number_or_code' => 'required_if:formation_type,==,2|min:9',
            'unique_type' => 'required|in:1,2',
            'owner_name' => 'required',
            'dob' => 'required',
            'address' => 'required',
            'license_and_passport' => 'required_if:unique_type,==,1',
            'passport' => 'required_if:unique_type,==,2',

        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
             $d_filename = null;
            // if ($request->hasFile('document')) {
            //     $file = $request->file('document');
            //     $d_filename = time() . $file->getClientOriginalName();
            //     $file->move(public_path() . '/projects/', $d_filename);
            // }
             $lp_filename = null;
            // if ($request->hasFile('license_and_passport')) {
            //     $file = $request->file('license_and_passport');
            //     $lp_filename = time() . $file->getClientOriginalName();
            //     $file->move(public_path() . '/projects/', $lp_filename);
            // }
             $p_filename = null;
            // if ($request->hasFile('passport')) {
            //     $file = $request->file('passport');
            //     $p_filename = time() . $file->getClientOriginalName();
            //     $file->move(public_path() . '/projects/', $p_filename);
            // }

            $project = new Project;
            $project->uuid = Str::uuid()->toString();
            $project->created_by = Auth::id();
            $project->company_name = $request->company_name;
            $project->company_address = $request->company_address;
            $project->formation_type = $request->formation_type;

            if ($project->formation_type == 1) {
                $project->state_of_formation = $request->state_of_formation;
                $project->tin_ein_number = $request->tin_ein_number;
                $project->foreign_based_company_us = $request->foreign_based_company_us;
                $project->company_registration_number_or_code = $request->company_registration_number_or_code;

            }
            if ($project->formation_type == 2) {
                $project->country_of_formation = $request->country_of_formation;
                $project->foreign_state_of_formation = $request->foreign_state_of_formation;
                $project->foreign_tin_ein_number = $request->foreign_tin_ein_number;
                $project->foreign_based_company = $request->foreign_based_company;
                $project->foreign_company_registration_number_or_code = $request->foreign_company_registration_number_or_code;

            }
            $project->save();
            if ($request->hasfile('document')) {
                foreach ($request->file('document') as $file) {
                    $d_filename = time() . $file->getClientOriginalName();
                    $file->move(public_path() . '/document/', $d_filename);
                    $pm = new ProjectHasDocument;
                    $pm->uuid = Str::uuid()->toString();
                    $pm->user_id = Auth::id();
                    $pm->project_id = $project->id;
                    $pm->document = $d_filename;
                    $pm->type = 'document';
                    $pm->save();
                }
            }
            // if ($request->hasFile('document')) {
            //     $project->document = $d_filename;
            // }
            if ($project->save()) {

                $pro_own = new ProjectHasOwner;
                $pro_own->uuid = Str::uuid()->toString();
                $pro_own->project_id = $project->id;
                $pro_own->owner_name = $request->owner_name;
                $pro_own->dob = $request->dob;
                $pro_own->address = $request->address;
                $pro_own->unique_type = $request->unique_type;

                // if ($pro_own->unique_type == 1) {
                //     if ($request->hasFile('license_and_passport')) {
                //         $pro_own->license_and_passport = $lp_filename;
                //     }
                // }
                // if ($pro_own->unique_type == 2) {
                //     if ($request->hasFile('passport')) {
                //         $pro_own->passport = $p_filename;
                //     }
                // }
                $pro_own->save();
                if ($pro_own->unique_type == 1) {
                    if ($request->hasfile('license_and_passport')) {
                        foreach ($request->file('license_and_passport') as $file) {
                            $lp_filename = time() . $file->getClientOriginalName();
                            $file->move(public_path() . '/license_and_passport/', $lp_filename);
                            $pm = new ProjectHasDocument;
                            $pm->user_id = Auth::id();
                            $pm->uuid = Str::uuid()->toString();
                            $pm->project_id = $project->id;
                            $pm->license_and_passport = $lp_filename;
                            $pm->type = 'license_and_passport';
                            $pm->save();
                        }
                    }
                }
                if ($pro_own->unique_type == 2) {
                    if ($request->hasfile('passport')) {
                        foreach ($request->file('passport') as $file) {
                            $p_filename = time() . $file->getClientOriginalName();
                            $file->move(public_path() . '/passport/', $p_filename);
                            $pm = new ProjectHasDocument;
                            $pm->user_id = Auth::id();
                            $pm->uuid = Str::uuid()->toString();
                            $pm->project_id = $project->id;
                            $pm->passport = $p_filename;
                            $pm->type = 'passport';
                            $pm->save();
                        }
                    }
                }
            }
            $data =  new ProjectResource($project);
            return $this->response($data , 'Project has been added!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

    /**
     *  @OA\Get(
     *     path="/api/project/list",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Project List",
     *     security={{"bearer_token":{}}},
     *     operationId="Project",
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
    public function get_project()
    {
        try {
            $project = Project::with(['projectOwner','document'])->get();
            $data =  ProjectResource::collection($project);
            return $this->response($project, 'Project List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/project/list/with/createdby",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Project List With Created By",
     *     security={{"bearer_token":{}}},
     *     operationId="Project List With Created By",
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
    public function get_project_with_created_by()
    {
        try {
            $p_created_by = Project::with(['projectOwner', 'createdBy','document'])->get();
            return $this->response($p_created_by, 'Project List With Created By');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Get(
     *     path="/api/get/perticular/project",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},
     *     summary="Get Perticular Project By Id",
     *     security={{"bearer_token":{}}},
     *     operationId="Get perticular project by id",
     *
     *     @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         required=true,
     *            description="project id",
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
    public function get_perticular_project(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $project = Project::where('id', $request->project_id)->with(['projectOwner', 'createdBy','document'])->get();
            return $this->response($project, 'Perticular Project List');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }

    /**
     *  @OA\Post(
     *     path="/api/project/assign/member",
     *     tags={"Project"},
     *     summary="Create Project Assign Member",
     *     security={{"bearer_token":{}}},
     *     operationId="create project assign member",
     *
     *     @OA\Parameter(
     *         name="project_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="team_member_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="1 - Approved | 2 - Disapproved",
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
    public function project_assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'team_member_id' => 'required|exists:team_members,id',
            'status' => 'nullable|in:1,2',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $p_assign = new ProjectHasTeamMember;
            $p_assign->project_id = $request->project_id;
            $p_assign->team_member_id = $request->team_member_id;
            $p_assign->status = $request->status;
            $p_assign->save();
            return $this->response($p_assign, 'Project has been assign to team member!');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

    /**
     *  @OA\Post(
     *     path="/api/project/process",
     *     tags={"Project"},
     *     summary="Create Project process",
     *     security={{"bearer_token":{}}},
     *     operationId="create project process",
     *
     *     @OA\Parameter(
     *         name="project_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="created_by_user_id",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="project_status",
     *         in="query",
     *         required=true,
     *         description="1 - File Details | 2 - Review by cta | 3 - Send to fincen | 4 - Cta filled",
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
    public function project_process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'project_status' => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false, 401);
        }

        try {
            $project_process = new ProjectProcess;
            $project_process->uuid = Str::uuid()->toString();
            $project_process->updated_by = Auth::id();
            $project_process->project_id = $request->project_id;
            $project_process->created_by_user_id = $request->created_by_user_id;
            $project_process->project_status = $request->project_status;
            $project_process->save();
            return $this->response($project_process, 'Project has been updated to next process');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }

    }

    /**
     *  @OA\Get(
     *     path="/api/user/project/updated/process/list",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},
     *     summary="Get user Project updated process List",
     *     security={{"bearer_token":{}}},
     *     operationId="Get user Project updated process List",
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
    public function user_project_updated_process()
    {
        try {
            $user_project = Project::where('created_by', Auth::id())->where('status', 1)->pluck('id');
            $fd_project = ProjectProcess::where('created_by_user_id', Auth::id())
                ->whereIn('project_id', $user_project)
                ->whereIn('project_status', [1])
                ->orderBy('updated_at', 'desc')
                ->get();

            $rc_project = ProjectProcess::where('created_by_user_id', Auth::id())
                ->whereIn('project_id', $user_project)
                ->whereIn('project_status', [2])
                ->orderBy('updated_at', 'desc')
                ->get();
            $fc_project = ProjectProcess::where('created_by_user_id', Auth::id())
                ->whereIn('project_id', $user_project)
                ->whereIn('project_status', [3])
                ->orderBy('updated_at', 'desc')
                ->get();
            $cf_project = ProjectProcess::where('created_by_user_id', Auth::id())
                ->whereIn('project_id', $user_project)
                ->whereIn('project_status', [4])
                ->orderBy('updated_at', 'desc')
                ->get();
            $data = [
                'Files details' => $fd_project,
                'Review By CTA' => $rc_project,
                'Send to FinCen' => $fc_project,
                'CTA Filled' => $cf_project,
            ];

            return $this->response($data, 'User Project Process updated list');
        } catch (Exception $e) {
            return $this->response([], $e->getMessage(), false, 400);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/project/search/list",
     *     tags={"Project"},
     *     security={{"bearer_token":{}}},  
     *     summary="project id with user id search",
     *     operationId="project id with user id search",
     *     
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="start date",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="end date",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="created_by",
     *         in="query",
     *         description="Search by user id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *    @OA\Parameter(
     *         name="project_id",
     *         in="query",
     *         description="Search by project id",
     *         @OA\Schema(
     *             type="integer"
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
    public function project_search_list(Request $request){

        $validator = Validator::make($request->all(),[
            'project_id' => 'nullable|exists:projects,id',
            'created_by' => 'nullable|exists:projects,created_by',
        ]);

        if ($validator->fails()) {
            return $this->response([], $validator->errors()->first(), false,400);
        }
		try{
                $project = Project::where('status',1)->with(['projectOwner', 'createdBy','document'])->orderBy('id','desc');
                $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);
                $result = Project::whereDate('created_at', '>=', $start_date)
                                    ->whereDate('created_at', '<=', $end_date);
                                

                if($request->created_by != null){
                    $project =  $project->where('created_by','LIKE','%'.$request->created_by.'%');
                }
                if($request->project_id != null){
                    $project =  $project->where('id','LIKE','%'.$request->project_id.'%');
                }
                $project = $project->get();
				return $this->response($project,'Projects Search List');	
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,404);
        }
    }

}
