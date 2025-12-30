<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Services\CalendarEventService;
use App\Services\CalendarRecurrenceService;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Filters\CalendarEventFilter;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function __construct(
        private CalendarEventService $calendarService,
        private CalendarRecurrenceService $recurrenceService,
    ) {
    }

    public function index(Request $request, CalendarEventFilter $filter)
    {
        return Inertia::render('Calendar/Index', [
            'events' => $this->calendarService
                ->all($filter)
                ->load([
                    'responsible:id,name',
                    'user:id,role'
                ]),
            'users' => User::select('id', 'name')->get(),
            'filters' => $request->all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        [$startTime, $endTime] = $this->normalizeTimes($data);

        $this->validateTimeOrder($startTime, $endTime);

        if (($data['recurrence_type'] ?? 'none') === 'none') {
            CalendarEvent::create(array_merge(
                $this->payload($data, $startTime, $endTime),
                [
                    'created_by' => auth()->id(),
                    'user_id' => auth()->id(),
                ]
            ));


            return back()->with('success', 'Evento criado com sucesso!');
        }

        $this->recurrenceService->generate(array_merge(
            $this->payload($data, $startTime, $endTime),
            [
                'created_by' => auth()->id(),
                'user_id' => auth()->id(),
            ]
        ));

        return back()->with('success', 'Eventos recorrentes criados com sucesso!');
    }

    public function update(Request $request, CalendarEvent $calendar)
    {
        if (
            $request->keys() === ['status']
        ) {
            $request->validate([
                'status' => 'required|in:sold,send_to_factory,to_invoice,separation,waiting_assembly,assembling,deliver_expenses,completed,general_calendar',
            ]);

            $calendar->update([
                'status' => $request->status,
                'updated_by' => auth()->id(),
            ]);

            return back()->with('success', 'Status atualizado!');
        }

        if (
            $request->keys() === ['start_date', 'end_date']
        ) {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $calendar->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'updated_by' => auth()->id(),
            ]);

            return back()->with('success', 'Data atualizada!');
        }

        $data = $this->validateData($request);

        [$startTime, $endTime] = $this->normalizeTimes($data);

        $this->validateTimeOrder($startTime, $endTime);

        $calendar->update(array_merge(
            $this->payload($data, $startTime, $endTime),
            ['updated_by' => auth()->id()]
        ));

        return back()->with('success', 'Evento atualizado com sucesso!');
    }


    public function destroy(CalendarEvent $calendar)
    {
        $calendar->delete();

        return back()->with('success', 'Evento removido com sucesso!');
    }


    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'start_date' => 'required|date',
            'end_date' => 'nullable|date',

            'flexible_date' => 'boolean',
            'is_montage_date' => 'boolean',

            'recurrence_type' => 'nullable|in:none,daily,business_daily,weekly,biweekly,monthly,yearly',
            'recurrence_until' => 'nullable|date',

            'repeat_day_of_week' => 'nullable|integer|min:0|max:6',
            'repeat_day_of_month' => 'nullable|integer|min:1|max:31',
            'repeat_day_of_year' => 'nullable|integer|min:1|max:366',

            'tags' => 'nullable|string|max:255',

            'status' => 'nullable|in:sold,send_to_factory,to_invoice,separation,waiting_assembly,assembling,deliver_expenses,completed,general_calendar',

            'start_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],

            'priority' => 'nullable|string|min:0|max:50',
            'responsible_id' => 'nullable|exists:users,id',
        ]);
    }

    private function normalizeTimes(array $data): array
    {
        $startTime = isset($data['start_time'])
            ? substr($data['start_time'], 0, 5)
            : null;

        $endTime = isset($data['end_time'])
            ? substr($data['end_time'], 0, 5)
            : null;

        return [$startTime, $endTime];
    }

    private function validateTimeOrder(?string $startTime, ?string $endTime): void
    {
        if ($startTime && $endTime) {
            if (
                Carbon::createFromFormat('H:i', $endTime)
                    ->lessThanOrEqualTo(
                        Carbon::createFromFormat('H:i', $startTime)
                    )
            ) {
                abort(
                    redirect()->back()->withErrors([
                        'end_time' => 'O horário final deve ser maior que o horário inicial.',
                    ])
                );
            }
        }
    }

    private function payload(array $data, ?string $startTime, ?string $endTime): array
    {
        return [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,

            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,

            'flexible_date' => $data['flexible_date'] ?? false,
            'is_all_day' => empty($startTime) && empty($endTime),
            'is_montage_date' => $data['is_montage_date'] ?? false,

            'recurrence_type' => $data['recurrence_type'] ?? null,
            'recurrence_until' => $data['recurrence_until'] ?? null,

            'repeat_day_of_week' => $data['repeat_day_of_week'] ?? null,
            'repeat_day_of_month' => $data['repeat_day_of_month'] ?? null,
            'repeat_day_of_year' => $data['repeat_day_of_year'] ?? null,

            'tags' => $data['tags'] ?? null,
            'status' => $data['status'] ?? null,

            'start_time' => $startTime,
            'end_time' => $endTime,

            'priority' => $data['priority'] ?? null,
            'responsible_id' => $data['responsible_id'] ?? null,
        ];
    }

}
