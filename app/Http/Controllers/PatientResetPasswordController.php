<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password; // Pastikan ini ada
use Illuminate\Auth\Events\PasswordReset; // Tambahkan ini
use Illuminate\Validation\ValidationException; // Tambahkan ini
use Illuminate\Support\Facades\Hash; // Tambahkan ini
use Illuminate\Support\Str; // Tambahkan ini

class PatientResetPasswordController extends Controller
{
    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/patient/dashboard'; // Sesuaikan dengan halaman setelah reset password berhasil

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, redirect the user back to the password request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.patient-reset-password', [
            'token' => $token,
            'email' => $request->email,
            'title' => 'Reset Password',
            'active' => 'reset-password'
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function reset(Request $request)
    {
        // Validasi input
        $request->validate($this->rules(), $this->validationErrorMessages());

        // Dapatkan broker password untuk guard 'patient'
        $response = Password::broker('patients')->reset(
            $this->credentials($request), function ($user) use ($request) {
                // Update password
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                // Fire PasswordReset event
                event(new PasswordReset($user));

                // Login user setelah reset password
                $this->guard()->login($user);
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email', 'password', 'password_confirmation', 'token');
    }

    /**
     * Get the response for a successful password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect($this->redirectTo)->with('status', trans($response));
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('patient'); // Menggunakan guard 'patient'
    }

    /**
     * Get the broker to be used by the provider.
     * Overrides the default to use the 'patients' guard's password broker.
     *
     * @return \Illuminate\Contracts\Auth\Passwords\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('patients'); // Menggunakan broker 'patients'
    }
}