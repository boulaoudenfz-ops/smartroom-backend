<?php
namespace App\Services;

use App\Models\Room;

class RecommendationService
{
    public function recommend(int $capacity, string $start, string $end)
    {
        $conflictingRoomIds = \App\Models\Reservation::whereIn('status', ['approved', 'pending'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                  ->orWhereBetween('end_datetime', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_datetime', '<=', $start)
                         ->where('end_datetime', '>=', $end);
                  });
            })
            ->pluck('room_id');

        return Room::where('status', 'available')
            ->where('capacity', '>=', $capacity)
            ->whereNotIn('id', $conflictingRoomIds)
            ->orderByRaw('ABS(capacity - ?) ASC', [$capacity])
            ->with('equipment')
            ->limit(5)
            ->get();
    }
}