<?php

namespace App\Http\Controllers;

use App\Models\Part;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\PartService;

class PartController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private PartService $partService) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Part::class);

        if ($request->boolean('all')) {
            return Part::orderBy('name')->get();
        }

        $search = $this->partService->search($request);

        if ($request->has('json')) {
            return $search;
        } else {
            return Inertia::render('Part/Index', [
                'parts' => $search['data'],
                'pagination' => $search,
            ]);
        }
    }
    public function store(Request $request)
    {
        $this->authorize('create', Part::class);
        return $this->partService->create($request);
    }
    public function update(Request $request, string $id)
    {
        $this->authorize('update', Part::class);
        return $this->partService->update($request, $id);
    }
    public function destroy(string $id)
    {
        $this->authorize('delete', Part::class);
        $this->partService->delete($id);

        return response()->noContent();
    }
    public function show(string $id)
    {
        $part = Part::findOrFail($id);
        return response()->json($part);
    }
}
