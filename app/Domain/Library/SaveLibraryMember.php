<?php

namespace App\Domain\Library;

use App\Domain\Settings\RecordAudit;
use App\Models\LibraryMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Adds a member of the library or changes whose card it is. A pupil is a member at
 * most once, which the form refuses first and the database keeps afterwards.
 */
final class SaveLibraryMember
{
    public function __construct(private readonly RecordAudit $audit) {}

    /**
     * @param  array{student_id: int, card_number: string, joined_on: string}  $data
     */
    public function handle(?LibraryMember $member, array $data): LibraryMember
    {
        return DB::transaction(function () use ($member, $data): LibraryMember {
            $created = $member === null;

            $takenBy = LibraryMember::query()
                ->where('student_id', $data['student_id'])
                ->when($member !== null, fn (Builder $query) => $query->whereKeyNot($member->id))
                ->first();

            if ($takenBy !== null) {
                throw ValidationException::withMessages([
                    'student_id' => __('library.members.already_member'),
                ]);
            }

            $member ??= new LibraryMember;
            $member->fill([
                'student_id' => $data['student_id'],
                'card_number' => $data['card_number'],
                'joined_on' => $data['joined_on'],
            ]);
            $member->save();

            $this->audit->handle($created ? 'member.created' : 'member.updated', $member);

            return $member;
        });
    }
}
