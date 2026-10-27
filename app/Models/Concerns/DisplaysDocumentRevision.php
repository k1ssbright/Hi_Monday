<?php

namespace App\Models\Concerns;

trait DisplaysDocumentRevision
{
    public function displayNo(?int $revisionNo = null): string
    {
        $number = $this->documentNumber();
        if (! filled($number)) {
            return '';
        }

        $rev = $revisionNo ?? $this->currentRevision?->revision_no ?? 1;

        return $number.'-R'.$rev;
    }

    protected function documentNumber(): ?string
    {
        $attribute = property_exists($this, 'documentNumberAttribute')
            ? $this->documentNumberAttribute
            : null;

        if (! is_string($attribute) || $attribute === '') {
            return null;
        }

        $value = $this->getAttribute($attribute);

        return is_string($value) ? $value : null;
    }
}
