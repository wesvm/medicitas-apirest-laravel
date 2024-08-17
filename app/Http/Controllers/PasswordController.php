<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Mail\PasswordResetMail;
use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('changePassword');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $user = User::where('dni', $request->dni)->first();
        $token = Str::uuid();

        Token::create([
            'token' => $token,
            'token_type' => 'forgot_password',
            'revoked' => false,
            'expired' => false,
            'expires_at' => now()->addMinutes(5),
            'user_id' => $user->id
        ]);

        $this->getUserDataByRole($user);

        Mail::to($user->email)->send(new PasswordResetMail($user, $token));
        return jsonResponse(message: 'Token has sent to your email.');
    }

    public function resetPassword(ResetPasswordRequest $request){
        $token = Token::where('token', $request->token)->first();

        if (!$token) return jsonResponse(status: 400, message: 'Invalid token.');

        if ($token->revoked || $token->expired || $token->expires_at->isPast()) {
            $token->update(['revoked' => true, 'expired' => true]);
            return jsonResponse(status: 400, message: 'Token has expired.');
        }

        $user = User::find($token->user_id);
        $user->password = bcrypt($request->password);
        $user->save();
        $token->delete();
        return jsonResponse(message: 'Password has been reset.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:3', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return jsonResponse(status: 400, message: 'Current password is incorrect.');
        }

        $user->password = bcrypt($request->new_password);
        $user->save();
        return jsonResponse(message: 'Password has been changed.');
    }

    private function getUserDataByRole(User $user): void
    {
        switch ($user->role) {
            case Roles::ADMIN->value:
                $user->load('admin');
                break;
            default:
        }
    }
}
