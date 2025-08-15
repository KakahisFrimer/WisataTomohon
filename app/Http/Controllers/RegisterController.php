<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:users,name',
                'regex:/^[a-zA-Z\s]*$/'
            ],
            'email_or_phone' => [
                'required',
                'string',
                'max:255',
                'unique:users,email_or_phone',
                function ($attribute, $value, $fail) {
                    if (strpos($value, '@') !== false) {
                        $validDomains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com'];
                        $domain = explode('@', $value)[1];
                        
                        if (!in_array($domain, $validDomains)) {
                            $fail('Domain email tidak didukung.');
                        }
                    } else {
                        if (!preg_match('/^[0-9]{10,13}$/', $value)) {
                            $fail('Nomor telepon harus 10-13 digit angka.');
                        }
                    }
                }
            ],
            'password' => 'required|string|min:6|confirmed',
            // TAMBAHKAN VALIDASI UNTUK SECURITY QUESTION
            'security_question_id' => 'required|integer|between:1,5',
            'security_answer' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // TAMBAHKAN FIELD SECURITY QUESTION KE DALAM CREATE
        User::create([
            'name' => $request->name,
            'email_or_phone' => $request->email_or_phone,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'security_question_id' => $request->security_question_id,
            'security_answer' => Hash::make($request->security_answer), // HASH JAWABAN
        ]);

        return redirect()->back()->with('success', 'Registrasi berhasil!');
    }
}