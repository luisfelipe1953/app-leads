<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class LeadRepository implements LeadRepositoryInterface
{
    public function paginate(array $filters, int $page, int $limit): LengthAwarePaginator
    {
        $query = Lead::query()->orderBy('created_at', 'desc');

        if (!empty($filters['fuente'])) {
            $query->where('fuente', $filters['fuente']);
        }

        if (!empty($filters['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['fecha_inicio']);
        }

        if (!empty($filters['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $filters['fecha_fin']);
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    public function findById(int $id): ?Lead
    {
        return Lead::find($id);
    }

    public function findByEmail(string $email): ?Lead
    {
        return Lead::where('email', $email)->first();
    }

    public function create(array $data): Lead
    {
        return Lead::create($data);
    }

    public function update(Lead $lead, array $data): Lead
    {
        $lead->update($data);
        return $lead->fresh();
    }

    public function delete(Lead $lead): void
    {
        $lead->delete();
    }

    public function getStats(): array
    {
        $total = Lead::count();

        $porFuente = Lead::selectRaw('fuente, COUNT(*) as total')
            ->groupBy('fuente')
            ->pluck('total', 'fuente')
            ->toArray();

        $promedioPrespuesto = Lead::whereNotNull('presupuesto')->avg('presupuesto');

        $ultimosSieteDias = Lead::where('created_at', '>=', Carbon::now()->subDays(7))->count();

        return [
            'total_leads'           => $total,
            'leads_por_fuente'      => $porFuente,
            'promedio_presupuesto'  => round($promedioPrespuesto ?? 0, 2),
            'leads_ultimos_7_dias'  => $ultimosSieteDias,
        ];
    }

    public function getFiltered(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Lead::query();

        if (!empty($filters['fuente'])) {
            $query->where('fuente', $filters['fuente']);
        }

        if (!empty($filters['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $filters['fecha_inicio']);
        }

        if (!empty($filters['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $filters['fecha_fin']);
        }

        return $query->get();
    }
}
