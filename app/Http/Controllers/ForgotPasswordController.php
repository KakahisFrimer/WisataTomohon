<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{

    public function showForgetPasswordForm()
    {
        return view('auth.forgot-password');
    }

 
    public function submitForgetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required|string'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email_or_phone', $request->email_or_phone)->first();

        if (!$user) {
            return back()->with('error', 'Email or phone number not registered.');
        }


        Session::put('reset_user', $user);


        return redirect()->route('account.confirmation');
    }


    public function showAccountConfirmationForm()
    {
        $user = Session::get('reset_user');
        if (!$user) {
            return redirect()->route('password.request');
        }

        return view('auth.account-confirmation', compact('user'));
    }

 
    public function showSecurityQuestionForm()
    {
        $user = Session::get('reset_user');
        if (!$user) {
            return redirect()->route('password.request');
        }

        $questions = config('security_questions.questions');
        $currentQuestionId = $user->security_question_id;
        $currentQuestion = $questions[$currentQuestionId] ?? 'Security Questions';

        return view('auth.security-question', compact('questions', 'currentQuestion'));
    }


    public function verifyAnswer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'security_question_id' => 'required|integer|between:1,5',
            'answer' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = Session::get('reset_user');
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Session has ended. Please try again.');
        }


        if ($request->security_question_id != $user->security_question_id) {
            return back()->with('error', 'Selected question does not match the registration.');
        }

        if (Hash::check($request->answer, $user->security_answer)) {

            $token = Str::random(60);
            $user->password_reset_token = $token;
            $user->save();

            return redirect()->route('password.reset', $token);
        }

        return back()->with('error', 'Wrong answer. Please try again.');
    }

    public function showResetPasswordForm($token)
    {
        $user = User::where('password_reset_token', $token)->first();
        if (!$user) {
            return redirect()->route('password.request')->with('error', 'Invalid or expired token.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'user' => $user
        ]);
    }


    public function submitResetPasswordForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('password_reset_token', $request->token)->first();
        if (!$user) {
            return back()->with('error', 'Invalid or expired token.');
        }


        if (Hash::check($request->password, $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'Your new password cannot be the same as your previous password']);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->save();


        Session::forget('reset_user');


        return redirect()->route('password.success');
    }


    public function showSuccessPage()
    {
        return view('auth.password-success');
    }
}