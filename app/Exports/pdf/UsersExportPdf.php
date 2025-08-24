<?php

namespace App\Exports\pdf;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class UsersExportPdf
{
    public function __construct(
        private readonly array $users
    ){}

    public function export(): Response
    {
        $pdf = Pdf::loadView('exports.users', ['users' => $this->users]);
        return $pdf->download('usuarios.pdf');
    }

}
