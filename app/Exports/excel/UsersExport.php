<?php

namespace App\Exports\excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements  FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private readonly array $users
    ){}

    /**
    * @return Collection
    */
    public function collection(): Collection
    {
        return collect($this->users)->map(function ($user) {
            return [
              'nombres' => $user->name,
              'email' => $user->email,
              'created_at' => $user->created_at->format('Y-m-d'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Correo electronico',
            'Fecha de registro',
        ];
    }
}
