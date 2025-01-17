<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Password;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PasswordController extends Controller {
    use ResetsPasswords;

    protected $redirectTo;

    public function __construct() {
        $this->middleware('guest', [
            'except' => ['postChange', 'update'],
        ]);
        $this->middleware('localize_auth', [
            'only' => 'postChange'
        ]);
        $this->middleware('localize_guest', [
            'except' => 'postChange'
        ]);
        $this->redirectTo = action('IndexController@getDashboard');
    }

    public function getActivate(Request $request, $token = null) {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        $email = DB::table(config('auth.password.table'))->where('token', $token)->first()->email;
        if (!$request->session()->has('state')) {
            $request->session()->flash('state', 'activate');
        }

        return view('global.auth', ['email' => $email, 'token' => $token]);
    }

    public function getReset(Request $request, $token = null) {
        if (is_null($token)) {
            throw new NotFoundHttpException;
        }

        $email = DB::table(config('auth.password.table'))->where('token', $token)->first()->email;
        if (!$request->session()->has('state')) {
            $request->session()->flash('state', 'reset');
        }

        return view('global.auth', ['email' => $email, 'token' => $token]);
    }

    public function postActivate(Request $request) {
        $this->validatesRequestErrorBag = 'activate';

        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        DB::table(config('auth.password.table'))->where('token', $credentials['token'])->update([
            'created_at' => new Carbon
        ]);

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                DB::table(config('auth.password.table'))->where('token', $credentials['token'])->delete();
                return redirect($this->redirectPath())->with('status', trans('auth.message.success.activated'));
            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)], 'activate');
        }
    }

    public function postEmail(Request $request) {
        $this->validatesRequestErrorBag = 'forgot';

        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function (Message $message, CanResetPassword $user) {
            $message->from($user->company->email ?: $user->company->admin->email, config('insura.name'));
            $message->subject($this->getEmailSubject());
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with(['state' => 'forgot', 'status' => trans($response)]);
            case Password::INVALID_USER:
                return redirect()->back()->with('state', 'forgot')->withErrors(['email' => trans($response)], 'forgot');
        }
    }

    public function postReset(Request $request) {
        $this->validatesRequestErrorBag = 'reset';

        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $this->resetPassword($user, $password);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect($this->redirectPath())->with('status', trans($response));
            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)], 'reset');
        }
    }

    public function update(Request $request) {
        $request->session()->flash('tab', 'security');
        $this->validate($request, [
            'current_password'  => "required",
            'new_password'      => 'confirmed|min:6|required',
        ]);
        $user = $request->user();
        $redirect = redirect()->back();

        if ($request->current_password === $request->new_password) {
            return $redirect->withErrors([
                'new_password' => trans('The new password cannot be the same as the current password.')
            ]);
        }

        if (password_verify($request->current_password, $user->password)) {
            Auth::logout();
            $this->resetPassword($user, $request->new_password);
            $redirect = $redirect->with('success', trans('settings.message.success.password.change'));
        } else {
            $redirect = $redirect->withErrors([
                trans('settings.message.error.password.change')
            ]);
        }

        return $redirect;
    }
}
