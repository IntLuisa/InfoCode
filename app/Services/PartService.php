<?php

namespace App\Services;

use App\Filters\PartFilter;
use App\Http\Resources\PartResource;
use App\Models\Part;
use Illuminate\Http\Request;

class PartService
{
    public function search(Request $request): mixed
    {
        $builder = Part::query();

        return PartResource::collection((new PartFilter($request, $builder))
            ->getBuilder()->appends($request->query()))->resource->toArray();
    }

    public function create(Request $request): Part|array
    {
        $data = $request->all();
        if (isset($data['ipiNaoAplica']) && $data['ipiNaoAplica']) {
            $data['ipiPercentage'] = 0;
        }
        $data['picture'] = (new ImageService)->handleUpload($request, 'picture', 'images/items');

        $part = Part::create($data);
        return $part;
    }

    public function update(Request $request, string $id): Part
    {
        $part = Part::findOrFail($id);

        $data = $request->all();

        if (isset($data['ipiNaoAplica']) && $data['ipiNaoAplica']) {
            $data['ipiPercentage'] = 0;
        }

        if ($request->boolean('shouldDeleteImage') && $part->picture) {
            \Illuminate\Support\Facades\Storage::delete($part->picture);
            $part->picture = null;
        }

        if ($request->hasFile('picture')) {
            $part->picture = (new \App\Services\ImageService)
                ->handleUpload($request, 'picture', 'images/items');
        }

        $part->code = $data['code'] ?? $part->code;
        $part->name = $data['name'] ?? $part->name;
        $part->amount = $data['amount'] ?? $part->amount;
        $part->amountFabric = $data['amountFabric'] ?? $part->amountFabric;
        $part->ipiPercentage = $data['ipiPercentage'] ?? $part->ipiPercentage;
        $part->kits = $data['kits'] ?? $part->kits;

        $part->save();

        return $part;
    }

    public function delete(string $id): bool
    {
        return (Part::findOrFail($id))->delete();
    }
}
