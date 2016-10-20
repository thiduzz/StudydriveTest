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

/**
 * This Controller is responsible for registering and signing in users and guaranteeing the security of the process.
 * Besides the logout method, all the methods in this controller are implementing the "guest" middleware - since this functionalities are used only for visitors, not logged in users.
 *
 * Class LoginRegisterController
 * @package App\Http\Controllers\Auth
 * @author  Thiago Mello
 * @see     \Illuminate\Foundation\Auth\ThrottlesLogins
 * @see     \App\Http\Controllers\Controller
 * @see     \App\Http\Middleware\RedirectIfAuthenticated
 * @since   1.0
 */
class LoginRegisterController extends Controller
{

    /**
     * Use the ThrottlesLogins trait which facilitates the control of throttling in the register/login functionality
     */
    use ThrottlesLogins;

    protected $redirectTo = '/home';

    /**
     * LoginRegisterController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Show the login view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegisterLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Receive the logout request and flush all the data stored in the session before redirecting for the visitors page
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        Auth::guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/');
    }

    /**
     * This method is responsible for receiving the login/register request (in the current app, this is happening via AJAX Request). First it is checked if the user has reached the ThrottlesLogins threshold, if so - an error happens. After that, the request data is validated according to the requirements of the project Philipp sent me. Then the attempt for login happens, if it fails, it will automatically create a user with the provided data.
     * @param Request $request
     * @see \App\Http\Controllers\Auth\LoginRegisterController::createNewUser
     * @return JsonResponse
     */
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
        $this->sendConfirmation($request, $user);
        Auth::guard()->login($user);
        return new JsonResponse(['url'=> url($this->redirectTo)],200);
    }

    /**
     * Helper method for the loginOrRegister method described above.
     * To guarantee the uniqueness of the activation_token it is being used a hash_hmac function with random string and the App Key as a salt.
     * @param array $data
     * @see \App\Http\Controllers\Auth\LoginRegisterController::loginOrRegister
     * @return static
     */
    public function createNewUser(array $data){
        return User::create([
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'activation_token'=> hash_hmac('sha256', str_random(60), config('app.key'))
        ]);
    }

    /**
     * Helper method responsible for sending the initial confirmation email.
     * @param Request $request
     * @param $user (\App\User)
     * @see \App\User
     * @see \App\Mail\Confirmation
     * @see \Illuminate\Support\Facades\Mail
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

    /**
     * Helper method to validate the data request (according to the requirements of the project)
     * @param Request $request
     * @return mixed
     */
    public function validateRequest(Request $request)
    {
        return Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);
    }

    /**
     * Helper method for successfull responses
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
     * Helper method for error responses
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
     * Override function of the ThrottlesLogins trait allowing to respond the request with a Json
     * @param Request $request
     * @return JsonResponse
     * @see \Illuminate\Foundation\Auth\ThrottlesLogins
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
