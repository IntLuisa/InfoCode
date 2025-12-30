<?php

namespace App\Http\Controllers;

use App\Models\Part;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use App\Services\PlaygroundService;
use Illuminate\Support\Facades\DB;
use App\Models\Playground;
use Illuminate\Support\Collection;

class PlaygroundController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private PlaygroundService $playground)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Playground::class);

        $playgroundsData = $this->playground->search($request);

        $playgrounds = $playgroundsData['data'];

        if ($playgrounds instanceof Collection) {
            $playgrounds->load('items.part');
        }

        if ($request->has('json')) {
            return $playgroundsData['data'];
        }

        return Inertia::render('Playground/Index', [
            'playground' => $playgrounds,
            'pagination' => $playgroundsData,
            'items' => Part::all(),
        ]);
    }


    public function store(Request $request)
    {
        $this->authorize('create', Playground::class);

        return $this->playground->create($request);
    }

    public function storeItem(Request $request, string $id)
    {
        return $this->playground->createItem($request, $id);
    }

    public function update(Request $request, $id, PlaygroundService $service)
    {
        $this->authorize('update', Playground::class);

        $playground = Playground::findOrFail($id);
        return $service->update($request, $playground);
    }
    public function destroy(string $id)
    {
        return $this->playground->delete($id);
    }

    public function destroyItem($pivot)
    {
        $this->authorize('delete', Playground::class);

        DB::table('playground_items')->where('id', $pivot)->delete();
        return back()->with('success', 'Item removido com sucesso.');
    }

    public function updateQuantity(Request $request, $id)
    {
        return $this->playground->updateQuantity($request, $id);
    }

    public function duplicate(Request $request, string $id)
    {
        return $this->playground->duplicate($id);
    }
    public function batchUpdateQuantities(Request $request)
    {
        return app(PlaygroundService::class)->batchUpdateQuantities($request);
    }
    public function show($id)
    {
        $playground = Playground::with('items')->findOrFail($id);
        return response()->json($playground);
    }

}
