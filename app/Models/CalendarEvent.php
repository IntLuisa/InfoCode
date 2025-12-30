<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Service;



class CalendarEvent extends Model
{
    use HasFactory;

    protected $table = 'calendar_events';

    protected $fillable = [
        'start_date',
        'end_date',
        'flexible_date',
        'is_all_day',
        'is_montage_date',

        'recurrence_type',
        'recurrence_until',
        'repeat_day_of_week',
        'repeat_day_of_month',
        'repeat_day_of_year',

        'title',
        'description',
        'location',
        'service_id',

        'tags',
        'status',

        'created_by',
        'updated_by',

        'start_time',
        'end_time',
        'priority',
        'responsible_id',

        'user_id',
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',

        'flexible_date' => 'boolean',
        'is_all_day' => 'boolean',
        'is_montage_date' => 'boolean',

        'recurrence_until' => 'date',

        'repeat_day_of_week' => 'integer',
        'repeat_day_of_month' => 'integer',
        'repeat_day_of_year' => 'integer',
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function booted()
    {
        static::addGlobalScope('user_service', function (Builder $builder) {
            $user = auth()->user();

            if (!$user) {
                return;
            }

            if (in_array($user->role, ['Board', 'Manager', 'Observer'])) {
                return;
            }

            if (in_array($user->role, ['Consultant', 'Assembler'])) {
                $builder->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('responsible_id', $user->id);
                });

                return;
            }

            if ($user->role === 'Assembly Manager') {
                $assemblerIds = User::where('role', 'Assembler')->pluck('id');

                $builder->where(function ($q) use ($user, $assemblerIds) {
                    $q->where('user_id', $user->id)
                        ->orWhere('responsible_id', $user->id)
                        ->orWhereIn('user_id', $assemblerIds)
                        ->orWhereIn('responsible_id', $assemblerIds);
                });

                return;
            }

            if ($user->role === 'Finance') {
                $financeIds = User::whereIn('role', ['Finance', 'Financial Assistant'])
                    ->pluck('id');

                $builder->where(function ($q) use ($user, $financeIds) {
                    $q->where('user_id', $user->id)
                        ->orWhere('responsible_id', $user->id)
                        ->orWhereIn('user_id', $financeIds)
                        ->orWhereIn('responsible_id', $financeIds);
                });

                return;
            }

            if ($user->role === 'Financial Assistant') {
                $builder->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhere('responsible_id', $user->id);
                });

                return;
            }
        });
    }

    protected $appends = ['hierarchy_color'];

    public function getHierarchyColorAttribute()
    {
        if (!$this->creator) {
            return '#6b7280';
        }

        return match ($this->creator->role) {
            'Board' => '#7c3aed',
            'Manager' => '#2563eb',
            'Consultant' => '#16a34a',
            'Assembler' => '#f59e0b',
            // 'Observer' => '#6b7280',
            'Finance' => '#dc2626',
            'Financial Assistant' => '#ef4444',
            'Assembly Manager' => '#0d9488',
            default => '#16a34a',
        };
    }

}
