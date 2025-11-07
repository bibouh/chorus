<?php

namespace Database\Seeders;

use App\Models\EventType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            ['name' => 'Rehearsal', 'slug' => 'rehearsal', 'description' => 'Répétition'],
            ['name' => 'Concert', 'slug' => 'concert', 'description' => 'Concert'],
            ['name' => 'General Rehearsal', 'slug' => 'general-rehearsal', 'description' => 'Répétition générale'],
            ['name' => 'Technical Rehearsal', 'slug' => 'technical-rehearsal', 'description' => 'Répétition technique'],
            ['name' => 'Rehearsal with Soloists', 'slug' => 'rehearsal-with-soloists', 'description' => 'Répétition avec solistes'],
            ['name' => 'Orchestra Rehearsal', 'slug' => 'orchestra-rehearsal', 'description' => 'Répétition d\'orchestre'],
            ['name' => 'Gala Concert', 'slug' => 'gala-concert', 'description' => 'Concert de gala'],
            ['name' => 'Charity Concert', 'slug' => 'charity-concert', 'description' => 'Concert de bienfaisance'],
            ['name' => 'Other', 'slug' => 'other', 'description' => 'Autre'],
        ];

        foreach ($eventTypes as $eventType) {
            EventType::firstOrCreate(
                ['slug' => $eventType['slug']],
                $eventType
            );
        }
    }
}
