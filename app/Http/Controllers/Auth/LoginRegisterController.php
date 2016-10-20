<?php

namespace App\Http\Controllers\Auth;

use App\Mail\Confirmation;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class LoginRegisterController extends Controller
{

    use ThrottlesLogins;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showRegisterLoginForm()
    {
        return view('auth.login');
    }

    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }

    public function loginOrRegister(Request $request)
    {

        if ($this->hasTooManyLoginAttempts($request)) {
            return $this->throttleErrorResponse($request);
        }
        $validator = $this->validateRequest($request);
        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }
        $credentials = $request->only($this->username(), 'password');
        if (Auth::guard()->attempt($credentials, $request->has('remember'))) {
            return $this->loginResponse($request);
        }
        if(User::where($this->username(),$request->all()[$this->username()])->count() > 0)
        {
            $this->incrementLoginAttempts($request);
            return new JsonResponse(['errors' => [Lang::get('messages.account_active_fail2')]], 422);
        }
        event(new Registered($user = $this->createNewUser($request->all())));
        //TODO:Send activation email
        $this->sendConfirmation($request, $user);
        Auth::guard()->login($user);
        return new JsonResponse(['url'=> url($this->redirectTo)],200);
    }

    public function createNewUser(array $data){
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'activation_token'=> hash_hmac('sha256', str_random(60), config('app.key'))
        ]);
    }

    /**
     * @param Request $request
     * @param $user
     */
    public function sendConfirmation(Request $request, $user)
    {
        $request->session()->flash('activation_warning', Lang::get('messages.activation_message', ['email' => $user->email]));
        $link = URL::route('user.activate',$user->activation_token);
        if(env('APP_ENV') == 'local')
        {
            Mail::to('thiago.mello@vizad.com.br')->send(new Confirmation($link));
        }else{
            Mail::to($user->email)->send(new Confirmation($link));
        }
    }

    public function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function loginResponse(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);
        return new JsonResponse(['url' => url($this->redirectTo)], 200);
    }

    /**
     * @param Request $request
     * @param $validator
     * @return JsonResponse
     */
    public function validationErrorResponse(Request $request, $validator)
    {
        $this->incrementLoginAttempts($request);
        return new JsonResponse(['errors' => $validator->errors()->all()], 422);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function throttleErrorResponse(Request $request)
    {
        $this->fireLockoutEvent($request);
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );
        $message = Lang::get('auth.throttle', ['seconds' => $seconds]);
        return new JsonResponse(['message' => $message], 429);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }
}
