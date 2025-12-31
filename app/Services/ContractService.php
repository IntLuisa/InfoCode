<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractService
{
    public function __construct(
        private PdfService $pdfService,
    ) {
    }

    public function createOrUpdate(Request $request)
    {
        $data = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'contract' => 'required|string',
            'approved' => 'required|boolean',
        ]);

        try {
            return DB::transaction(function () use ($data) {
                $contract = Contract::updateOrCreate(
                    ['budget_id' => $data['budget_id']],
                    [
                        'contract' => $data['contract'],
                        'approved' => $data['approved'],
                    ]
                );

                return $contract;
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function pdf(string $id)
    {
        $contract = Contract::findOrFail($id);

        return $this->pdfService->generatePdf(
            'contract',
            [
                'contract' => $contract,
            ],
            [
                'margin_header' => 6,
                'margin_right' => 6,
                'margin_left' => 6,
                'margin_top' => 36,
                'margin_bottom' => 20,
            ],
            $this->pdfService->getHtmlHeader($id),
            true,
            [],
        )->stream("CO_{$contract->budget->client_code}_{$contract->budget->client_name}_{$contract->budget_id}.pdf");
    }
}
