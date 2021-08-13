<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Mail;

class UserController extends Controller
{  

    public function userLogin(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $data = $request->all();
        $user = User::where('username', $data['username'])->first();

        if($user) {

            if($user->status != 0) {
        
                $credentials = $request->only('username', 'password');
                if (Auth::attempt($credentials)) {
                    
                    $token = $user->createToken('aman')->plainTextToken;

                    return response()->json(['success'=>'Logged In!', 'user'=>$user, 'token'=>$token]);
                }
                
                return response()->json(['error'=>'Login details are not valid']);

            } else {
                return response()->json(['error'=>'Please verify your email first.']);
            }

        } else {
            return response()->json(['error'=>'User not forund!']);
        }
    }
      

    public function userRegistration(Request $request)
    {  
        $request->validate([
            'username' => 'required|min:4|max:20',
            'password' => 'required|min:6',
        ]);
           
        $data = $request->all();

        

        if(isset($data['name'])) {
            $name = $data['name'];
        } else {
            $name = '';
        }

        if(isset($data['email'])) {
            $email = $data['email'];
        } else {
            $email = '';
        }

        if(isset($data['password'])) {
            $password = $data['password'];
        }

        if(isset($data['username'])) {
            $username = $data['username'];
        }

        if(isset($data['user_role'])) {
            $user_role = $data['user_role'];
        } else {
            $user_role = 0;
        }

        if(isset($data['avatar'])) {
            $avatar = $data['avatar'];
        } else {
            $avatar = '';
        }

        if(isset($data['registered_at'])) {
            $registered_at = $data['registered_at'];
        } else {
            $registered_at = null;
        }

        $email_pin = rand(10,1000000);
        
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'username' => $username,
            'user_role' => $user_role,
            'avatar' => $avatar,
            'registered_at' => $registered_at,
            'email_verify_pin' => $email_pin
        ]);
         
        return response()->json(['success'=>'Check your email and verify.', 'user'=> $user]);

    }    
    

    public function logOut(Request $request) {
        Auth::user()->tokens()->delete();
        Session::flush();
  
        return response()->json(['success'=>'Logout!']);
    }

    public function userInvitation(Request $request)
    {  
        $request->validate([
            'email' => 'required|email'
        ]);
           
        $data = $request->all();

        /*try {
            Mail::raw(['Please signup on this link https::www.example.com'], function($message) {
                 $message->to($data['email'])->subject('Signup Invitation');
            });
        } catch (Exception $ex) {
            $mail_notification = array(
                'mail_message' => 'Something wrong with mail send.',
            );
        }*/
         
        return response()->json(['success'=>'Invitation email sent to '.$data['email']]);

    }

    public function verifyPin(Request $request)
    {  
        
        $data = $request->all();

        $user = $user = User::where([['id', $data['user']], ['email_verify_pin', $data['p']]])->get();

        if(count($user) > 0) {

            $user = User::where('id', $data['user'])
            ->update(['status' => 1,'email_verify_pin' => null,'email_verified_at' => now(),'registered_at' => now()]);

            return response()->json(['success'=>'Email verified! You can login now.', 'user'=> $user]);

        } else {
            return response()->json(['error'=>'Something wrong!']);
        }

    }

    public function userUpdateProfile(Request $request)
    {  

        $data = $request->all();
        //var_dump($request->user());
        $user_id = auth()->id();
        if(Auth::check()) {

            if(isset($data['name'])) {
                $name = $data['name'];
                User::where('id', $user_id)->update(['name'=>$name]);
            }

            if(isset($data['email'])) {
                $email = $data['email'];
                User::where('id', $user_id)->update(['email'=>$email]);
            }

            if(isset($data['avatar'])) {
                $avatar = $data['avatar'];
                User::where('id', $user_id)->update(['avatar'=>$avatar]);
            }

            return response()->json(['success'=>'Updated profile']);
        } else {
            return response()->json(['error'=>'Something wrong!']);
        }

    }
}
