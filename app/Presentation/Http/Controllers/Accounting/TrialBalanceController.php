<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Accounting;

use App\Application\Accounting\TrialBalance\Actions\GetTrialBalanceListAction;
use App\Application\Accounting\TrialBalance\Actions\GetTrialBalanceByIdAction;
use App\Application\Accounting\TrialBalance\Actions\CreateTrialBalanceAction;
use App\Application\Accounting\TrialBalance\Actions\UpdateTrialBalanceAction;
use App\Application\Accounting\TrialBalance\Actions\DeleteTrialBalanceAction;
use App\Application\Accounting\TrialBalance\DTOs\TrialBalanceDTO;
use App\Helpers\ResponseApiHelper;
use App\Infrastructure\Accounting\Exports\TrialBalanceExport;
use App\Presentation\Http\Requests\Accounting\StoreTrialBalanceRequest;
use App\Presentation\Http\Requests\Accounting\UpdateTrialBalanceRequest;
use App\Presentation\Http\Resources\Accounting\TrialBalanceResource;
use App\Presentation\Http\Resources\Accounting\TrialBalanceCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrialBalanceController
{
    public function __construct(
        private readonly GetTrialBalanceListAction  $getListAction,
        private readonly GetTrialBalanceByIdAction  $getByIdAction,
        private readonly CreateTrialBalanceAction   $createAction,
        private readonly UpdateTrialBalanceAction   $updateAction,
        private readonly DeleteTrialBalanceAction   $deleteAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = ($this->getListAction)(
                filters: $request->only(['search', 'sort', 'order', 'page']),
                perPage: (int) $request->get('per_page', 15),
            );
            return ResponseApiHelper::success('Data retrieved successfully.', new TrialBalanceCollection($data));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return ResponseApiHelper::success('Data retrieved successfully.', new TrialBalanceResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function store(StoreTrialBalanceRequest $request): JsonResponse
    {
        try {
            $entity = ($this->createAction)(TrialBalanceDTO::fromArray($request->validated()));
            return ResponseApiHelper::created('TrialBalance created successfully.', new TrialBalanceResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function update(UpdateTrialBalanceRequest $request, int $id): JsonResponse
    {
        try {
            $entity = ($this->updateAction)($id, TrialBalanceDTO::fromArray($request->validated()));
            return ResponseApiHelper::success('TrialBalance updated successfully.', new TrialBalanceResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            ($this->deleteAction)($id);
            return ResponseApiHelper::success('TrialBalance deleted successfully.');
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function downloadPdf(int $id): Response|JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return Pdf::loadView('pdf.accounting.trial_balances-detail', [
                'entity' => $entity,
                'title'  => 'Trial Balances Report',
            ])->setPaper('a4', 'portrait')->download("trial_balances-{$id}.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportPdf(Request $request): Response|JsonResponse
    {
        try {
            $data = ($this->getListAction)($request->only(['search']), 9999);
            return Pdf::loadView('pdf.accounting.trial_balances-list', [
                'items' => $data->items(),
                'title' => 'Trial Balances List',
            ])->setPaper('a4', 'landscape')->download("trial_balances-list.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportExcel(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            return Excel::download(
                new TrialBalanceExport($request->only(['search'])),
                "trial_balances-export.xlsx"
            );
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }
}
