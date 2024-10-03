<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\Client;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class ResetPasswordController extends Controller
{
    use ApiResponseTrait;

    public function resetPasswordPage(Request $request, $token)
    {
        $email = $request->get('email');
        if (is_null($email) || is_null($token)) {
            abort(404, 'Not found');
        }
        $storedToken = DB::table('password_reset_tokens')
            ->where('email', $email)->get();
        if (count($storedToken) > 0 && Hash::check($token, $storedToken->value('token'))) {
            return view('reset_password_page', ['email' => $email, 'token' => $token]);
        }
        abort(404, 'Not found');
    }
    public function resetPasswordRequest(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        try {
            $token = Password::broker()->createToken($user);
            //FIXME put correct reset password link
            $resetLink = url('https://test/password/reset/confirm/' . $token . '?email=' . $user->email);

            Mail::to($user)->send(new
                ResetPasswordMail($resetLink));

            return $this->apiResponse('Reset link sent to your email');
        } catch (\Exception $e) {
            return $this->apiResponse($e->getMessage(), 500, 'Failed to send email');
        }
    }

    protected function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', 'min:8', Rules\Password::defaults()],
        ];
    }


    protected function credentials(Request $request)
    {
        return $request->only(['email', 'password', 'password_confirmation', 'token']);
    }

    protected function resetPasswordConfirm(Request $request)
    {
        $validation = validator::make($this->credentials($request), $this->rules());
        $token = $request->get('token');
        $email = $request->get('email');
        if ($validation->fails()) {
            return view('reset_password_page', [
                'email' => $email,
                'token' => $token,
                'errors' => $validation->errors()
            ]);
        }
        $password = $request->input('password');
        $storedToken = DB::table('password_reset_tokens')
            ->where('email', $email)->get();
        if (count($storedToken) > 0 && Hash::check($token, $storedToken->value('token'))) {
            $user = User::where('email', $email)->first();
            $auth = $user;
            $auth->forcefill([
                'password' => hash::make($password),
                'remember_token' => str::random(60),
            ])->save();
            DB::table('password_reset_tokens')
                ->where('email', $email)->delete();
            return view('reset_password_success');
        }
        abort(404, 'Not found');
    }
}
