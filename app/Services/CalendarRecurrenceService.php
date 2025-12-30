<?php

namespace App\Services;

use App\Models\CalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CalendarRecurrenceService
{

    public function generate(array $baseData): void
    {
        if (
            empty($baseData['recurrence_type']) ||
            $baseData['recurrence_type'] === 'none'
        ) {
            CalendarEvent::create($baseData);
            return;
        }

        if (empty($baseData['recurrence_until'])) {
            throw new \InvalidArgumentException(
                'recurrence_until é obrigatório para eventos recorrentes'
            );
        }

        $groupId = (string) Str::uuid();

        $startDate = Carbon::parse($baseData['start_date'])->startOfDay();
        $untilDate = Carbon::parse($baseData['recurrence_until'])->endOfDay();

        $dates = $this->generateDates($baseData, $startDate, $untilDate);

        foreach ($dates as $date) {
            CalendarEvent::create(array_merge(
                $baseData,
                [
                    'start_date' => $date->toDateString(),
                    'end_date' => $this->resolveEndDate($baseData, $date),
                    'recurrence_group_id' => $groupId,
                ]
            ));
        }
    }

    private function resolveEndDate(array $data, Carbon $currentDate): ?string
    {
        if (empty($data['end_date'])) {
            return null;
        }

        $start = Carbon::parse($data['start_date']);
        $end = Carbon::parse($data['end_date']);

        $duration = $start->diffInDays($end);

        return $currentDate
            ->copy()
            ->addDays($duration)
            ->toDateString();
    }


    private function generateDates(
        array $data,
        Carbon $start,
        Carbon $until
    ): array {
        return match ($data['recurrence_type']) {
            'daily' => $this->daily($start, $until),
            'business_daily' => $this->businessDaily($start, $until),
            'weekly' => $this->weekly($start, $until, $data['repeat_day_of_week'] ?? null),
            'biweekly' => $this->biweekly($start, $until, $data['repeat_day_of_week'] ?? null),
            'monthly' => $this->monthly($start, $until, $data['repeat_day_of_month'] ?? null),
            'yearly' => $this->yearly($start, $until, $data['repeat_day_of_year'] ?? null),
            default => [],
        };
    }

    private function daily(Carbon $start, Carbon $until): array
    {
        $dates = [];
        $current = $start->copy();

        while ($current->lte($until)) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return $dates;
    }

    private function businessDaily(Carbon $start, Carbon $until): array
    {
        $dates = [];
        $current = $start->copy();

        while ($current->lte($until)) {
            if ($current->isWeekday()) {
                $dates[] = $current->copy();
            }
            $current->addDay();
        }

        return $dates;
    }

    private function weekly(
        Carbon $start,
        Carbon $until,
        ?string $daysOfWeek
    ): array {
        if (empty($daysOfWeek)) {
            return [];
        }

        $days = array_map('intval', explode(',', $daysOfWeek));

        $dates = [];
        $current = $start->copy();

        while ($current->lte($until)) {
            if (in_array($current->dayOfWeek, $days, true)) {
                $dates[] = $current->copy();
            }
            $current->addDay();
        }

        return $dates;
    }

    private function biweekly(
        Carbon $start,
        Carbon $until,
        ?string $daysOfWeek
    ): array {
        if (empty($daysOfWeek)) {
            return [];
        }

        $days = array_map('intval', explode(',', $daysOfWeek));

        $dates = [];
        $current = $start->copy();

        while ($current->lte($until)) {
            $weeksDiff = $start->diffInWeeks($current);

            if (
                $weeksDiff % 2 === 0 &&
                in_array($current->dayOfWeek, $days, true)
            ) {
                $dates[] = $current->copy();
            }

            $current->addDay();
        }

        return $dates;
    }

    private function monthly(
        Carbon $start,
        Carbon $until,
        ?int $dayOfMonth
    ): array {
        if (!$dayOfMonth || $dayOfMonth < 1 || $dayOfMonth > 31) {
            return [];
        }

        $dates = [];
        $current = $start->copy()->day($dayOfMonth);

        if ($current->lt($start)) {
            $current->addMonth();
        }

        while ($current->lte($until)) {
            if ($current->day === $dayOfMonth) {
                $dates[] = $current->copy();
            }
            $current->addMonth();
        }

        return $dates;
    }

    private function yearly(
        Carbon $start,
        Carbon $until,
        ?int $dayOfYear
    ): array {
        if (!$dayOfYear || $dayOfYear < 1 || $dayOfYear > 366) {
            return [];
        }

        $dates = [];
        $year = $start->year;

        while (true) {
            $date = Carbon::create($year, 1, 1)->addDays($dayOfYear - 1);

            if ($date->lt($start)) {
                $year++;
                continue;
            }

            if ($date->gt($until)) {
                break;
            }

            $dates[] = $date;
            $year++;
        }

        return $dates;
    }
}
