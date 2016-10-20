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
use JavaScript;

/**
 * This Controller is responsible for receiving requests for the logged in user (with the exception of the method activateUser.
 * Once the user logged in, he can only resend the activation email for his account and logout but once his account is active, he can also perform profile update.
 *
 * @author  Thiago Mello
 * @see     \App\Http\Controllers\Controller
 * @since   1.0
 */
class UserController extends Controller
{
    /**
     * Show the logged in screen
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        JavaScript::put([
            'user' => Auth::user(),
        ]);
        return view('home');
    }

    /**
     * This method is just a simple implementation that allow not-confirmed users to request for a new confirmation email.
     * Since sending emails is a heavy task which is not being queued in a remote queue (not in the scope of the project), this method is also implementing Throttling
     * to avoid abusive ammount of request which could lead up to a lack of memory in the server. If this method is accessed 3 times the user is blocked for 5 minutes
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @see \App\Mail\Confirmation
     * @see \Illuminate\Support\Facades\Mail
     * @see \Illuminate\Routing\Middleware\ThrottleRequests
     * @middleware \Illuminate\Auth\Middleware\Authenticate
     */
    public function resendActivation(Request $request){
        try{
            if(Auth::user()->active == 0)
            {
                $link = URL::route('user.activate',Auth::user()->activation_token);
                if(env('APP_ENV') == 'local')
                {
                    Mail::to(env('MAIL_USERNAME','thiago.mello@vizad.com.br'))->send(new Confirmation($link));
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

    /**
     * This is the method responsible for receiving the user account data only for a proof of concept - the only data available for change is the user avatar and the user name. Since both of these informations are optionals - they are not validated to be required. As an additional validation, it is checked if the user is active and if the requested account to be changed is actually the current user account. For this application, this method is only called in a AJAX request waiting for a JSON Response.
     * @param $id
     * @param Request $request
     * @return JsonResponse
     * @see validateUpdateRequest
     * @middleware \Illuminate\Auth\Middleware\Authenticate
     */
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

    /**
     * This is a helper method for the update method described above
     * @param array $data
     * @return \Illuminate\Validation\Factory
     */
    public function validateUpdateRequest(array $data)
    {
        return Validator::make($data, [
            'profile_name' => 'max:60',
            'avatar' => 'image'
        ]);
    }

    /**
     * This method is responsible for receiving the GET request when the user click on the activation button in the activation email.
     * Basically we check if the current token exists and if it does, we activate the user. If the user is already active we throw an error in the session.
     * @param $token
     * @param Request $request
     * @middleware none
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
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
