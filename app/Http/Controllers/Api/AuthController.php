<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only('name', 'email', 'password');

        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 200);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return response()->json([
                'error' => true,
                'message' => 'Could not create token.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'error' => true,
                'message' => 'Sorry, user cannot be logged out'
            ], 500);
        }
    }

    public function get_user(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }

    public function forget(Request $request){

        $request->validate(['email' => 'required|email']);
        $email = $request->only('email');
        $status = Password::sendResetLink( $email, function ($user , $token){
            echo $token;
            Mail::to($user->email)->send(new ResetPassword($token , $user->email));
        });

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : response()->json(['email' => __($status)]);
    }

    public function reset(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['status', __($status)])
            : response()->json(['email' => [__($status)]]);
    }

    public function updatePassword(Request $request)
    {
        $validated = Validator::make($request->all() ,[
            'password' => 'required|confirmed|min:6',
            'current_password' => 'required'
        ]);

        if ($validated->fails()) {
            return \response()->json($validated->errors()->all());
        }
        try {
            $user = auth()->user();
            $test = Hash::check($validated->validated()['current_password'], $user->password);
            if(!$test){
                return \response()->json(['msg' => 'Current Password Not Correct']);
            }else{
                $credintial = $validated->validated();
                $credintial['password'] = bcrypt($credintial['password']);
                $user->update($credintial);
                return \response()->json(['msg' => 'Password Updated Successfully']);
            }
        }catch (\Exception $e){
            return \response()->json(['msg' => $e->getMessage()]);
        }
    }
}
