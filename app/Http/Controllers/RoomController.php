<?php
namespace App\Http\Controllers;

use App\Models\Room;
use App\Traits\ApiResponse;
use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponse;

    public function __construct(private RecommendationService $recommender) {}

    public function index(Request $request)
    {
        $query = Room::with('equipment')
            ->where('status', 'available');

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->capacity) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->building) {
            $query->where('building', $request->building);
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $rooms = $query->orderBy('name')->paginate(12);

        return $this->success($rooms);
    }

    public function show(Room $room)
    {
        $room->load(['equipment', 'reservations' => function ($q) {
            $q->where('status', 'approved')
              ->where('end_datetime', '>=', now())
              ->select('id', 'room_id', 'start_datetime', 'end_datetime', 'title');
        }]);

        return $this->success($room);
    }

    public function availability(Request $request, Room $room)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $reservations = $room->reservations()
            ->whereDate('start_datetime', $request->date)
            ->where('status', 'approved')
            ->get(['start_datetime', 'end_datetime', 'title']);

        return $this->success([
            'room'         => $room->only('id', 'name', 'capacity'),
            'date'         => $request->date,
            'reservations' => $reservations,
        ]);
    }

    public function recommendations(Request $request)
    {
        $request->validate([
            'capacity'       => 'required|integer',
            'start_datetime' => 'required|date',
            'end_datetime'   => 'required|date|after:start_datetime',
        ]);

        $rooms = $this->recommender->recommend(
            $request->capacity,
            $request->start_datetime,
            $request->end_datetime
        );

        return $this->success($rooms);
    }
}