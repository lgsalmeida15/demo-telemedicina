<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aqui você pode registrar rotas para sua aplicação. Estas rotas são carregadas
| pelo RouteServiceProvider e agrupadas no grupo "web" com middleware padrão.
|
*/

// Health check endpoint
Route::get('/health', function () {
    $status = ['status' => 'healthy'];
    
    try {
        DB::connection()->getPdo();
        $status['database'] = 'connected';
        $status['timestamp'] = now()->toIso8601String();
    } catch (\Exception $e) {
        $status['status'] = 'unhealthy';
        $status['database'] = 'disconnected';
        $status['error'] = $e->getMessage();
        return response()->json($status, 503);
    }
    
    return response()->json($status, 200);
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Redireciona a rota raiz para a página de login
// Route::get('/', function () {
//     return redirect()->route('login');
// });

Route::get('/', 'Admin\CheckoutController@landingPage')->name('home'); // landing page inicial


// Página de Busca de Convenios
Route::get('convenios', 'PublicConvenioController@index')->middleware(['remove.frame']);
Route::get('convenios/iframe', 'PublicConvenioController@iframe')->middleware(['remove.frame']);


/**
 * login para beneficiario
 */
Route::get('beneficiario/login', 'Auth\BeneficiaryAuthController@showLoginForm')->name('beneficiary.login');
Route::post('beneficiario/login/submit', 'Auth\BeneficiaryAuthController@login')->name('beneficiary.login.submit');
Route::post('beneficiario/logout', 'Auth\BeneficiaryAuthController@logout')->name('beneficiary.logout');

/**
 * área do beneficiário
 */
Route::group(
    [
        'namespace' => 'Beneficiary',
        'middleware' => ['auth:beneficiary'],
        'prefix' => 'beneficiary-area'
    ], function () {
        Route::get('/', 'BeneficiaryAreaController@index')->name('beneficiary.area.index');
        Route::get('/edit', 'BeneficiaryAreaController@profileEdit')->name('beneficiary.area.profile.edit'); // tela de edição de perfil
        Route::put('/update', 'BeneficiaryAreaController@profileUpdate')->name('beneficiary.area.profile.update'); // atualizar perfil (titular)
        Route::get('/dependents', 'BeneficiaryAreaController@dependents')->name('beneficiary.area.dependent');
        Route::get('/{plan}/details', 'BeneficiaryAreaController@planDetails')->name('beneficiary.area.plan.details');
        Route::get('/telemedicine', 'BeneficiaryAreaController@telemedicine')->name('beneficiary.area.telemedicine');
        Route::post('/telemedicine/redirect', 'BeneficiaryAreaController@redirectToTelemedicine')->name('beneficiary.area.telemedicine.redirect');
        Route::get('/schedule', 'BeneficiaryAreaController@schedules')->name('beneficiary.area.schedule');
        Route::post('/cancel', 'BeneficiaryAreaController@cancel')->name('beneficiary.area.cancel');
        Route::post('/updatecreditcard', 'BeneficiaryAreaController@updatecreditcard')->name('beneficiary.area.updatecreditcard');
    }
);



/**
 * login para dependente
 */
Route::get('dependente/login', 'Auth\DependentAuthController@showLoginForm')->name('dependent.login');
Route::post('dependente/login', 'Auth\DependentAuthController@login')->name('dependent.login.submit');
Route::post('dependente/logout', 'Auth\DependentAuthController@logout')->name('dependent.logout');



/**
 * área do dependente
 */
Route::group(
    [
        'namespace'  => 'Dependent',
        'middleware' => 'auth:dependent',
        'prefix'     => 'dependent-area'
    ],
    function () {
        Route::get('/', 'DependentAreaController@index')->name('dependent.area.index');
        Route::get('/edit', 'DependentAreaController@profileEdit')->name('dependent.area.profile.edit'); // tela de edição de perfil
        Route::put('/update', 'DependentAreaController@profileUpdate')->name('dependent.area.profile.update'); // atualizar perfil (dependente)
        Route::get('/{plan}/details', 'DependentAreaController@planDetails')->name('dependent.area.plan.details');
        Route::get('/telemedicine', 'DependentAreaController@telemedicine')->name('dependent.area.telemedicine');
        Route::post('/telemedicine/redirect', 'DependentAreaController@redirectToTelemedicine')->name('dependent.area.telemedicine.redirect');
        Route::get('/schedule', 'DependentAreaController@schedules')->name('dependent.area.schedules');
    }
);




/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação para ADMIN/COMPANY
|--------------------------------------------------------------------------
|
| CRUD (com permissão tanto para admin quanto para beneficiarios logados)
|
*/
Route::group(['prefix' => 'dependents', 'middleware' => 'auth:beneficiary,web', 'namespace' => 'Admin'], function () {
    Route::get('{beneficiaryId}/create', 'DependentController@create')->name('dependent.create');
    Route::post('/store', 'DependentController@store')->name('dependent.store');
    Route::get('/{dependent}/edit', 'DependentController@edit')->name('dependent.edit');
    Route::put('/{dependent}/update', 'DependentController@update')->name('dependent.update');
    Route::delete('/{dependent}/delete', 'DependentController@softDelete')->name('dependent.softdelete');
    Route::get('/{dependent}/show', 'DependentController@show')->name('dependent.show');
});

/**
 * Rota de busca de beneficiários (para autocomplete)
 */
Route::get('/beneficiaries/search', 'Admin\CheckoutController@searchBeneficiary')->name('beneficiaries.search');

/**
 * Link Check-Out
 */
Route::get('/{uuid}/landing', 'Admin\CheckoutController@landingPage')->name('checkout.landing'); // landing page inicial
Route::get('/{uuid}/checkout', 'Admin\CheckoutController@checkout')->name('checkout.page'); // página de checkout
Route::post('/checkout/process', 'Admin\CheckoutController@checkoutProcess')->name('checkout.process'); // processa o checkout
Route::get('/checkout/confirmation/{invoiceUuid}', 'Admin\CheckoutController@checkoutConfirmation')->name('checkout.confirmation'); // page de confirmação


Route::group(['prefix' => 'plans', 'middleware' => 'auth', 'namespace' => 'Admin'], function(){
    Route::post('{beneficiary}/store', 'BeneficiaryPlanController@store')->name('beneficiary.plan.store');
    Route::delete('{plan}/delete', 'BeneficiaryPlanController@destroy')->name('beneficiary.plan.destroy');
});

/*
|--------------------------------------------------------------------------
| Rotas protegidas por autenticação
|--------------------------------------------------------------------------
|
| Estas rotas só estão acessíveis para usuários autenticados.
|
*/
Route::group([
    'namespace' => 'Admin',
    'middleware' => 'auth',
    'prefix' => 'admin'
], function () {
    /**
     * Página Inicial do Sistema
     */
    Route::get('/home', 'HomeController@index')->name('admin.home');


    /*
    |--------------------------------------------------------------------------
    | Gerenciamento de Usuários
    |--------------------------------------------------------------------------
    |
    | Rotas relacionadas ao gerenciamento de usuários, incluindo registro,
    | edição de perfil e exclusão. Protegidas por middleware 'auth'.
    |
    */
    Route::group(['prefix' => 'users'], function () {
        /**
         * Gestão de Usuaários do Sistema
         */
        Route::get('user', 'UserController@index')->name('user.index');         // Exibir lista de usuários
        Route::get('user/create', 'UserController@create')->name('user.create'); // Formulário de criação de usuário
        Route::post('user/admin', 'UserController@storeAdmin')->name('user.registro.admin');         // Salvar novo usuário
        Route::post('user/app', 'UserController@storeApp')->name('user.registro.app');         // Salvar novo usuário
        Route::get('user/{user}/edit', 'UserController@edit')->name('user.edit'); // Formulário de edição de usuário
        Route::put('user', 'UserController@update')->name('user.update'); // Atualizar usuário existente
        Route::delete('user/{user}', 'UserController@destroy')->name('user.destroy'); // Excluir usuário


        /**
         * Gestão de Perfil Autenticado
         */
        Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
        Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
        Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

        // Registro de usuário adicional
        // Route::post('registro', 'UserController@registro')->name('user.registro');

        // Exclusão de usuário
        Route::get('delete/{id}', 'UserController@delete')->name('user.delete');
    });

    /**
     * Gestão de Empresas
     */
    Route::group(['prefix' => 'companies'], function () {
        Route::get('/', 'CompanyController@index')->name('company.index'); // lista de companies
        Route::get('/criar', 'CompanyController@create')->name('company.form');
        Route::post('/store', 'CompanyController@store')->name('company.store');
        Route::get('/{company}/show', 'CompanyController@show')->name('company.show');
        Route::get('/{company}/edit', 'CompanyController@edit')->name('company.edit');
        Route::put('/{company}/update', 'CompanyController@update')->name('company.update');
        Route::delete('/{company}/delete', 'CompanyController@softDelete')->name('company.softdelete');
        Route::get('/report', 'CompanyController@report')->name('company.report');

        /**
         * Gestão de Planos da empresa
         */
        Route::group(['prefix'=>'plans'], function () {
            Route::get('{company}/', 'PlanController@index')->name('plan.index');
            Route::get('{company}/create', 'PlanController@create')->name('plan.create');
            Route::post('/store', 'PlanController@store')->name('plan.store');
            Route::get('/{plan}/show', 'PlanController@show')->name('plan.show');
            Route::get('/{plan}/edit', 'PlanController@edit')->name('plan.edit');
            Route::put('/{plan}/update', 'PlanController@update')->name('plan.update');
            Route::delete('/{plan}/destroy', 'PlanController@destroy')->name('plan.destroy');

            /**
             * Gestão de Serviços dos Planos da Empresa (PlanConvenience)
             */
            Route::group(['prefix'=>'conveniences'], function () {
                Route::get('{plan}/', 'PlanConvenioController@index')->name('plan.convenience.index');
                Route::post('{plan}/store', 'PlanConvenioController@store')->name('plan.convenience.store');
                Route::delete('{plan_convenience}/destroy', 'PlanConvenioController@destroy')->name('plan.convenience.destroy');
            });
        });

        /**
         * Gestão de Beneficiários da Empresa
         */
        Route::group(['prefix' => 'beneficiaries'], function () {
            Route::get('{company}/', 'BeneficiaryController@index')->name('beneficiary.index'); // lista de beneficiaries
            Route::get('{company}/criar', 'BeneficiaryController@create')->name('beneficiary.form');
            Route::post('/store', 'BeneficiaryController@store')->name('beneficiary.store'); // cria beneficiary
            Route::delete('/{beneficiary}/delete', 'BeneficiaryController@softDelete')->name('beneficiary.softdelete');
            Route::get('/{beneficiary}/show', 'BeneficiaryController@show')->name('beneficiary.show');
            Route::get('/{beneficiary}/edit', 'BeneficiaryController@edit')->name('beneficiary.edit');
            Route::put('/{beneficiary}/update', 'BeneficiaryController@update')->name('beneficiary.update');
            Route::post('/import', 'BeneficiaryController@importExcel')->name('beneficiary.import');
        });

        /**
         * Rota exclusiva para admin de Dependentes
         */
        Route::group(['prefix' => 'dependents'], function () {
            Route::get('{beneficiaryId}/dependents', 'DependentController@index')->name('dependent.index');
        });
    });

    /**
     * Gestão geral de beneficiários
     */
    Route::group(['prefix' => 'beneficiaries'], function () {
        Route::get('/', 'BeneficiaryController@generalIndex')->name('beneficiary.general.index');
    });

    /**
     * Gestão de Beneficiários Demo
     */
    Route::group(['prefix' => 'demo-beneficiary', 'as' => 'demo-beneficiary.'], function () {
        Route::get('/', 'DemoBeneficiaryController@index')->name('index');
        Route::get('/create', 'DemoBeneficiaryController@create')->name('create');
        Route::post('/', 'DemoBeneficiaryController@store')->name('store');
        Route::get('/{beneficiary}', 'DemoBeneficiaryController@show')->name('show');
        Route::post('/{beneficiary}/extend', 'DemoBeneficiaryController@extend')->name('extend');
        Route::post('/{beneficiary}/convert', 'DemoBeneficiaryController@convertToReal')->name('convert');
        Route::get('/{beneficiary}/login-as', 'DemoBeneficiaryController@loginAs')->name('login-as');
        Route::delete('/{beneficiary}', 'DemoBeneficiaryController@destroy')->name('destroy');
    });

    

    /**
     * Gestão de Categorias de Serviços
     */
    Route::group(['prefix' => 'convenios/categorias'], function () {
        Route::get('/', 'ConveniosCategoriaController@index')->name('convenio.categoria.index');
        Route::post('/store', 'ConveniosCategoriaController@store')->name('convenio.categoria.store');
        Route::delete('/{categoria}', 'ConveniosCategoriaController@softDelete')->name('convenio.categoria.softdelete');
        Route::post('/convenio/categoria/store-ajax', 'ConveniosCategoriaController@storeAjax')->name('convenio.categoria.store_ajax');

    });

    /**
     * Gestão de Tipos de Serviços
     */
    Route::group(['prefix'=>'convenios/tipos'], function () {
        Route::get('/', 'ConvenioTypeController@index')->name('convenio.type.index');
        Route::post('/store', 'ConvenioTypeController@store')->name('convenio.type.store');
        Route::delete('{type}/destroy', 'ConvenioTypeController@destroy')->name('convenio.type.destroy');
        Route::post('/store-ajax', 'ConvenioTypeController@storeAjax')->name('convenio.type.store.ajax');
    });

    /**
     * Gestão de Serviços (Convenios)
     */
    Route::group(['prefix' => 'convenios'], function () {
        Route::get('/', 'ConvenioController@index')->name('convenio.index');
        Route::get('/create', 'ConvenioController@create')->name('convenio.create');
        Route::post('/store', 'ConvenioController@store')->name('convenio.store');
        Route::get('/{convenio}/show', 'ConvenioController@show')->name('convenio.show');
        Route::get('/{convenio}/view_edit', 'ConvenioController@view_edit')->name('convenio.view_edit');
        Route::put('/{convenio}/update', 'ConvenioController@update')->name('convenio.update');
        Route::delete('/{convenio}/delete', 'ConvenioController@delete')->name('convenio.delete');

        
    });

    /**
     * Gestão de Parceiros
     */
    Route::group(['prefix' => 'partners'], function () {
        Route::get('/', 'PartnerController@index')->name('partner.index');
        Route::get('/create', 'PartnerController@create')->name('partner.create');
        Route::post('/store', 'PartnerController@store')->name('partner.store'); 
        Route::get('/{partner}/edit', 'PartnerController@edit')->name('partner.edit');  
        Route::put('/{partner}/update', 'PartnerController@update')->name('partner.update');
        Route::get('/{partner}/show', 'PartnerController@show')->name('partner.show');
        Route::delete('/{partner}/delete', 'PartnerController@softDelete')->name('partner.softdelete');

        // Gerenciamento de Indicações
        Route::group(['prefix' => 'indications'], function(){
            Route::post('{partner}/store', 'PartnerCompanyController@store')->name('partner.indication.store');
            Route::delete('{indication}/delete', 'PartnerCompanyController@destroy')->name('partner.indication.destroy');
        });
    });

    /**
     * Gestão Financeiro
     */
    Route::group(['prefix' => 'financeiro'], function () {
        Route::get('/', 'FinancialController@index')->name('financial.index'); // lista de beneficiaries
        Route::get('/print', 'FinancialController@print')->name('financial.print');
        // Route::get('/criar', 'BeneficiaryController@formCreate')->name('beneficiary.form');
        Route::post('/store', 'FinancialController@store')->name('financial.store'); // cria beneficiary
        Route::put('{id}', 'FinancialController@update')->name('financial.update');   // financial.update
        Route::post('{id}', 'FinancialController@destroy')->name('financial.destroy');  // financial.destroy


        /**
         * Gestão de Caixas
         */
        Route::group(['prefix'=>'caixa'], function () {
            Route::get('/', 'CaixaController@index')->name('caixa.index');
            Route::post('/store', 'CaixaController@store')->name('caixa.store');
            Route::put('/{caixa}/update', 'CaixaController@update')->name('caixa.update');
            Route::delete('/{caixa}/delete', 'CaixaController@softDelete')->name('caixa.delete');
        });


        /**
         * Gestão Planos de Contas
         */
        Route::group(['prefix' => 'plano_contas'], function () {
            Route::get('/', 'CostCenterController@index')->name('costcenter.index');
            Route::post('/store', 'CostCenterController@store')->name('costcenter.store');
            Route::put('/{costcenter}/update', 'CostCenterController@update')->name('costcenter.update');
            Route::delete('/{costcenter}/delete', 'CostCenterController@delete')->name('costcenter.delete');
        });

        /**
         * Gestão de Contas a Pagar
         */
        Route::group(['prefix' => 'contas_pagar'], function () {
            Route::get('/', 'ContaPagarController@index')->name('conta_pagar.index');
            Route::get('/create', 'ContaPagarController@create')->name('conta_pagar.create');
            Route::post('/store', 'ContaPagarController@store')->name('conta_pagar.store');
            Route::get('/{conta}/show', 'ContaPagarController@show')->name('conta_pagar.show');
            Route::get('/{conta}/view_edit', 'ContaPagarController@view_edit')->name('conta_pagar.view_edit');
            Route::put('/{conta}/update', 'ContaPagarController@update')->name('conta_pagar.update');
            Route::delete('/{conta}/delete', 'ContaPagarController@softDelete')->name('conta_pagar.softdelete');
            Route::post('{conta}/pay', 'ContaPagarController@pay')->name('conta_pagar.pay');
        });

        /**
         * Gestão de Contas a Receber
         */
        Route::group(['prefix' => 'contas_receber'], function () {
            Route::get('/', 'ContaReceberController@index')->name('conta_receber.index');
            Route::get('/create', 'ContaReceberController@create')->name('conta_receber.create');
            Route::post('/store', 'ContaReceberController@store')->name('conta_receber.store');
            Route::get('/{conta}/view_edit', 'ContaReceberController@view_edit')->name('conta_receber.view_edit');
            Route::get('/{conta}/show', 'ContaReceberController@show')->name('conta_receber.show');
            Route::put('/{conta}/update', 'ContaReceberController@update')->name('conta_receber.update');
            Route::delete('{conta}/delete', 'ContaReceberController@softDelete')->name('conta_receber.softdelete');
            Route::post('{conta}/pay', 'ContaReceberController@pay')->name('conta_receber.pay');
        });
    });

    /**
     * Gestão de Produtos
     */

    Route::group(['prefix' => 'produtos'], function (){
        /**
         * Categorias
         */
        Route::group(['prefix'=>'categorias'], function(){
            Route::get('/', 'ProdutoCategoriasController@index')->name('produtos.categorias.index');
            Route::post('/store', 'ProdutoCategoriasController@store')->name('produtos.categorias.store');
            Route::delete('/{categoria}/delete', 'ProdutoCategoriasController@softDelete')->name('produtos.categorias.softdelete');
        });
        /**
         * Unidades
         */
        Route::group(['prefix'=>'unidades'], function(){
            Route::get('/', 'ProdutoUnidadeController@index')->name('produtos.unidades.index');
            Route::post('/store', 'ProdutoUnidadeController@store')->name('produtos.unidades.store');
            Route::put('/{unidade}/update', 'ProdutoUnidadeController@update')->name('produtos.unidades.update');
            Route::delete('/{unidade}/delete', 'ProdutoUnidadeController@softDelete')->name('produtos.unidades.softdelete');
        });
        /**
         * Grupos
         */
        Route::group(['prefix'=>'grupos'], function(){
            Route::get('/', 'ProdutoGrupoController@index')->name('produtos.grupos.index');
            Route::post('/store', 'ProdutoGrupoController@store')->name('produtos.grupos.store');
            Route::put('/{grupo}/update', 'ProdutoGrupoController@update')->name('produtos.grupos.update');
            Route::delete('/{grupo}/delete', 'ProdutoGrupoController@softDelete')->name('produtos.grupos.softdelete');
        });

        /**
         * Lista de Produtos
         */
        Route::group(['prefix'=>'produtos'], function(){
            Route::get('/', 'ProdutoController@index')->name('produtos.index');
            Route::get('/create', 'ProdutoController@create')->name('produtos.create');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Rotas de Autenticação
|--------------------------------------------------------------------------
|
| Rotas para login, logout, registro e redefinição de senha. Inclui também
| rotas para confirmação de senha e verificação de e-mail.
|
*/
// Login
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

// Registro
// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');

// Redefinição de Senha
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

// Confirmação de Senha
Route::get('password/confirm', 'Auth\ConfirmPasswordController@showConfirmForm')->name('password.confirm');
Route::post('password/confirm', 'Auth\ConfirmPasswordController@confirm');

// Verificação de E-mail
Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');



// recuperação de senha de dependentes e beneficiarios

Route::get('/password/forgot/form', 'Auth\AuthController@showForgotForm')->name('forgot.form.password');
Route::get('/password/forgot/beneficiary/form', 'Auth\AuthController@showForgotFormBeneficiary')->name('forgot.form.beneficiary.password');
Route::post('/password/forgot', 'Auth\AuthController@forgotPassword')->name('forgot.password'); //Rota única para recuperação de senha
Route::get('/password/reset', 'Auth\AuthController@showResetForm')->name('reset.form.password');
Route::get('/password/reset', 'Auth\AuthController@resetPassword')->name('update.password');
Route::get('/confirm/email', 'Auth\AuthController@confirm')->name('confirm.email');