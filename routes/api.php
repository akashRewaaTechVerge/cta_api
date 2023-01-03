<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\PlanController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\ErrorController;
use App\Http\Controllers\API\projectController;
use App\Http\Controllers\API\ProjectFavouriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('username/check', [UserController::class, 'check_username']);
Route::post('forgot/password', [UserController::class,'forgot_password']);
Route::group(['middleware' => ['auth:api']], function () {
    // profile
    Route::get('profile', [UserController::class, 'me']);
    Route::post('profile/edit', [UserController::class, 'edit_profile']);
    Route::post('change/password', [UserController::class, 'change_password']);
    
    //plan
    Route::post('plan/duration', [PlanController::class, 'plan_duration']);
    Route::post('plan/duration/edit', [PlanController::class, 'plan_duration_edit']);
    Route::post('plan/duration/delete', [PlanController::class, 'plan_duration_delete']);
    Route::get('plan/duration/list', [PlanController::class, 'list_plan_durations']);
    Route::post('plan', [PlanController::class, 'plan']);
    Route::get('plan/list', [PlanController::class, 'get_plans']);
    Route::post('plan/delete', [PlanController::class, 'delete_plan']);
    Route::post('plan/edit', [PlanController::class, 'edit_plan']);
    Route::post('plan/status', [PlanController::class, 'plan_status']);
    Route::post('plan/has/user', [PlanController::class, 'plan_has_user']);
    Route::get('plan/active/user/count/list', [PlanController::class, 'plan_active_user_lists']);
    Route::get('plan/has/user/details', [PlanController::class, 'plan_has_user_details']);


    //notification
    Route::post('notification/duration', [NotificationController::class, 'notification_duration']);
    Route::post('notification/duration/edit', [NotificationController::class, 'notification_duration_edit']);
    Route::post('notification/duration/delete', [NotificationController::class, 'notification_duration_delete']);
    Route::get('notification/duration/list', [NotificationController::class, 'list_notification_durations']);
    Route::post('notification/create', [NotificationController::class, 'notification_create']);
    Route::get('notification/list', [NotificationController::class, 'list_notifications']);

    //team
    Route::post('create/team/member', [TeamController::class, 'create_team_member']);
    Route::post('edit/team/member', [TeamController::class, 'edit_team_member']);
    Route::post('team/member/status', [TeamController::class, 'team_member_status']);
    Route::post('delete/team/member', [TeamController::class, 'delete_team_member']);
    Route::get('team/member/list', [TeamController::class, 'get_team_members']);
    Route::post('create/team', [TeamController::class, 'create_team']);
    Route::get('team/list', [TeamController::class, 'teams_list']);
    Route::post('delete/team', [TeamController::class, 'delete_team']);
    Route::post('edit/team', [TeamController::class, 'edit_team']);

    //reason
    Route::post('reason/create', [ErrorController::class, 'reason_create']);
    Route::post('reason/edit', [ErrorController::class, 'reason_edit']);
    Route::post('reason/delete', [ErrorController::class, 'reason_delete']);
    Route::get('reason/list', [ErrorController::class, 'reason_lists']);

    Route::post('error/create', [ErrorController::class, 'error_create']);
    Route::post('error/status', [ErrorController::class, 'error_status']);
    Route::get('error/list', [ErrorController::class, 'error_lists']);

    //project
    Route::post('project/create', [ProjectController::class, 'create_project']);
    Route::get('project/list', [ProjectController::class, 'get_project']);
    Route::get('project/list/with/createdby', [ProjectController::class, 'get_project_with_created_by']);
    Route::get('get/perticular/project', [ProjectController::class, 'get_perticular_project']);
    Route::post('project/assign/member', [ProjectController::class, 'project_assign']);
    Route::post('project/process', [ProjectController::class, 'project_process']);
    Route::get('user/project/updated/process/list', [ProjectController::class, 'user_project_updated_process']);
    Route::get('project/search/list', [ProjectController::class, 'project_search_list']);

    //project favourite
    Route::post('project/add/remove/favourite', [ProjectFavouriteController::class, 'project_add_remove_favourite']);
    Route::get('project/favourite/list', [ProjectFavouriteController::class, 'project_favourite_list']);

    //dashboard
    Route::get('admin/dashboard/project/process/count', [HomeController::class, 'admin_dashboard_project_process_count']);
    Route::get('admin/dashboard/recent/project', [HomeController::class, 'admin_all_recent_projects']);
    Route::get('user/dashboard/recent/project', [HomeController::class, 'user_all_recent_projects']);
    Route::get('user/dashboard/project/process/count', [HomeController::class, 'user_dashboard_project_process_count']);
    Route::get('search/project/by/date', [HomeController::class, 'search_project_by_date']);

    //logout
    Route::get('logout', [UserController::class, 'logout']);
});
