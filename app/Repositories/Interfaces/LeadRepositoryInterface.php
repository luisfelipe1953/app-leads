<?php

namespace App\Repositories\Interfaces;

use App\Models\Lead;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeadRepositoryInterface
{
    public function paginate(array $filters, int $page, int $limit): LengthAwarePaginator;
    public function findById(int $id): ?Lead;
    public function findByEmail(string $email): ?Lead;
    public function create(array $data): Lead;
    public function update(Lead $lead, array $data): Lead;
    public function delete(Lead $lead): void;
    public function getStats(): array;
    public function getFiltered(array $filters): \Illuminate\Database\Eloquent\Collection;
}
