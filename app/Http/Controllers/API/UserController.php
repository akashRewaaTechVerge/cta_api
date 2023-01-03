<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Follower;
use App\Models\Country;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Auth;
use Hash;
use DB;
use Mail;
use Illuminate\Support\Str;


/**
* @OA\Info(
*      description="",
*     version="1.0.0",
*      title="CTA",
* )
**/
 
/**
*  @OA\SecurityScheme(
*     securityScheme="bearer_token",
*         type="http",
*         scheme="bearer",
*     ),
**/
class UserController extends Controller
{


    use ApiTrait;

    /**
     *  @OA\Post(
     *     path="/api/register",
     *     tags={"User"},
     *     summary="Create Account",
     *     operationId="create account",
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
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
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
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|unique:users',
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'phone' => 'required',
            'password' => 'required|min:8',
            'country_code' => 'required',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try{
            $user = new User();
            $user->uuid = Str::uuid()->toString();
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->password = bcrypt($request->password);
            $user->country_code = $request->country_code;
            $user->phone = $request->phone;
            $user->ip_address = \Request::ip();
            $user->save();
            return $this->response([],'Registered Successully!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
        }

    }
    /**
     *  @OA\Post(
     *     path="/api/login",
     *     tags={"User"},
     *     summary="Login",
     *     security={{"bearer_token":{}}},
     *     operationId="login",
     * 
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * *     @OA\Parameter(
     *         name="phone",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="password",
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
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required_without:phone|email',
            'password' => 'required',
            'phone' => 'required_without:email'
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try{
            $user = User::where('email',$request->email)->orWhere('phone',$request->phone)->first();
            if($user){
                if(Hash::check($request->password,$user->password)){
                    $user->device_token = $request->device_token;
                    $user->tokens()->delete();
                    $token = $user->createToken('API')->accessToken;
                    $user['token'] = $token;
                    return $this->response($user,'Login Successully!');
                }else{
                    return $this->response([], 'Enter valid password!', false,400); 
                }   
            }
            return $this->response([], 'Please enter valid credentials', false,400); 

        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
        }

    }
    /**
     *  @OA\Get(
     *     path="/api/profile",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Get User Profile",
     *     operationId="profile",
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
    public function me()
    {
        try{
            $user = User::find(Auth::id());
            return $this->response($user, 'Profile!'); 
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
        }

    }
    /**
     *  @OA\Post(
     *     path="/api/profile/edit",
     *     tags={"User"},
     *     summary="Edit Profile",
     *     security={{"bearer_token":{}}},
     *     operationId="edit-profile",
     * 
     *     @OA\Parameter(
     *         name="first_name",
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="last_name",
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
    public function edit_profile(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
        ]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false,401);
        }

        try{
            $user = User::find(Auth::id());
            if($user){
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->save();
                return $this->response($user, 'Your profile has been updated');
            }
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
        }
    }
   
    /**
     *  @OA\Get(
     *     path="/api/logout",
     *     tags={"User"},
     *     security={{"bearer_token":{}}},  
     *     summary="Logout",
     *     security={{"bearer_token":{}}},
     *     operationId="Logout",
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
    public function logout()
    {
        try{
            $user = User::find(Auth::id());
            $user->tokens()->delete();
            return $this->response('','Logout Successfully!');
        }catch(Exception $e){
            return $this->response([], $e->getMessage(), false,400);
        }
       
    }
   /** 
     *  @OA\Post(
     *     path="/api/change/password",
     *     tags={"User"},
     *     summary="Change Password",
     *     security={{"bearer_token":{}}},
     *     operationId="change-password",
     * 
     *     @OA\Parameter(
     *         name="old_password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *     @OA\Parameter(
     *         name="password",
     *         required=true,
     *         in="query",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     * 
     *    @OA\Parameter(
     *         name="new_confirm_password",
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
   public function change_password(Request $request)
   {
    $validator = Validator::make($request->all(),[
        'old_password' => 'required',
        'password' => 'required|min:8',
        'new_confirm_password' => 'required|same:password'
    ]);

    if($validator->fails())
    {
        return $this->response([], $validator->errors()->first(), false,401);
    }

    try{
        $user = User::find(Auth::id());
        if($user){
            if(Hash::check($request->old_password,$user->password)){
                $user->password =  bcrypt($request->password);
                $user->save();
                return $this->response('','Password changed Successully!');
            }else{
                return $this->response([], 'Not matched current password!', false,400); 
            }   
        }
        return $this->response([], 'Enter Valid user name', false,400); 

    }catch(Exception $e){
        return $this->response([], $e->getMessage(), false,400);
    }
   }
   /**
	 *  @OA\Post(
	 *     path="/api/forgot/password",
	 *     tags={"User"},
	 *     summary="Forgot password",
	 *     operationId="forgot-password",
	 * 
	 *     @OA\Parameter(
	 *         name="email",
	 *         in="query",
	 *         required=true,
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
	 * )
	**/
	public function forgot_password(Request $request)
	{
		
		$validator = Validator::make($request->all(),[
			'email' => 'required|email|exists:users,email',
		]);

        if($validator->fails())
        {
            return $this->response([], $validator->errors()->first(), false,401);
        }
		$user = User::where('email',$request->email)->first();
        if(empty($user))
        {
            return $this->sendError('This email not registered'); 
        }

        try{
            $newPass = substr(md5(time()), 0, 10);
            $user->password = bcrypt($newPass);
            $user->save();
            $data = [
                'first_name' => $user->first_name,
                'password' => $newPass
            ];
            $email = $user->email;
            Mail::send('mail.forgot', $data, function($message) use ($email) {
                $message->to($email, 'test')->subject
                   ('Forgot Password');
            });
            return $this->response('','Email sent succesfully!');

        } catch (Exception $e)
        {
            return $this->response([], $e->getMessage(), false,400);
        }      
		
    }
    
    

}
