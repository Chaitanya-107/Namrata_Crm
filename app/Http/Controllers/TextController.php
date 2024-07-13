<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendText;
use App\Models\Text;
use App\Models\User;
use Illuminate\Http\Request;

class TextController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'message' => 'required|string',
            'recipient' => 'required',
        ]);

        // Save text message
        $text = new Text([
            'message' => $request->message,
            'status' => 0,
        ]);
        $text->recipient()->associate(User::findOrFail($request->recipient));
        $text->sender()->associate($request->user());
        $text->save();

        // Dispatch job to send text asynchronously
        dispatch(new SendText($text));

        return redirect()->back()->with('status', trans('communication.message.info.sent', ['type' => 'text / SMS']));
    }

    public function delete(Text $text)
    {
        $text->delete();
        return redirect()->back()->with('status', trans('communication.message.info.deleted', ['type' => 'text / SMS']));
    }

    public function getAll(Request $request, User $recipient)
    {
        $user = $request->user();
        $texts = $recipient->incomingTexts->where('sender_id', $user->id)
            ->merge($recipient->outgoingTexts->where('recipient_id', $user->id))
            ->sortByDesc('created_at');

        return view('global.texts', compact('texts', 'recipient'));
    }
}
