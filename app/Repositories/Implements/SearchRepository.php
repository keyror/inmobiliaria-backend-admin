<?php

namespace App\Repositories\Implements;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Person;
use App\Models\Property;
use App\Models\User;
use App\Repositories\ISearchRepository;

class SearchRepository implements ISearchRepository
{
    public function search(string $term, int $limit = 5): array
    {
        return [
            'properties' => $this->searchProperties($term, $limit),
            'people' => $this->searchPeople($term, $limit),
            'companies' => $this->searchCompanies($term, $limit),
            'audit_logs' => $this->searchAuditLogs($term, $limit),
        ];
    }

    private function searchProperties(string $term, int $limit): array
    {
        return Property::query()
            ->with([
                'offerType:id,name',
                'status:id,name',
                'propertyType:id,name',
                'images' => fn ($q) => $q->where('is_cover', true)
                    ->select('id', 'imageable_id', 'imageable_type', 'file_path'),
            ])
            ->where(function ($q) use ($term) {
                $q->where('code', 'LIKE', "%{$term}%")
                    ->orWhere('title', 'LIKE', "%{$term}%")
                    ->orWhere('description', 'LIKE', "%{$term}%")
                    ->orWhereHas('offerType', fn ($q) => $q->where('name', 'LIKE', "%{$term}%"))
                    ->orWhereHas('status', fn ($q) => $q->where('name', 'LIKE', "%{$term}%"))
                    ->orWhereHas('propertyType', fn ($q) => $q->where('name', 'LIKE', "%{$term}%"))
                    ->orWhereHas('addresses', fn ($q) => $q
                        ->where('sector', 'LIKE', "%{$term}%")
                        ->orWhere('address', 'LIKE', "%{$term}%")
                        ->orWhereHas('city', fn ($q) => $q->where('name', 'LIKE', "%{$term}%"))
                    )
                    ->orWhereHas('features', fn ($q) => $q
                        ->where('feature_description', 'LIKE', "%{$term}%")
                        ->orWhereHas('featureType', fn ($q) => $q->where('name', 'LIKE', "%{$term}%"))
                    );
            })
            ->latest()
            ->limit($limit)
            ->get(['id', 'code', 'title', 'status_id', 'offer_type_id', 'property_type_id'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'code' => $p->code,
                'label' => $p->title,
                'subtitle' => collect([$p->offerType?->name, $p->status?->name])
                    ->filter()->implode(' · '),
                'cover_url' => $p->images->first()?->url,
            ])
            ->values()
            ->all();
    }

    private function searchPeople(string $term, int $limit): array
    {
        return Person::query()
            ->with(['contacts' => fn ($q) => $q->where('is_principal', true)->limit(1)])
            ->where(function ($q) use ($term) {
                $q->where('full_name', 'LIKE', "%{$term}%")
                    ->orWhere('first_name', 'LIKE', "%{$term}%")
                    ->orWhere('last_name', 'LIKE', "%{$term}%")
                    ->orWhere('document_number', 'LIKE', "%{$term}%")
                    ->orWhereHas('contacts', fn ($q) => $q
                        ->where('email', 'LIKE', "%{$term}%")
                        ->orWhere('phone', 'LIKE', "%{$term}%")
                        ->orWhere('mobile', 'LIKE', "%{$term}%")
                    );
            })
            ->latest()
            ->limit($limit)
            ->get(['id', 'full_name', 'first_name', 'last_name', 'document_number'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'label' => $p->full_name ?: trim("{$p->first_name} {$p->last_name}"),
                'subtitle' => collect([
                    $p->document_number,
                    $p->contacts->first()?->email,
                ])->filter()->implode(' · '),
            ])
            ->values()
            ->all();
    }

    private function searchAuditLogs(string $term, int $limit): array
    {
        $causerIds = User::where('email', 'LIKE', "%{$term}%")->pluck('id');

        return AuditLog::query()
            ->with('causer:id,email')
            ->where(function ($q) use ($term, $causerIds) {
                $q->where('log_name', 'LIKE', "%{$term}%")
                    ->orWhere('event', 'LIKE', "%{$term}%")
                    ->orWhereIn('causer_id', $causerIds);
            })
            ->latest()
            ->limit($limit)
            ->get(['id', 'log_name', 'event', 'causer_id', 'causer_type', 'created_at'])
            ->map(fn ($log) => [
                'id' => $log->id,
                'label' => collect([
                    __('audit.modules.'.$log->log_name, [], 'es'),
                    $log->event,
                ])->filter()->implode(' • '),
                'subtitle' => collect([
                    $log->causer?->email,
                    $log->created_at?->format('Y-m-d H:i'),
                ])->filter()->implode(' · '),
            ])
            ->values()
            ->all();
    }

    private function searchCompanies(string $term, int $limit): array
    {
        return Company::query()
            ->with(['contacts' => fn ($q) => $q->where('is_principal', true)->limit(1)])
            ->where(function ($q) use ($term) {
                $q->where('company_name', 'LIKE', "%{$term}%")
                    ->orWhere('tradename', 'LIKE', "%{$term}%")
                    ->orWhere('nit', 'LIKE', "%{$term}%")
                    ->orWhereHas('contacts', fn ($q) => $q
                        ->where('email', 'LIKE', "%{$term}%")
                    );
            })
            ->latest()
            ->limit($limit)
            ->get(['id', 'company_name', 'tradename', 'nit'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'label' => $c->company_name,
                'subtitle' => collect([$c->tradename, $c->nit ? "NIT: {$c->nit}" : null])
                    ->filter()->implode(' · '),
            ])
            ->values()
            ->all();
    }
}
