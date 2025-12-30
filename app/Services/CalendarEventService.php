<?php

namespace App\Services;

use App\Models\CalendarEvent;
use Illuminate\Support\Collection;
use App\Filters\CalendarEventFilter;

class CalendarEventService
{
    public function __construct(private CalendarEvent $calendar)
    {
    }

    public function all(?CalendarEventFilter $filter = null)
    {
        $query = CalendarEvent::query();

        if ($filter) {
            $filter->apply($query);
        }

        return $query->get();
    }
    public function create(array $data): CalendarEvent
    {
        return $this->calendar->create($data);
    }

    public function update(CalendarEvent $calendar, array $data): CalendarEvent
    {
        $calendar->update($data);
        return $calendar;
    }

    public function delete(CalendarEvent $calendar): void
    {
        $calendar->delete();
    }
}
