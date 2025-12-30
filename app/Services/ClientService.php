<?php

namespace App\Services;

use App\Filters\ClientFilter;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientService
{
    public function search(Request $request): mixed
    {
        $builder = Client::query();

        return ClientResource::collection((new ClientFilter($request, $builder))
            ->getBuilder()->appends($request->query()))->resource->toArray();
    }

    public function create(Request $request): Client|array
    {
        $data = $request->all();
        $client = Client::create($data);
        return $client;
    }

    public function update(Request $request, string $id): Client
    {
        $client = Client::findOrFail($id);
        $data = $request->all();
        $client->update($data);
        $client->refresh();

        return $client;
    }
}
