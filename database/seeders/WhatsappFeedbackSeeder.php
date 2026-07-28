<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WhatsappFeedback;

class WhatsappFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate to ensure clean state; add real records via Filament Admin
        WhatsappFeedback::truncate();
    }
}
