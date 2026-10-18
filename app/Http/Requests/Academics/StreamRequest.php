<?php

namespace App\Http\Requests\Academics;

use App\Models\SchoolClass;
use App\Models\Stream;
use Illuminate\Validation\Rule;

class StreamRequest extends AcademicsRequest
{
    public function authorize(): bool
    {
        $stream = $this->route('stream');

        return $stream instanceof Stream
            ? (bool) $this->user()?->can('update', $stream)
            : (bool) $this->user()?->can('create', Stream::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $stream = $this->route('stream');
        $class = $this->route('schoolClass');
        $classId = $stream instanceof Stream ? $stream->school_class_id : ($class instanceof SchoolClass ? $class->id : 0);

        $name = Rule::unique('streams', 'name')->where('school_class_id', $classId);

        return [
            'name' => ['required', 'string', 'max:100', $stream instanceof Stream ? $name->ignore($stream->id) : $name],
        ];
    }
}
