<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'room_id'          => 'required|exists:rooms,id',
            'title'            => 'required|string|min:3|max:255',
            'description'      => 'nullable|string|max:1000',
            'start_datetime'   => 'required|date',
            'end_datetime'     => 'required|date|after:start_datetime',
            'attendees_count'  => 'required|integer|min:1|max:500',
        ];
    }
}