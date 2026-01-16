<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Beneficiary;
use App\Models\Company;
use App\Models\Convenio;
use App\Models\Plan;
use App\Models\Partner;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $beneficiaries = Beneficiary::whereNull('deleted_at')->get()->sortBy('name');
        $companies = Company::whereNull('deleted_at')
        ->orderBy('name', 'asc')
        ->paginate(8);
        $convenios = Convenio::all();
        $plans = Plan::all();
        $partners = Partner::all();

        return view('pages.dashboard', compact(
            'companies',
            'beneficiaries',
            'convenios',
            'plans',
            'partners',
        ));
    }
}
