<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait HasSort
{
    /**
     * Define the sortable fields for the model.
     *
     * @return array
     */
    public function sortFields(): array
    {
        return ['id'];
    }

    /**
     * Scope a query to sort by the specified field and direction.
     *
     * @param Builder $builder
     * @param string|null $sortDirection
     * @return void
     * @throws HttpResponseException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function scopeSort(Builder $builder, ?string $sortDirection = ''): void
    {
        $sortBy = request()->get('sortBy', 'id');
        $sortDirection = $sortDirection ?: request()->get('sortDirection', 'asc');
        $sortDirection = $sortDirection === 'asc' ? 'asc' : 'desc';

        if (!in_array($sortBy, $this->sortFields())) {
            throw new HttpResponseException(
                jsonResponse(status: 400,message: 'Invalid sortBy field')
            );
        }
        $builder->orderBy($sortBy, $sortDirection);
    }
}
