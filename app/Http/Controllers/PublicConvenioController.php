<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ConvenioRepository;

class PublicConvenioController extends Controller
{

    private $convenioRepository;

    public function __construct (ConvenioRepository $convenioRepository)
    {
        $this->convenioRepository = $convenioRepository;
    }

    public function index()
    {
        $convenios = $this->convenioRepository->all();
        return response()->view('public.convenios', compact('convenios'))
        ->header('X-Frame-Options', 'ALLOWALL')
        ->header('Content-Security-Policy', "frame-ancestors *"); // compatibilidade adicional
    }


    public function iframe()
    {
        $convenios = $this->convenioRepository->all();
        return response()->view('public.convenios-iframe', compact('convenios'))
        ->header('X-Frame-Options', 'ALLOWALL')
        ->header('Content-Security-Policy', "frame-ancestors *"); // compatibilidade adicional
    }
}
