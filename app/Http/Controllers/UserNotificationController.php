<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        
        $query = Notifikasi::where('user_id', $user->user_id)->latest();
        if ($request->input('filter') === 'unread') {
            $query->unread();
        }
        $notifications = $query->paginate(10);

        return view('users.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead()
    {
        $user = User::findOrFail(Auth::id());
        
        Notifikasi::where('user_id', $user->user_id)->where('is_read', false)->update(['is_read' => true]);
        return redirect()->back();
    }

    public function markRead(int $id)
    {
        $user = User::findOrFail(Auth::id());
        
        $notif = Notifikasi::where('user_id', $user->user_id)->where('notifikasi_id', $id)->first();
        if ($notif) {
            $notif->is_read = true;
            $notif->save();
        }
        return redirect()->back();
    }
}
