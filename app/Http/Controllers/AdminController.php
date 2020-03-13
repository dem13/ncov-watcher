<?php

namespace App\Http\Controllers;

use App\Jobs\GlobalTelegramMessage;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display admin panel
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function panel()
    {
        return view('admin.panel');
    }

    /**
     *
     */
    public function globalMessage(Request $request)
    {
        if ($message = $request->get('message')) {
            GlobalTelegramMessage::dispatch($message);
        }

        return redirect()->back()->with([
            'global_message_sent' => (boolean)$message
        ]);
    }
}
