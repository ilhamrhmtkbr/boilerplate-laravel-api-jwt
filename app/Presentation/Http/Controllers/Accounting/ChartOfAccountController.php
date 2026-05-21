<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Accounting;

use App\Application\Accounting\ChartOfAccount\Actions\GetChartOfAccountListAction;
use App\Application\Accounting\ChartOfAccount\Actions\GetChartOfAccountByIdAction;
use App\Application\Accounting\ChartOfAccount\Actions\CreateChartOfAccountAction;
use App\Application\Accounting\ChartOfAccount\Actions\UpdateChartOfAccountAction;
use App\Application\Accounting\ChartOfAccount\Actions\DeleteChartOfAccountAction;
use App\Application\Accounting\ChartOfAccount\DTOs\ChartOfAccountDTO;
use App\Helpers\ResponseApiHelper;
use App\Infrastructure\Accounting\Exports\ChartOfAccountExport;
use App\Presentation\Http\Requests\Accounting\StoreChartOfAccountRequest;
use App\Presentation\Http\Requests\Accounting\UpdateChartOfAccountRequest;
use App\Presentation\Http\Resources\Accounting\ChartOfAccountResource;
use App\Presentation\Http\Resources\Accounting\ChartOfAccountCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ChartOfAccountController
{
    public function __construct(
        private readonly GetChartOfAccountListAction  $getListAction,
        private readonly GetChartOfAccountByIdAction  $getByIdAction,
        private readonly CreateChartOfAccountAction   $createAction,
        private readonly UpdateChartOfAccountAction   $updateAction,
        private readonly DeleteChartOfAccountAction   $deleteAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = ($this->getListAction)(
                filters: $request->only(['search', 'sort', 'order', 'page']),
                perPage: (int) $request->get('per_page', 15),
            );
            return ResponseApiHelper::success('Data retrieved successfully.', new ChartOfAccountCollection($data));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return ResponseApiHelper::success('Data retrieved successfully.', new ChartOfAccountResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function store(StoreChartOfAccountRequest $request): JsonResponse
    {
        try {
            $entity = ($this->createAction)(ChartOfAccountDTO::fromArray($request->validated()));
            return ResponseApiHelper::created('ChartOfAccount created successfully.', new ChartOfAccountResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function update(UpdateChartOfAccountRequest $request, int $id): JsonResponse
    {
        try {
            $entity = ($this->updateAction)($id, ChartOfAccountDTO::fromArray($request->validated()));
            return ResponseApiHelper::success('ChartOfAccount updated successfully.', new ChartOfAccountResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            ($this->deleteAction)($id);
            return ResponseApiHelper::success('ChartOfAccount deleted successfully.');
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function downloadPdf(int $id): Response|JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return Pdf::loadView('pdf.accounting.chart_of_accounts-detail', [
                'entity' => $entity,
                'title'  => 'Chart Of Accounts Report',
            ])->setPaper('a4', 'portrait')->download("chart_of_accounts-{$id}.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportPdf(Request $request): Response|JsonResponse
    {
        try {
            $data = ($this->getListAction)($request->only(['search']), 9999);
            return Pdf::loadView('pdf.accounting.chart_of_accounts-list', [
                'items' => $data->items(),
                'title' => 'Chart Of Accounts List',
            ])->setPaper('a4', 'landscape')->download("chart_of_accounts-list.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportExcel(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            return Excel::download(
                new ChartOfAccountExport($request->only(['search'])),
                "chart_of_accounts-export.xlsx"
            );
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }
}
