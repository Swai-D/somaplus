<?php

namespace App\Domain\Academics;

use App\Models\SchoolClass;

final class SaveSchoolClass
{
    /**
     * @param  array{name: string, sort_order?: int|null}  $data
     */
    public function handle(array $data, ?SchoolClass $class = null): SchoolClass
    {
        $class ??= new SchoolClass;
        $class->fill(['name' => $data['name'], 'sort_order' => $data['sort_order'] ?? 0])->save();

        return $class;
    }
}
