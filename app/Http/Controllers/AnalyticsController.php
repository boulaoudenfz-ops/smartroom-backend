<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function personal()
    {
        $userId = Auth::id();

        $total     = Reservation::where('user_id', $userId)->count();
        $upcoming  = Reservation::where('user_id', $userId)
                        ->whereIn('status', ['approved','pending'])
                        ->where('start_datetime', '>', now())->count();
        $completed = Reservation::where('user_id', $userId)->where('status','completed')->count();
        $pending   = Reservation::where('user_id', $userId)->where('status','pending')->count();

        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date  = Carbon::today()->subDays($i);
            $count = Reservation::where('user_id', $userId)
                        ->whereDate('created_at', $date)->count();
            $weeklyData[] = ['day' => $date->format('D'), 'count' => $count];
        }

        $statusBreakdown = Reservation::where('user_id', $userId)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')->get();

        $upcomingList = Reservation::with('room')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved','pending'])
            ->where('start_datetime', '>', now())
            ->orderBy('start_datetime')->limit(5)->get();

        return $this->success(compact(
            'total','upcoming','completed','pending',
            'weeklyData','statusBreakdown','upcomingList'
        ));
    }

    public function admin()
    {
        $totalRooms        = \App\Models\Room::count();
        $totalUsers        = \App\Models\User::where('role','user')->count();
        $totalReservations = Reservation::count();
        $pendingApprovals  = Reservation::where('status','pending')->count();

        return $this->success(compact(
            'totalRooms','totalUsers','totalReservations','pendingApprovals'
        ));
    }
}