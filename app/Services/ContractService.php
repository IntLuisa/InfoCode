<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Notification;
use App\Models\Workflow;
use App\Models\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContractService
{
    public function __construct(
        private PdfService $pdfService,
    ) {}

    public function createOrUpdate(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'contract' => 'required|string',
            'approved' => 'required|boolean',
            'type' => 'required|in:krenke,topotec',
        ]);

        try {
            return DB::transaction(function () use ($data) {
                $contract = Contract::updateOrCreate(
                    ['service_id' => $data['service_id']],
                    [
                        'contract' => $data['contract'],
                        'approved' => $data['approved'],
                        'type' => $data['type'],
                    ]
                );

                $current_step = 6;
                $workflow_id = Workflow::where('order', $current_step)->first()->id;
                $workflowService = WorkflowService::updateOrCreate([
                    'workflow_id' => $workflow_id,
                    'service_id' => $data['service_id'],
                ], [
                    'workflow_id' => $workflow_id,
                    'service_id' => $data['service_id'],
                    'user_id' => Auth::user()->id,
                    'finished_at' => $data['approved'] ? now() : null,
                ]);

                $nextWorkflow = Workflow::where('order', $current_step + ($data['approved'] ? 1 : 0))->first();

                if ($data['approved']) {
                    Notification::withoutGlobalScope('user_notifications')
                        ->where('service_id', $data['service_id'])
                        ->where('order', $current_step)
                        ->delete();

                    if ($nextWorkflow) {
                        Notification::create([
                            'user_id' => $workflowService->service->user_id,
                            'notification' => $nextWorkflow->step,
                            'service_id' => $data['service_id'],
                            'url' => route('processes.index', ['service_id' => $data['service_id']]),
                            'order' => $nextWorkflow->order,
                            'sector' => $nextWorkflow->sector,
                        ]);
                    }
                } else {
                    if (!Notification::where('service_id', $data['service_id'])->where('order', $current_step)->first()) {
                        Notification::withoutGlobalScope('user_notifications')
                            ->where('service_id', $data['service_id'])
                            ->where('order', $current_step + 1)
                            ->delete();

                        Notification::create([
                            'notification' => $nextWorkflow->step,
                            'service_id' => $data['service_id'],
                            'url' => route('services.index', ['likes' => [['id' => $data['service_id']]]]),
                            'order' => $nextWorkflow->order,
                            'sector' => $nextWorkflow->sector,
                        ]);
                    }
                }

                return $contract;
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function pdf(string $id, string $type = 'krenke')
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
            $this->pdfService->getHtmlHeader($type, $id),
            true,
            [],
            $type
        )->stream("CO_{$contract->service->client_code}_{$contract->service->client_name}_{$contract->service_id}.pdf");
    }
}
