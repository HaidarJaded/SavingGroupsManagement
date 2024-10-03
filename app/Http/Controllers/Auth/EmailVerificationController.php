<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailVerifyRequest;
use App\Models\Client;
use App\Models\User;
use App\Notifications\Auth\EmailVerificationNotification;
use App\Traits\ApiResponseTrait;
use Ichtrojan\Otp\Otp;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class EmailVerificationController extends Controller
{
    use ApiResponseTrait;

    public function __construct()
    {
    }

    public function sendEmailVerificationNotification(Request $request): JsonResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->apiResponse(null, 403, 'The user has already been verified. Invalid process.');
        }

        $request->user()->notify(new EmailVerificationNotification());

        return $this->apiResponse('Verification message was sent successfully');
    }

    /**
     * Mark email as verified
     *
     */
    public function emailVerify(Request $request, string $token)
    {
        try {
            $tokenInfo = PersonalAccessToken::where('token', $token)->firstOrFail();
            $verifiable = $tokenInfo->tokenable;
            if (is_null($verifiable)) {
                abort(404, 'Not found');
            }
            if (!is_null($verifiable->email_verified_at)) {
                return view('email_verification_success', ['message' => 'The email has already been verified!']);
            }

            $emailVerifySuccess = $verifiable->forceFill([
                'email_verified_at' => now()
            ])->save();

            if ($emailVerifySuccess) {
                return view('email_verification_success', ['message' => 'Email Verified!']);
            }

            return $this->apiResponse(null, 500, 'Failed to verify email. Please try again later.');
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }
}
