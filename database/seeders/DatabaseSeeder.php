<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Equipment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name'       => 'Admin SmartRoom',
            'email'      => 'admin@smartroom.dev',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'department' => 'Administration',
        ]);

        // Users
        collect(['Alice Martin', 'Bob Dupont', 'Clara Benali'])->each(function ($name, $i) {
            User::create([
                'name'     => $name,
                'email'    => strtolower(explode(' ', $name)[0]) . '@smartroom.dev',
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]);
        });

        // Equipment
        $equipment = collect(['Projector', 'WiFi', 'Whiteboard', 'Coffee Machine', 'Video Conf'])
            ->map(fn($name) => Equipment::create(['name' => $name]))->pluck('id');

        // Rooms
        $rooms = [
            ['name' => 'Salle Innovation',   'capacity' => 12, 'type' => 'meeting',    'building' => 'A'],
            ['name' => 'Lab Numérique',       'capacity' => 20, 'type' => 'lab',        'building' => 'B'],
            ['name' => 'Espace Collaboratif', 'capacity' => 30, 'type' => 'coworking',  'building' => 'A'],
            ['name' => 'Amphi Connect',       'capacity' => 80, 'type' => 'conference', 'building' => 'C'],
            ['name' => 'Salle Agile',         'capacity' => 8,  'type' => 'meeting',    'building' => 'B'],
        ];

        foreach ($rooms as $data) {
            $room = Room::create(array_merge($data, [
                'description' => "Espace moderne équipé pour {$data['type']}",
                'floor'       => rand(1, 4),
                'status'      => 'available',
            ]));
            $room->equipment()->attach($equipment->random(3)->toArray());
        }
    }
}
