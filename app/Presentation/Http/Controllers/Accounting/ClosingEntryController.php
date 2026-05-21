<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\Accounting;

use App\Application\Accounting\ClosingEntry\Actions\GetClosingEntryListAction;
use App\Application\Accounting\ClosingEntry\Actions\GetClosingEntryByIdAction;
use App\Application\Accounting\ClosingEntry\Actions\CreateClosingEntryAction;
use App\Application\Accounting\ClosingEntry\Actions\UpdateClosingEntryAction;
use App\Application\Accounting\ClosingEntry\Actions\DeleteClosingEntryAction;
use App\Application\Accounting\ClosingEntry\DTOs\ClosingEntryDTO;
use App\Helpers\ResponseApiHelper;
use App\Infrastructure\Accounting\Exports\ClosingEntryExport;
use App\Presentation\Http\Requests\Accounting\StoreClosingEntryRequest;
use App\Presentation\Http\Requests\Accounting\UpdateClosingEntryRequest;
use App\Presentation\Http\Resources\Accounting\ClosingEntryResource;
use App\Presentation\Http\Resources\Accounting\ClosingEntryCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClosingEntryController
{
    public function __construct(
        private readonly GetClosingEntryListAction  $getListAction,
        private readonly GetClosingEntryByIdAction  $getByIdAction,
        private readonly CreateClosingEntryAction   $createAction,
        private readonly UpdateClosingEntryAction   $updateAction,
        private readonly DeleteClosingEntryAction   $deleteAction,
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $data = ($this->getListAction)(
                filters: $request->only(['search', 'sort', 'order', 'page']),
                perPage: (int) $request->get('per_page', 15),
            );
            return ResponseApiHelper::success('Data retrieved successfully.', new ClosingEntryCollection($data));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return ResponseApiHelper::success('Data retrieved successfully.', new ClosingEntryResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function store(StoreClosingEntryRequest $request): JsonResponse
    {
        try {
            $entity = ($this->createAction)(ClosingEntryDTO::fromArray($request->validated()));
            return ResponseApiHelper::created('ClosingEntry created successfully.', new ClosingEntryResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function update(UpdateClosingEntryRequest $request, int $id): JsonResponse
    {
        try {
            $entity = ($this->updateAction)($id, ClosingEntryDTO::fromArray($request->validated()));
            return ResponseApiHelper::success('ClosingEntry updated successfully.', new ClosingEntryResource($entity));
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            ($this->deleteAction)($id);
            return ResponseApiHelper::success('ClosingEntry deleted successfully.');
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function downloadPdf(int $id): Response|JsonResponse
    {
        try {
            $entity = ($this->getByIdAction)($id);
            return Pdf::loadView('pdf.accounting.closing_entries-detail', [
                'entity' => $entity,
                'title'  => 'Closing Entries Report',
            ])->setPaper('a4', 'portrait')->download("closing_entries-{$id}.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportPdf(Request $request): Response|JsonResponse
    {
        try {
            $data = ($this->getListAction)($request->only(['search']), 9999);
            return Pdf::loadView('pdf.accounting.closing_entries-list', [
                'items' => $data->items(),
                'title' => 'Closing Entries List',
            ])->setPaper('a4', 'landscape')->download("closing_entries-list.pdf");
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }

    public function exportExcel(Request $request): BinaryFileResponse|JsonResponse
    {
        try {
            return Excel::download(
                new ClosingEntryExport($request->only(['search'])),
                "closing_entries-export.xlsx"
            );
        } catch (\Throwable $e) {
            return ResponseApiHelper::error($e);
        }
    }
}
