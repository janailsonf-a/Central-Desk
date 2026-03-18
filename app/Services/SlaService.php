<?php

namespace App\Services;

use App\Models\Priority;
use App\Models\Sla;

class SlaService
{
    public function calculateDueAt(int $companyId, Priority $priority): ?\Illuminate\Support\Carbon
    {
        $sla = Sla::where('company_id', $companyId)
            ->where('priority_id', $priority->id)
            ->where('active', true)
            ->first();

        if (! $sla) {
            return null;
        }

        return now()->addMinutes($sla->resolution_minutes);
    }

    public function isOverdue(?\Illuminate\Support\Carbon $dueAt): bool
    {
        if (! $dueAt) {
            return false;
        }

        return now()->greaterThan($dueAt);
    }
}