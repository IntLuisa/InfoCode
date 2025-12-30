<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;

class ClientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private ClientService $clientService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Client::class);
        $search = $this->clientService->search($request);

        if ($request->has('json')) {
            return $search;
        } else {
            return Inertia::render('Client/Index', [
                'clients' => $search['data'],
                'pagination' => $search,
            ]);
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create', Client::class);

        return $this->clientService->create($request);
    }

    public function update(Request $request, string $id)
    {
        $this->authorize('update', Client::class);

        return $this->clientService->update($request, $id);
    }

    public function destroy(string $id)
    {
        $this->authorize('delete', Client::class);

        $client = Client::findOrFail($id);
        return $client->delete();
    }

    public function restore(string $id)
    {
        $this->authorize('restore', Client::class);
        $client = Client::withTrashed()->findOrFail($id);
        return $client->restore();
    }
}
