<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(\App\Repositories\BeneficiaryRepository::class, \App\Repositories\BeneficiaryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PartnerRepository::class, \App\Repositories\PartnerRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PassagemRepository::class, \App\Repositories\PassagemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ConvenioRepository::class, \App\Repositories\ConvenioRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AssociacaoRepository::class, \App\Repositories\AssociacaoRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AutoridadeRepository::class, \App\Repositories\AutoridadeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AutorizacaoCompraRepository::class, \App\Repositories\AutorizacaoCompraRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ContaPagarRepository::class, \App\Repositories\ContaPagarRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CartaoCreditoRepository::class, \App\Repositories\CartaoCreditoRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FinancialRepository::class, \App\Repositories\FinancialRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CostCenterRepository::class, \App\Repositories\CostCenterRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CaixaRepository::class, \App\Repositories\CaixaRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ContaReceberRepository::class, \App\Repositories\ContaReceberRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ConveniosCategoriaRepository::class, \App\Repositories\ConveniosCategoriaRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CompanyRepository::class, \App\Repositories\CompanyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PartnerCompanyRepository::class, \App\Repositories\PartnerCompanyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PlanRepository::class, \App\Repositories\PlanRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PlanConvenioRepository::class, \App\Repositories\PlanConvenioRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ConvenioTypeRepository::class, \App\Repositories\ConvenioTypeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InvoiceRepository::class, \App\Repositories\InvoiceRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\InvoiceHistoryRepository::class, \App\Repositories\InvoiceHistoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DependentRepository::class, \App\Repositories\DependentRepositoryEloquent::class);
        //:end-bindings:
    }
}
