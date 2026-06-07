<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminRoomController extends Controller
{
    use ApiResponse;

    public function index()    { return $this->success(Room::with('equipment')->paginate(20)); }
    public function show(Room $room) { return $this->success($room->load('equipment')); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'capacity'          => 'required|integer|min:1',
            'floor'             => 'nullable|integer',
            'building'          => 'nullable|string|max:100',
            'type'              => 'required|in:meeting,classroom,lab,coworking,conference',
            'requires_approval' => 'boolean',
        ]);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::random(4);
        $room = Room::create($data);
        return $this->success($room, 'Room created', 201);
    }

    public function update(Request $request, Room $room)
    {
        $room->update($request->except('slug'));
        return $this->success($room, 'Room updated');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return $this->success(null, 'Room deleted');
    }
}