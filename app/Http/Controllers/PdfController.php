<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    //
    public function index(){
        $data = [
            'title'=> "Fndaaa",
            'date' => date('m/d/Y'),
        ];
        $pdf = Pdf::loadView('pdf', $data);
        return $pdf->download('invoice.pdf');
    }
}
