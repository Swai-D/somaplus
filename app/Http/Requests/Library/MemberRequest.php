<?php

namespace App\Http\Requests\Library;

use App\Models\LibraryMember;
use App\Tenancy\TenantContext;
use Illuminate\Validation\Rule;

/**
 * A member of the library: one pupil of the school with a card number.
 */
class MemberRequest extends LibraryRequest
{
    public function authorize(): bool
    {
        $member = $this->route('member');

        return $member instanceof LibraryMember
            ? (bool) $this->user()?->can('update', $member)
            : (bool) $this->user()?->can('create', LibraryMember::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $member = $this->route('member');
        $ignore = $member instanceof LibraryMember ? $member->id : null;

        return [
            'student_id' => [
                'required',
                $this->inSchool('students'),
                // A pupil is a member of the library at most once.
                Rule::unique('library_members', 'student_id')->where('tenant_id', $this->tenantId())->ignore($ignore),
            ],
            'card_number' => ['required', 'string', 'max:40', $this->unique('library_members', 'card_number', $ignore)],
            'joined_on' => ['required', 'date'],
        ];
    }

    private function tenantId(): ?string
    {
        return app(TenantContext::class)->currentId();
    }
}
