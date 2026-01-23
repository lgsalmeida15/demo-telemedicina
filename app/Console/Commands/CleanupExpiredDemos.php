<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DemoBeneficiaryService;

class CleanupExpiredDemos extends Command
{
    protected $signature = 'demo:cleanup {--days=7 : Dias após expiração para remover}';
    protected $description = 'Remove beneficiários demo expirados há mais de X dias';
    
    protected $demoService;
    
    public function __construct(DemoBeneficiaryService $demoService)
    {
        parent::__construct();
        $this->demoService = $demoService;
    }
    
    public function handle()
    {
        $days = (int) $this->option('days');
        
        $this->info("Removendo beneficiários demo expirados há mais de {$days} dias...");
        
        $count = $this->demoService->cleanupExpiredDemos($days);
        
        $this->info("✓ {$count} beneficiário(s) demo removido(s).");
        
        return 0;
    }
}

