<?php

namespace App\Http\Controllers\Concerns;

use App\Services\EditableSalesDocumentService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

trait HandlesEditableDocumentCrud
{
    /**
     * @param  array<string, mixed>  $extraViewData
     */
    protected function editableDocumentEditView(
        Model $document,
        object $revision,
        string $view,
        string $documentVariable,
        array $editableStatuses,
        array $extraViewData = [],
    ): View {
        if (! in_array($revision->status, $editableStatuses, true)) {
            abort(422, 'ไม่สามารถแก้ไขเอกสารในสถานะนี้ได้');
        }

        if (method_exists($document, 'partner')) {
            $document->loadMissing(['partner.contacts', 'partner.branches']);
        }

        $partner = $document->partner ?? null;
        if (! $partner && method_exists($document, 'taxInvoice')) {
            $document->loadMissing(['taxInvoice.partner.contacts', 'taxInvoice.partner.branches']);
            $partner = $document->taxInvoice?->partner;
        }
        if (! $partner && method_exists($document, 'salesOrder')) {
            $document->loadMissing(['salesOrder.partner.contacts', 'salesOrder.partner.branches']);
            $partner = $document->salesOrder?->partner;
        }

        if (method_exists($revision, 'lines')) {
            $revision->loadMissing('lines');
        }

        if (method_exists($revision, 'paymentTerms')) {
            $revision->loadMissing('paymentTerms');
        }

        /** @var EditableSalesDocumentService $editable */
        $editable = $this->editable;

        return view($view, array_merge(
            $editable->formViewDataFromRevision(
                $partner,
                $this->createBy(),
                $revision,
            ),
            [
                $documentVariable => $document,
                'revision' => $revision,
                'standalone' => true,
                'seedLines' => old('line')
                    ? $editable->seedLinesFromOldInput()
                    : $editable->seedLinesFromRevision($revision),
            ],
            $extraViewData,
        ));
    }

    /**
     * @param  array<string, mixed>  $extraRules
     * @param  callable(array): array<string, mixed>  $revisionFieldsFromData
     * @param  class-string<Model>  $lineModelClass
     * @param  callable|null  $afterPersist
     */
    protected function editableDocumentUpdate(
        Request $request,
        Model $document,
        object $revision,
        array $editableStatuses,
        array $extraRules,
        callable $revisionFieldsFromData,
        string $lineModelClass,
        string $revisionForeignKey,
        string $showRouteName,
        bool $withPayment = false,
        ?callable $afterPersist = null,
    ): RedirectResponse {
        if (! in_array($revision->status, $editableStatuses, true)) {
            throw ValidationException::withMessages(['status' => 'ไม่สามารถแก้ไขเอกสารในสถานะนี้ได้']);
        }

        /** @var EditableSalesDocumentService $editable */
        $editable = $this->editable;

        $data = $request->validate($editable->payloadRules($extraRules, withPayment: $withPayment));

        $partner = $editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $editable->filterLines(
            $editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        DB::transaction(function () use ($document, $revision, $partner, $data, $lines, $editable, $lineModelClass, $revisionForeignKey, $revisionFieldsFromData, $afterPersist) {
            if (in_array('partner_id', $document->getFillable(), true)) {
                $document->partner_id = $partner?->id;
                $document->partner_branch_id = $partner?->branches()->value('id');
                $document->partner_contact_id = $partner?->contacts()->value('id');
                $document->save();
            }

            $revision->revision_no = ((int) ($revision->revision_no ?? 1)) + 1;
            $revision->fill(array_merge(
                $revisionFieldsFromData($data),
                $editable->partyFieldsFromRequest($data),
                $editable->totalsFieldsFromRequest($data),
            ));
            $revision->save();


            $editable->replaceLinesOnRevision(
                $revision,
                $lines,
                $data,
                $lineModelClass,
                $revisionForeignKey,
            );

            if ($afterPersist) {
                $afterPersist($revision, $data);
            }
        });

        return redirect()->route($showRouteName, $document)
            ->with('success', 'บันทึกการแก้ไขเรียบร้อยแล้ว');
    }

    protected function editableDocumentDestroy(
        Model $document,
        object $revision,
        string $indexRouteName,
        array $deletableStatuses = ['draft'],
    ): RedirectResponse {
        if (! in_array($revision->status, $deletableStatuses, true)) {
            throw ValidationException::withMessages(['status' => 'ลบได้เฉพาะเอกสารสถานะ draft']);
        }

        $document->delete();

        return redirect()->route($indexRouteName)
            ->with('success', 'ลบเอกสารเรียบร้อยแล้ว');
    }

    protected function editableDocumentVoid(
        object $revision,
        array $voidableStatuses = ['draft', 'issued'],
        string $voidStatus = 'voided',
    ): RedirectResponse {
        if (! in_array($revision->status, $voidableStatuses, true)) {
            throw ValidationException::withMessages(['status' => 'ยกเลิกไม่ได้สำหรับสถานะนี้']);
        }

        $revision->status = $voidStatus;
        if (in_array('voided_at', $revision->getFillable(), true)) {
            $revision->voided_at = now();
        }
        $revision->save();

        return back()->with('success', 'ยกเลิกเอกสารเรียบร้อยแล้ว');
    }
}
