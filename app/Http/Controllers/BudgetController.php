<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetRequest;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Services\PdfService;

class BudgetController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private BudgetService $budgetService)
    {
    }

    public function index(Request $request)
    {
        $budgets = $this->budgetService->search($request);

        return Inertia::render('Budget/Index', [
            'budgets' => BudgetResource::collection($budgets),
        ]);
    }

    public function store(BudgetRequest $request)
    {
        $this->authorize('create', Budget::class);

        $this->budgetService->store($request);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Projeto criado com sucesso!');
    }

    public function update(BudgetRequest $request, string $id)
    {
        $this->authorize('update', Budget::class);

        $this->budgetService->update($request, $id);

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Projeto atualizado com sucesso!');
    }


    public function destroy(string $id)
    {
        $this->authorize('delete', Budget::class);

        $this->budgetService->delete($id);

        return redirect()
            ->back()
            ->with('success', 'Projeto removido com sucesso!');
    }

    public function duplicate(Request $request, string $id)
    {
        return $this->budgetService->duplicate($id);
    }

    // public function pdf(string $id, Request $request)
    // {
    //     $coverFiles = $request->get('cover') === 'true' ? array_merge(
    //         [
    //             'assets/images/covers/1.png',
    //             'assets/images/covers/2.png',
    //             'assets/images/covers/3.png',
    //         ],
    //         PdfService::base64ToTempFile($request->input('coverImages')[0] ?? '')
    //     ) : [];


    //     return $this->budgetService->generatePdfByType($id, $request->get('type', 'krenke'), $coverFiles);
    // }

    // public function pdf(string $id, Request $request)
    // {
    //     $coverFiles = $request->get('cover') === 'true' ? array_merge(
    //         [
    //             'assets/images/covers/1.png',
    //             'assets/images/covers/2.png',
    //             'assets/images/covers/3.png',
    //         ],
    //         PdfService::base64ToTempFile($request->input('coverImages')[0] ?? '')
    //     ) : [];

    //     return $this->budgetService->generatePdfByType(
    //         $id,
    //         $request->get('type', 'krenke'),
    //         $coverFiles
    //     );
    // }



}
