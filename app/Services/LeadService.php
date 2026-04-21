<?php

namespace App\Services;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class LeadService
{
    public function __construct(
        private readonly LeadRepositoryInterface $repository
    ) {}

    public function list(array $filters, int $page, int $limit): LengthAwarePaginator
    {
        return $this->repository->paginate($filters, $page, $limit);
    }

    public function find(int $id): Lead
    {
        $lead = $this->repository->findById($id);

        if (!$lead) {
            abort(404, 'Lead no encontrado.');
        }

        return $lead;
    }

    public function create(array $data): Lead
    {
        if ($this->repository->findByEmail($data['email'])) {
            throw ValidationException::withMessages([
                'email' => ['El email ya está registrado.'],
            ]);
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Lead
    {
        $lead = $this->find($id);

        if (isset($data['email']) && $data['email'] !== $lead->email) {
            $existing = $this->repository->findByEmail($data['email']);
            if ($existing) {
                throw ValidationException::withMessages([
                    'email' => ['El email ya está en uso por otro lead.'],
                ]);
            }
        }

        return $this->repository->update($lead, $data);
    }

    public function delete(int $id): void
    {
        $lead = $this->find($id);
        $this->repository->delete($lead);
    }

    public function stats(): array
    {
        return $this->repository->getStats();
    }

    public function getFiltered(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->getFiltered($filters);
    }
}
