<?php

namespace App\Services;

use App\Filters\PlaygroundFilter;
use App\Http\Resources\PlaygroundResource;
use App\Models\Part;
use App\Models\Playground;
use Illuminate\Http\Request;
use App\Models\ItemPlayground;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class PlaygroundService
{
    protected ImageService $imageService;
    public function __construct()
    {
        $this->imageService = new ImageService();
    }

    public function search(Request $request): array
    {
        $builder = Playground::with('items');

        return PlaygroundResource::collection((new PlaygroundFilter($request, $builder))
            ->getBuilder()->appends($request->query()))->resource->toArray();
    }

    public function create(Request $request): Playground
    {
        $data = $request->all();
        $data['picture'] = (new ImageService)->handleUpload($request, 'picture', 'images/items');
        return Playground::create($data);
    }


    public function update(Request $request, Playground $playground)
    {
        if ($request->boolean('shouldDeleteImage')) {
            if ($playground->picture) {
                Storage::delete($playground->picture);
                $playground->picture = null;
            }
        }

        if ($request->hasFile('picture')) {
            $playground->picture = $this->imageService->handleUpload($request, 'picture', 'pictures/items');
        }

        $playground->name = $request->name;
        $playground->code = $request->code;
        $playground->discount = $request->discount;
        $playground->discount_factory = $request->discount_factory;
        $playground->save();

        return response()->json($playground);
    }

    public function createItem(Request $request, string $playgroundId)
    {
        $playground = Playground::findOrFail($playgroundId);
        $data = $request->only(['id', 'code', 'name', 'amount']);

        if (!empty($data['id'])) {
            $part = Part::findOrFail($data['id']);
            $playground->items()->attach($part->id, ['quantity' => 1]);

            $pivot = ItemPlayground::where('playground_id', $playground->id)
                ->where('item_id', $part->id)
                ->latest('id')
                ->first();

            return response()->json([
                'id' => $part->id,
                'name' => $part->name,
                'code' => $part->code,
                'amount' => $part->amount,
                'pivot_id' => $pivot->id,
                'quantity' => $pivot->quantity,
            ]);
        }

        $part = new Part();
        $part->code = $data['code'];
        $part->name = $data['name'];
        $part->amount = $data['amount'];
        $part->save();

        $playground->items()->attach($part->id, ['quantity' => 1]);

        $pivot = ItemPlayground::where('playground_id', $playground->id)
            ->where('part_id', $part->id)
            ->latest('id')
            ->first();

        return response()->json([
            'id' => $part->id,
            'name' => $part->name,
            'code' => $part->code,
            'amount' => $part->amount,
            'pivot_id' => $pivot->id,
            'quantity' => $pivot->quantity,
        ]);
    }


    public function updateItem(Request $request, string $id): Part
    {
        $item = Part::findOrFail($id);
        $item->update($request->all());

        return $item;
    }

    public function delete(string $id): bool
    {
        $playground = Playground::findOrFail($id);
        return $playground->delete();
    }

    public function destroyItem($id)
    {
        $pivotItem = ItemPlayground::find($id);

        if (!$pivotItem) {
            return response()->json(['error' => 'Item não encontrado'], 404);
        }

        $pivotItem->delete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('picture')) {
            $data['picture'] = $this->imageService->handleUpload($request, 'picture', 'pictures/items');
        }

        $playground = Playground::create($data);

        return response()->json($playground);
    }

    public function updateQuantity(Request $request, $pivotId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $pivotItem = ItemPlayground::findOrFail($pivotId);
        $pivotItem->quantity = $request->quantity;
        $pivotItem->save();

        return response()->json([
            'message' => 'Quantidade atualizada com sucesso!',
            'item' => $pivotItem
        ]);
    }

    public function batchUpdateQuantities(Request $request)
    {
        $request->validate([
            'updates' => 'required|array',
            'updates.*.pivot_id' => 'required|integer|exists:playground_items,id',
            'updates.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $updatedItems = [];

        DB::transaction(function () use ($request, &$updatedItems) {
            foreach ($request->updates as $update) {
                $pivotItem = ItemPlayground::findOrFail($update['pivot_id']);
                $pivotItem->update(['quantity' => $update['quantity']]);
                $updatedItems[] = $pivotItem;
            }
        });

        return response()->json([
            'message' => "Sucesso! " . count($updatedItems) . " item(ns) atualizado(s)",
            'updated_count' => count($updatedItems),
            'items' => $updatedItems
        ]);
    }


    public function duplicate(string $id): Playground
    {
        $original = Playground::with([
            'items' => function ($query) {
                $query->withPivot('quantity');
            }
        ])->findOrFail($id);

        $copy = $original->replicate();
        $copy->code = $original->code . '-E';
        $copy->name = $original->name . ' - Especial';
        $copy->picture = null;
        $copy->save();

        $itemsToSync = [];
        foreach ($original->items as $item) {
            $itemsToSync[$item->id] = [
                'quantity' => $item->pivot->quantity ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($itemsToSync)) {
            $copy->items()->sync($itemsToSync);
        }

        return $copy->load([
            'items' => function ($query) {
                $query->withPivot('quantity');
            }
        ]);
    }

    public function getThumbUrls(array|Collection $playgrounds): array
    {
        return collect($playgrounds)->map(function ($playground) {
            return [
                'id' => $playground->id,
                'thumb_url' => $playground->thumb ? url('storage/' . $playground->thumb->filename) : null,
            ];
        })->all();
    }
}
