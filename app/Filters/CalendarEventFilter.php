<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CalendarEventFilter
{
    public function __construct(
        protected Request $request
    ) {
    }

    public function apply(Builder $query): Builder
    {
        return $query
            ->when(
                filled($this->request->search),
                function (Builder $q) {
                    $search = $this->request->search;

                    $q->where(function (Builder $sub) use ($search) {
                        $sub->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
                }
            )
            ->when(
                filled($this->request->role),
                fn(Builder $q) =>
                $q->whereHas('user', function (Builder $userQuery) {
                    $userQuery->where('role', $this->request->role);
                })
            )
            ->when(
                filled($this->request->status),
                fn(Builder $q) =>
                $q->where('status', $this->request->status)
            )
            ->when(
                filled($this->request->priority),
                fn(Builder $q) =>
                $q->where('priority', $this->request->priority)
            );
    }
}
