<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function forgot_pwd_mail(Request $request)
    {

        $email_check = User::where('email', $request->email)->first();

        if ($email_check) {
            $password = Str::random(8);
            $hashed_password = Hash::make($password);
            $email_check->password = $hashed_password;
            $email_check->save();

            $year = \Carbon\Carbon::now()->year;

            \Mail::send(
                'auth.password_reset_mail',
                [
                    'email' => $request->get('email'),
                    'password' => $password,
                    'year' => $year,
                    'name' => $email_check->name
                ],

                function ($message) use ($request) {
                    $message->from('joshuazeigler17@gmail.com');
                    $message->to($request->email)->subject('Glow Recovery Password Confirmation');
                }

            );

            return redirect()->back()->with(['message' => 'Mail sent successfully! Please check it.']);
        } else {
            return redirect()->back()->with(['message' => 'Email not verified or email not found']);
        }
    }
}
