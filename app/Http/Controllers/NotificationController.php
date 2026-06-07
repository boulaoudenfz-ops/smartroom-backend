<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderByDesc('created_at')->paginate(20);
        return $this->success($notifications);
    }

    public function markRead($id)
    {
        Notification::where('id', $id)->where('user_id', Auth::id())
            ->update(['is_read' => true]);
        return $this->success(null, 'Marked as read');
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())->update(['is_read' => true]);
        return $this->success(null, 'All marked as read');
    }
}