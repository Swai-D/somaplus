{{-- The fields of a book, used by the pop-up of the list and by the page that changes a book.
     Variables: book (Book or null), subjects. --}}
@php($editing = isset($book))
@php($decimals = \App\Support\Money::CURRENCIES[strtoupper(\App\Tenancy\TenantContext::instance()->current()?->currency ?? 'TZS')] ?? 2)
@php($priceShown = $editing && $book->price !== null ? number_format($book->price / (10 ** $decimals), $decimals, '.', '') : '')

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label" for="book_subject_id">{{ __('library.books.form.subject') }}</label>
        <select class="form-select @error('subject_id') is-invalid @enderror" id="book_subject_id" name="subject_id">
            <option value="">{{ $editing ? __('library.books.list.no_subject') : '—' }}</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" @selected(old('subject_id', $book->subject_id ?? '') == $subject->id)>{{ $subject->name }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('library.books.form.subject_hint') }}</div>
        @error('subject_id')<div class="text-danger fs-13 mt-1">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'name', 'label' => __('library.books.form.name'), 'value' => $book->name ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'publisher', 'label' => __('library.books.form.publisher'), 'required' => false, 'value' => $book->publisher ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'author', 'label' => __('library.books.form.author'), 'required' => false, 'value' => $book->author ?? ''])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'book_number', 'label' => __('library.books.form.number'), 'required' => false, 'value' => $book->book_number ?? '', 'hint' => __('library.books.form.number_hint')])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'rack_number', 'label' => __('library.books.form.rack'), 'required' => false, 'value' => $book->rack_number ?? '', 'hint' => __('library.books.form.rack_hint')])
    </div>
    <div class="col-md-6">
        @include('academics._field', ['name' => 'quantity', 'label' => __('library.books.form.qty'), 'type' => 'number', 'required' => true, 'value' => old('quantity', $book->quantity ?? 1), 'hint' => __('library.books.form.qty_hint')])
    </div>
    <div class="col-md-6">
        {{-- A decimal of the school's money, typed as text so the browser does not round it. --}}
        @include('academics._field', ['name' => 'price', 'label' => __('library.books.form.price'), 'type' => 'text', 'inputmode' => 'decimal', 'required' => false, 'value' => $priceShown, 'hint' => __('library.books.form.price_hint')])
    </div>
</div>
