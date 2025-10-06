<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contract;
use App\Services\ContractTermsService;

class GenerateContractTerms extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'contracts:generate-terms {--all : Regenerate terms for all contracts} {--missing : Only generate for contracts without terms}';

    /**
     * The console command description.
     */
    protected $description = 'Generate contract terms and conditions for contracts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting contract terms generation...');

        if ($this->option('all')) {
            $contracts = Contract::with(['vendor', 'bid'])->get();
            $this->info("Regenerating terms for all {$contracts->count()} contracts...");
        } elseif ($this->option('missing')) {
            $contracts = Contract::with(['vendor', 'bid'])
                ->where(function($query) {
                    $query->whereNull('terms')
                          ->orWhere('terms', '')
                          ->orWhere('terms', 'No terms specified');
                })
                ->get();
            $this->info("Generating terms for {$contracts->count()} contracts without terms...");
        } else {
            $this->error('Please specify either --all or --missing option');
            return 1;
        }

        $progressBar = $this->output->createProgressBar($contracts->count());
        $progressBar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($contracts as $contract) {
            try {
                $contractTerms = ContractTermsService::generateContractTerms($contract);
                $contract->update(['terms' => $contractTerms]);
                $successCount++;
            } catch (\Exception $e) {
                $this->error("\nError generating terms for contract {$contract->contract_number}: " . $e->getMessage());
                $errorCount++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();

        $this->newLine(2);
        $this->info("Contract terms generation completed!");
        $this->info("✅ Successfully processed: {$successCount} contracts");
        
        if ($errorCount > 0) {
            $this->error("❌ Errors encountered: {$errorCount} contracts");
        }

        return 0;
    }
}
