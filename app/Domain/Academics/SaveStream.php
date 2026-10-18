<?php

namespace App\Domain\Academics;

use App\Models\SchoolClass;
use App\Models\Stream;

final class SaveStream
{
    /**
     * @param  array{name: string}  $data
     */
    public function handle(SchoolClass $class, array $data, ?Stream $stream = null): Stream
    {
        $stream ??= new Stream;
        $stream->school_class_id = $class->id;
        $stream->fill(['name' => $data['name']])->save();

        return $stream;
    }
}
