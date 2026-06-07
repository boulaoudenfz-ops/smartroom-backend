<?php
namespace App\Services;

use App\Models\Reservation;

class ConflictDetectionService
{
    public function check(int $roomId, string $start, string $end): ?Reservation
    {
        return Reservation::where('room_id', $roomId)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_datetime', [$start, $end])
                      ->orWhereBetween('end_datetime', [$start, $end])
                      ->orWhere(function ($q) use ($start, $end) {
                          $q->where('start_datetime', '<=', $start)
                            ->where('end_datetime', '>=', $end);
                      });
            })
            ->first();
    }
}