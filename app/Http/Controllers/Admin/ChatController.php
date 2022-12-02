<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use DB;
use Hash;
use App\User;
use function App\Helpers\generate_api_token;

class ChatController extends Controller
{
    public function getChatView(Request $request)
    {
        try {
            $user_temp_id = $request->user_temp_id;
            return view('admin.chat.view', compact('user_temp_id'));
        } catch (\Exception $e) {
            throw new \App\Exceptions\CustomException($e->getMessage());
        }
    }

    public function getChatView1()
    {
        return view('admin.chat.view1');
    }
}
