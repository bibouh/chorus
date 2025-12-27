<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Seeder;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            ['name' => 'Répétition ordinaire', 'slug' => 'repetition-ordinaire', 'description' => 'Répétition ordinaire'],
            ['name' => 'Répétition extraordinaire', 'slug' => 'repetition-extraordinaire', 'description' => 'Répétition extraordinaire'],
            ['name' => 'Répétition concert', 'slug' => 'repetition-concert', 'description' => 'Répétition concert'],
            ['name' => 'Prière du vendredi', 'slug' => 'priere-du-vendredi', 'description' => 'Prière du vendredi'],
            ['name' => 'Messe dimanche', 'slug' => 'messe-dimanche', 'description' => 'Messe dimanche'],
            ['name' => 'Messe mariage', 'slug' => 'messe-mariage', 'description' => 'Messe mariage'],
            ['name' => 'Autres messes', 'slug' => 'autres-messes', 'description' => 'Autres messes'],
            ['name' => 'Concert', 'slug' => 'concert', 'description' => 'Concert'],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::firstOrCreate(
                ['slug' => $eventType['slug']],
                $eventType
            );
        }
    }
}
