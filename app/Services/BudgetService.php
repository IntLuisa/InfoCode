<?php

namespace App\Services;

use App\Filters\BudgetFilter;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BudgetService
{
    // public function __construct(private PdfService $pdfService) {}

    public function search(Request $request)
    {
        $builder = Budget::with('client');

        $builder = (new BudgetFilter($request, $builder))
            ->getBuilder();

        return $builder
            // ->paginate(15)
            ->appends($request->query());
    }


    public function store(Request $request)
    {
        $data = $request->all();

        try {
            return DB::transaction(function () use ($data) {
                $budget = Budget::create(array_merge($data, [
                    'user_id' => Auth::user()->id,
                ]));

                return $budget->load('client');
            });
        } catch (\Exception $e) {
            Log::error('Erro ao criar orçamento: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(Request $request, string $id): Budget
    {
        $data = $request->all();

        return DB::transaction(function () use ($data, $id) {
            $budget = Budget::findOrFail($id);
            $budget->update($data);


            return $budget->load('client');
        });
    }

    public function delete(string $id): bool
    {
        $budget = Budget::findOrFail($id);
        return $budget->delete();
    }

    public function duplicate(string $id): Budget
    {
        $original = Budget::with('client')->findOrFail($id);

        $copy = $original->replicate();
        $copy->status = 'pending';
        $copy->save();

        foreach ($original->items as $item) {
            $itemCopy = $item->replicate();
            $itemCopy->budget_id = $copy->id;
            $itemCopy->save();
        }

        return $copy->load('logs');
    }

    // public function generatePdfByType(string $id, ?string $type = 'krenke', array $coverFiles = [])
    // {
    //     $budget = Budget::with('items', 'client')->findOrFail($id);

    //     return $this->pdfService->generatePdf(
    //         'budget',
    //         [
    //             'budget' => $budget,
    //             'budgetItems' => $budget->items,
    //         ],
    //         [
    //             'margin_right' => 6,
    //             'margin_header' => 6,
    //             'margin_left' => 6,
    //             'margin_top' => 36,
    //             'margin_bottom' => 10,
    //         ],
    //         $this->pdfService->getHtmlHeader($type, $id),
    //         true,
    //         $coverFiles,
    //         $type,
    //     )->stream("PO_{$budget->client->code}_{$budget->client->name}_{$budget->id}");
    // }
}
