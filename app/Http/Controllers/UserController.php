<?php

namespace App\Http\Controllers;

use App\Mail\Confirmation;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * UserController constructor.
     */
    public function __construct()
    {
    }

    public function resendActivation(Request $request){
        try{
            if(Auth::user()->active == 0)
            {
                $link = URL::route('user.activate',Auth::user()->activation_token);
                if(env('APP_ENV') == 'local')
                {
                    Mail::to('thiago.mello@vizad.com.br')->send(new Confirmation($link));
                }else{
                    Mail::to(Auth::user()->email)->send(new Confirmation($link));
                }
                $request->session()->put('activated_success', Lang::get('messages.resend_message',['email'=>Auth::user()->email]));
            }
            $request->session()->put('activation_failed', Lang::get('messages.activated_message2'));
            return redirect('/home');
        }catch (\Exception $e)
        {
            $request->session()->put('activation_failed', $e->getMessage());
            return redirect('/home');
        }


    }

    public function update($id, Request $request)
    {
        $validator = $this->validateUpdateRequest($request->all());
        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->errors()->all()], 422);
        }
        //UPDATE USER
        if(Auth::user()->id == $id && Auth::user()->active == 1)
        {
            $user = User::find($id);
            if($request->hasFile('avatar'))
            {
                $path = $request->file('avatar')->store(
                    'avatars/'.$id, 's3'
                );
                Storage::disk('s3')->setVisibility($path, 'public');
                $user->avatar_url = $path;
            }
            if($request->has('profile_name'))
            {
                $user->name = $request->get('profile_name');
            }
            $user->save();
            return new JsonResponse([], 200);
        }
        return new JsonResponse(['errors' => ['You are not authorized to acess this resource']], 403);


    }

    public function validateUpdateRequest(array $data)
    {
        return Validator::make($data, [
            'profile_name' => 'max:60',
            'avatar' => 'image'
        ]);
    }

    public function activateUser($token, Request $request)
    {
        $user = User::where('activation_token',$token)->first();
        if($user != null)
        {
            if($user->active == 0)
            {
                $user->active = 1;
                $user->save();
                if(Auth::check()){
                    $request->session()->put('activated_success', Lang::get('messages.activated_message',['email'=>$user->email]));
                }else{
                    $request->session()->put('activated_success', Lang::get('messages.activated_message2',['email'=>$user->email]));
                }
                return redirect('/');
            }else{
                $request->session()->put('activation_failed', Lang::get('messages.account_active_fail'));
                return redirect('/');
            }
        }else{
            $request->session()->put('activation_failed', Lang::get('messages.account_fatal_fail'));
            return redirect('/');
        }
    }
}
