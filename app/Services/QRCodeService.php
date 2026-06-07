<?php
namespace App\Services;

use Illuminate\Support\Str;

class QRCodeService
{
    public function generate(int $reservationId): string
    {
        return hash('sha256', $reservationId . Str::random(32) . config('app.key'));
    }
}