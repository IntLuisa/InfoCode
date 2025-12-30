<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CalendarEvent;
use Carbon\Carbon;

class CalendarEventSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'sold',
            'send_to_factory',
            'to_invoice',
            'separation',
            'waiting_assembly',
            'assembling',
            'deliver_expenses',
            'completed',
            'general_calendar',
        ];

        $recurrences = ['none', 'daily', 'weekly', 'biweekly', 'monthly'];
        $tags = ['reunião', 'evento', 'interno', 'externo', 'treinamento', 'planejamento'];

        for ($i = 1; $i <= 50; $i++) {

            $startDate = Carbon::now()
                ->addDays(rand(-30, 60))
                ->startOfDay();

            $endDate = (clone $startDate)
                ->addDays(rand(0, 3))
                ->endOfDay();

            $recurrenceType = $recurrences[array_rand($recurrences)];

            CalendarEvent::create([
                'title' => "Evento {$i}",
                'description' => "Descrição do evento número {$i}",

                'start_date' => $startDate,
                'end_date' => $endDate,

                'flexible_date' => (bool) rand(0, 1),
                'is_all_day' => true,
                'is_montage_date' => (bool) rand(0, 1),

                'recurrence_type' => $recurrenceType,
                'recurrence_until' => $recurrenceType !== 'none'
                    ? (clone $startDate)->addMonths(rand(1, 4))
                    : null,

                'repeat_day_of_week' => in_array($recurrenceType, ['weekly', 'biweekly'])
                    ? rand(0, 6)
                    : null,

                'repeat_day_of_month' => $recurrenceType === 'monthly'
                    ? rand(1, 28)
                    : null,

                'repeat_day_of_year' => null,

                'tags' => $tags[array_rand($tags)],
                'status' => $statuses[array_rand($statuses)],

                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }
    }
}
