<?php

namespace App\Http\Controllers;

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
        $this->middleware('auth:api')->only('update');
    }

    public function forgot(Request $request)
    {
        $request->validate(['dni' => 'required|string|digits:8|exists:users,dni']);

        $user = User::where('dni', $request->get('dni'))->first();
        $token = Str::uuid();

        return transactional(function () use ($user, $token) {
            Token::create([
                'token' => $token,
                'token_type' => 'forgot_password',
                'revoked' => false,
                'expired' => false,
                'expires_at' => now()->addMinutes(5),
                'user_id' => $user->id
            ]);

            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
            return jsonResponse(message: 'Token has sent to your email.');
        });
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|confirmed|min:3'
        ]);

        $token = Token::where('token', $request->get('token'))->first();

        if (!$token) return jsonResponse(status: 400, message: 'Invalid token.');

        if ($token->revoked || $token->expired || $token->expires_at->isPast()) {
            $token->update(['revoked' => true, 'expired' => true]);
            return jsonResponse(status: 400, message: 'Token has expired.');
        }

        $user = User::find($token->user_id);
        $user->password = bcrypt($request->get('password'));
        $user->save();
        $token->delete();
        return jsonResponse(message: 'Your password has been reset.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:3|confirmed'
        ]);

        $user = auth()->user();

        if (!Hash::check($request->get('current_password'), $user->password)) {
            return jsonResponse(status: 400, message: 'Your current password is incorrect.');
        }

        $user->password = bcrypt($request->get('new_password'));
        $user->save();
        return jsonResponse(message: 'Your password has been changed.');
    }
}
