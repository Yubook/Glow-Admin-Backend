<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Messages;
use App\Events\NewMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function index()
    {
        return view('admin.chat');
    }

    public function get()
    {
        //get the contact list except aunthecated user
        $contacts = User::where('id', '!=', auth()->id())->get();

        $unreadIds = Messages::select(\DB::raw('`from` as sender_id , count(`from`) as messages_count'))
            ->where('to', auth()->id())
            ->where('read', false)
            ->groupBy('from')
            ->get();
        $contacts = $contacts->map(function ($contact) use ($unreadIds) {
            $contactUnread = $unreadIds->where('sender_id', $contact->id)->first();
            $contact->unread = $contactUnread ? $contactUnread->messages_count : 0;
            return $contact;
        });

        return response()->json($contacts);
    }

    public function getMessagesFor($id)
    {
        //update the read message
        Messages::where('from', $id)->where('to', auth()->id())->update(['read' => true]);
        // $messages = Messages::where('from', $id)->orWhere('to', $id)->get();

        $messages = DB::table('messages')->where(function ($q) use ($id) {
            $q->where('from', auth()->id());
            $q->where('to', $id);
        })->orWhere(function ($q) use ($id) {
            $q->where('from', $id);
            $q->where('to', auth()->id());
        })->select("messages.*")->get();

        return response()->json($messages);
    }

    public function send(Request $request)
    {
        if (request()->has('file')) {
            $filename = request('file')->store('chat');
            $message = Messages::create([
                'from' => auth()->user()->id,
                'to' => $request->contact_id,
                'image' => $filename,
                'created_at' => Carbon::now()
            ]);
        } else {
            $message = Messages::create([
                'from' => auth()->user()->id,
                'to' => $request->contact_id,
                'message' => $request->text,
                'created_at' => Carbon::now()
            ]);
        }

        broadcast(new NewMessage($message));
        return response()->json($message);
    }
}
