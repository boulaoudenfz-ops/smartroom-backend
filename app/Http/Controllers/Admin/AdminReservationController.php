<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class AdminReservationController extends Controller
{
    use ApiResponse;

    public function index()
    {
        return $this->success(
            Reservation::with(['user','room'])->orderByDesc('created_at')->paginate(20)
        );
    }

    public function show(Reservation $reservation)
    {
        return $this->success($reservation->load(['user','room']));
    }

    public function approve(Reservation $reservation)
    {
        $reservation->update(['status' => 'approved']);
        Notification::create([
            'user_id' => $reservation->user_id,
            'title'   => 'Reservation Approved',
            'message' => "Your reservation '{$reservation->title}' has been approved.",
            'type'    => 'success',
        ]);
        return $this->success($reservation, 'Approved');
    }

    public function reject(Request $request, Reservation $reservation)
    {
        $request->validate(['rejection_reason' => 'required|string']);
        $reservation->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        Notification::create([
            'user_id' => $reservation->user_id,
            'title'   => 'Reservation Rejected',
            'message' => "Your reservation '{$reservation->title}' was rejected: {$request->rejection_reason}",
            'type'    => 'error',
        ]);
        return $this->success($reservation, 'Rejected');
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update($request->only('status','rejection_reason'));
        return $this->success($reservation);
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return $this->success(null, 'Deleted');
    }
}