<?php
// app/Services/BaseService.php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsActivityTrait;

abstract class BaseService
{
    protected Model $model;
    use LogsActivityTrait;
    
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * Get all records with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query();
        
        // Apply filters
        foreach ($filters as $field => $value) {
            if ($value !== null && $value !== '') {
                if (is_string($value) && strlen($value) > 0) {
                    $query->where($field, 'like', "%{$value}%");
                } else {
                    $query->where($field, $value);
                }
            }
        }
        
        // Apply sorting
        if (request()->has('sort_by') && request()->has('sort_direction')) {
            $query->orderBy(request('sort_by'), request('sort_direction'));
        } else {
            $query->latest();
        }
        
        return $query->paginate($perPage);
    }
    
    /**
     * Find record by ID
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }
    
    /**
     * Find record by ID or fail
     */
    public function findOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }
    
    /**
     * Create new record
     */
    public function create($maker,array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $cleanData = $this->cleanEnumData($data);

            $record = $this->model->create($cleanData);
            DB::commit();
            return $record;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating record: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update existing record
     */
    public function update($maker, int $id, array $data): Model
    {
        DB::beginTransaction();
        
        try {
            $record = $this->findOrFail($id);
            $cleanData = $this->cleanEnumData($data);
            $record->update($cleanData);
            DB::commit();
            return $record->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating record: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete record
     */
    public function delete($maker, int $id): bool
    {
        DB::beginTransaction();
        
        try {
            $record = $this->findOrFail($id);
            $result = $record->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting record: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Bulk delete records
     */
    public function bulkDelete($maker, array $ids): bool
    {
        DB::beginTransaction();
        
        try {
            $result = $this->model->whereIn('id', $ids)->delete();
            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk deleting records: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function cleanEnumData(array $data): array
    {
        return array_map(function ($value) {
            if (is_object($value)) {
                // Jika object Enum, ambil value-nya
                if (method_exists($value, 'value')) {
                    return $value->value;
                }
                // Jika object dengan __toString
                if (method_exists($value, '__toString')) {
                    return $value->__toString();
                }
                // Jika tidak bisa dikonversi, skip
                return null;
            }
            return $value;
        }, $data);
    }
}