<?php

namespace App\Repositories\Implements;

use App\Models\Rent;
use App\Repositories\IRentRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class RentRepository implements IRentRepository
{
    public function getRentsByFilters(): LengthAwarePaginator
    {
        return Rent::query()
            ->with([
                'property:id,code,title',
                'contractType:id,name,alias',
            ])
            ->allowedFilters([
                'contract_number',
                'status',
                'start_date',
                'end_date',
                'property.code',
                'property.title',
                'contractType.alias',
            ])
            ->allowedSorts([
                'contract_number',
                'start_date',
                'end_date',
                'canon',
                'created_at',
            ])
            ->jsonPaginate();
    }

    public function getRentWithRelations(Rent $rent): Rent
    {
        return $rent->load([
            'property:id,code,title,property_type_id,status_property_id',
            'contractType:id,name,alias',
            'incrementType:id,name,alias',
            'paymentBank:id,name,alias',
            'limitDate',
            'rentTenantCodebtors.tenant:id,full_name,company_name,document_number,document_type_id,organization_type_id',
            'rentTenantCodebtors.codebtor:id,full_name,company_name,document_number,document_type_id,organization_type_id',
            'documents',
        ]);
    }

    public function create(array $data): Rent
    {
        return Rent::create([
            'property_id' => $data['property_id'],
            'status' => $data['status'] ?? null,
            'contract_number' => $data['contract_number'] ?? null,
            'contract_type_id' => $data['contract_type_id'] ?? null,
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'duration' => $data['duration'] ?? null,
            'destination' => $data['destination'] ?? null,
            'activity' => $data['activity'] ?? null,
            'period' => $data['period'] ?? null,
            'canon' => $data['canon'] ?? null,
            'iva' => $data['iva'] ?? null,
            'administration_included' => $data['administration_included'] ?? false,
            'is_ph' => $data['is_ph'] ?? false,
            'interest_rate' => $data['interest_rate'] ?? null,
            'increment_type_id' => $data['increment_type_id'] ?? null,
            'adjustment_date' => $data['adjustment_date'] ?? null,
            'is_insured' => $data['is_insured'] ?? false,
            'consignment_account' => $data['consignment_account'] ?? null,
            'payment_bank_id' => $data['payment_bank_id'] ?? null,
            'commissions' => $data['commissions'] ?? null,
            'signed_city' => $data['signed_city'] ?? null,
            'signed_at' => $data['signed_at'] ?? null,
            'additional_clauses' => $data['additional_clauses'] ?? null,
            'internal_notes' => $data['internal_notes'] ?? null,
            'limit_dates_id' => $data['limit_dates_id'] ?? null,
        ]);
    }

    public function update(array $data, Rent $rent): void
    {
        $rent->update([
            'property_id' => $data['property_id'] ?? $rent->property_id,
            'status' => $data['status'] ?? $rent->status,
            'contract_number' => $data['contract_number'] ?? $rent->contract_number,
            'contract_type_id' => $data['contract_type_id'] ?? $rent->contract_type_id,
            'start_date' => $data['start_date'] ?? $rent->start_date,
            'end_date' => $data['end_date'] ?? $rent->end_date,
            'duration' => $data['duration'] ?? $rent->duration,
            'destination' => $data['destination'] ?? $rent->destination,
            'activity' => $data['activity'] ?? $rent->activity,
            'period' => $data['period'] ?? $rent->period,
            'canon' => $data['canon'] ?? $rent->canon,
            'iva' => $data['iva'] ?? $rent->iva,
            'administration_included' => $data['administration_included'] ?? $rent->administration_included,
            'is_ph' => $data['is_ph'] ?? $rent->is_ph,
            'interest_rate' => $data['interest_rate'] ?? $rent->interest_rate,
            'increment_type_id' => $data['increment_type_id'] ?? $rent->increment_type_id,
            'adjustment_date' => $data['adjustment_date'] ?? $rent->adjustment_date,
            'is_insured' => $data['is_insured'] ?? $rent->is_insured,
            'consignment_account' => $data['consignment_account'] ?? $rent->consignment_account,
            'payment_bank_id' => $data['payment_bank_id'] ?? $rent->payment_bank_id,
            'commissions' => $data['commissions'] ?? $rent->commissions,
            'signed_city' => $data['signed_city'] ?? $rent->signed_city,
            'signed_at' => $data['signed_at'] ?? $rent->signed_at,
            'additional_clauses' => $data['additional_clauses'] ?? $rent->additional_clauses,
            'internal_notes' => $data['internal_notes'] ?? $rent->internal_notes,
            'limit_dates_id' => $data['limit_dates_id'] ?? $rent->limit_dates_id,
        ]);
    }

    public function delete(Rent $rent): void
    {
        $rent->delete();
    }
}
