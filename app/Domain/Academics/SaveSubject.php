<?php

namespace App\Domain\Academics;

use App\Models\Subject;

final class SaveSubject
{
    /**
     * @param  array{name: string, code?: string|null}  $data
     */
    public function handle(array $data, ?Subject $subject = null): Subject
    {
        $code = trim((string) ($data['code'] ?? ''));

        $subject ??= new Subject;
        $subject->fill(['name' => $data['name'], 'code' => $code === '' ? null : $code])->save();

        return $subject;
    }
}
