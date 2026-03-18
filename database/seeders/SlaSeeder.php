<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Priority;
use App\Models\Sla;
use Illuminate\Database\Seeder;

class SlaSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();

        $map = [
            'baixa' => [240, 2880],
            'media' => [120, 1440],
            'alta' => [60, 480],
            'critica' => [30, 120],
        ];

        foreach ($map as $slug => [$firstResponse, $resolution]) {
            $priority = Priority::where('slug', $slug)->first();

            if (! $priority) {
                continue;
            }

            Sla::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'priority_id' => $priority->id,
                ],
                [
                    'first_response_minutes' => $firstResponse,
                    'resolution_minutes' => $resolution,
                    'active' => true,
                ]
            );
        }
    }
}