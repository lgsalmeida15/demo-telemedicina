<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Plan;

class PlanUuidSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // percorre todas as empresas sem UUID
        Plan::whereNull('uuid')
        ->orWhere('uuid', '')
        ->chunk(100, function ($plans) {
            foreach ($plans as $plan) {
                $plan->uuid = Str::uuid()->toString();
                $plan->save();
            }
        });
    }
}
