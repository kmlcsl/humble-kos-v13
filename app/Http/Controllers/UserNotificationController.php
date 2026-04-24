<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notifikasi;

class UserNotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $query = Notifikasi::where('user_id', $user->user_id)->latest();
        if ($request->get('filter') === 'unread') {
            $query->unread();
        }
        $notifications = $query->paginate(10);

        return view('users.notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        Notifikasi::where('user_id', $user->user_id)->where('is_read', false)->update(['is_read' => true]);
        return redirect()->back();
    }

    public function markRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        $notif = Notifikasi::where('user_id', $user->user_id)->where('notifikasi_id', $id)->first();
        if ($notif) {
            $notif->is_read = true;
            $notif->save();
        }
        return redirect()->back();
    }
}
