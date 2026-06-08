<?php
namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Models\Reservation;
use App\Services\ConflictDetectionService;
use App\Services\QRCodeService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReservationController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ConflictDetectionService $conflictDetector,
        private QRCodeService $qrService
    ) {}

    public function index()
    {
        $reservations = Reservation::with('room')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return $this->success($reservations);
    }

    public function store(ReservationRequest $request)
    {
        $conflict = $this->conflictDetector->check(
            $request->room_id,
            $request->start_datetime,
            $request->end_datetime
        );

        if ($conflict) {
            return $this->error('Time slot conflict detected', 409, [
                'conflicting_reservation' => $conflict,
            ]);
        }

        $reservation = Reservation::create([
            'user_id'          => Auth::id(),
            'room_id'          => $request->room_id,
            'title'            => $request->title,
            'description'      => $request->description,
            'start_datetime'   => $request->start_datetime,
            'end_datetime'     => $request->end_datetime,
            'attendees_count'  => $request->attendees_count,
            'status'           => 'pending',
        ]);

        $qrCode = $this->qrService->generate($reservation->id);
        $reservation->update(['qr_code' => $qrCode]);

        return $this->success($reservation->load('room'), 'Reservation created', 201);
    }

    public function show(Reservation $reservation)
    {
        $this->authorize('view', $reservation);
        return $this->success($reservation->load(['room', 'user']));
    }

public function cancel(Reservation $reservation)
{
    $user = auth()->user();

    if ($reservation->user_id !== $user->id && !$user->is_admin) {
        return $this->error('Unauthorized');
    }

    if (!in_array($reservation->status, ['pending', 'approved'])) {
        return $this->error('Cannot cancel this reservation');
    }

    if ($reservation->status === 'cancelled') {
        return $this->error('Already cancelled');
    }

    $reservation->update([
        'status' => 'cancelled'
    ]);

    return $this->success($reservation, 'Reservation cancelled successfully');
}



    // public function cancel(Reservation $reservation)
    // {
    //     $this->authorize('cancel', $reservation);

    //     if (!in_array($reservation->status, ['pending', 'approved'])) {
    //         return $this->error('Cannot cancel this reservation');
    //     }

    //     $reservation->update(['status' => 'cancelled']);

    //     return $this->success($reservation, 'Reservation cancelled');
    // }

    public function checkin(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $reservation = Reservation::where('qr_code', $request->qr_code)
            ->where('status', 'approved')
            ->whereDate('start_datetime', today())
            ->first();

        if (!$reservation) {
            return $this->error('Invalid or expired QR code', 404);
        }

        $reservation->update(['checked_in_at' => now()]);

        return $this->success($reservation->load('room'), 'Check-in successful');
    }
}