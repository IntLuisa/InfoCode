<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Services\ContractService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ContractController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected ContractService $contractService) {}

    public function store(Request $request)
    {
        $this->authorize('create', Contract::class);
        return $this->contractService->createOrUpdate($request);
    }

    public function pdf(Request $request, string $id)
    {
        $type = $request->get('type', 'krenke');
        return $this->contractService->pdf($id, $type);
    }
}
