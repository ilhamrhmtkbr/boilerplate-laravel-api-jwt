<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Accounting;

use App\Application\Accounting\GeneralLedger\Actions\GetGeneralLedgerListAction;
use App\Application\Accounting\GeneralLedger\Actions\GetGeneralLedgerByIdAction;
use App\Application\Accounting\GeneralLedger\Actions\CreateGeneralLedgerAction;
use App\Application\Accounting\GeneralLedger\Actions\UpdateGeneralLedgerAction;
use App\Application\Accounting\GeneralLedger\Actions\DeleteGeneralLedgerAction;
use App\Application\Accounting\GeneralLedger\DTOs\GeneralLedgerDTO;
use App\Helpers\ResponseApiHelper;
use App\Infrastructure\Accounting\Exports\GeneralLedgerExport;
use App\Presentation\Http\Requests\Accounting\StoreGeneralLedgerRequest;
use App\Presentation\Http\Requests\Accounting\UpdateGeneralLedgerRequest;
use App\Presentation\Http\Resources\Accounting\GeneralLedgerResource;
use App\Presentation\Http\Resources\Accounting\GeneralLedgerCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GeneralLedgerController
{
    public function __construct(
        private readonly GetGeneralLedgerListAction  $getListAction,
        private readonly GetGeneralLedgerByIdAction  $getByIdAction,
        private readonly CreateGeneralLedgerAction   $createAction,
        private readonly UpdateGeneralLedgerAction   $updateAction,
        private readonly DeleteGeneralLedgerAction   $deleteAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = ($this->getListAction)(
                filters: $request->only(['search', 'sort', 'order', 'page']),
                perPage: (int) $request->get('per_page', 15),
            );
            return ResponseApiHelper::success('Data retrieved successfully.', new GeneralLedgerCollection($data));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return ResponseApiHelper::success('Data retrieved successfully.', new GeneralLedgerResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function store(StoreGeneralLedgerRequest $request): JsonResponse
    {
        try {
            $entity = ($this->createAction)(GeneralLedgerDTO::fromArray($request->validated()));
            return ResponseApiHelper::created('GeneralLedger created successfully.', new GeneralLedgerResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function update(UpdateGeneralLedgerRequest $request, int $id): JsonResponse
    {
        try {
            $entity = ($this->updateAction)($id, GeneralLedgerDTO::fromArray($request->validated()));
            return ResponseApiHelper::success('GeneralLedger updated successfully.', new GeneralLedgerResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            ($this->deleteAction)($id);
            return ResponseApiHelper::success('GeneralLedger deleted successfully.');
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function downloadPdf(int $id): Response|JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return Pdf::loadView('pdf.accounting.general_ledgers-detail', [
                'entity' => $entity,
                'title'  => 'General Ledgers Report',
            ])->setPaper('a4', 'portrait')->download("general_ledgers-{$id}.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportPdf(Request $request): Response|JsonResponse
    {
        try {
            $data = ($this->getListAction)($request->only(['search']), 9999);
            return Pdf::loadView('pdf.accounting.general_ledgers-list', [
                'items' => $data->items(),
                'title' => 'General Ledgers List',
            ])->setPaper('a4', 'landscape')->download("general_ledgers-list.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportExcel(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            return Excel::download(
                new GeneralLedgerExport($request->only(['search'])),
                "general_ledgers-export.xlsx"
            );
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }
}
