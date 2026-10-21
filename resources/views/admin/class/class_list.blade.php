@extends('admin.navigation')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/school-class-management.css') }}">

<main class="school-class-page" data-school-class-page>
    <header class="school-class-page__header">
        <div>
            <p class="school-class-page__eyebrow">{{ get_phrase('Academic Management') }}</p>
            <h1>{{ get_phrase('Classes and Sections') }}</h1>
            <p>{{ get_phrase('Manage class structure without risking linked academic records.') }}</p>
        </div>
        @if ($canManageClasses)
            <button type="button" class="school-class-page__primary" data-class-open-modal data-url="{{ route('admin.class.open_modal') }}" data-title="{{ get_phrase('Create Class') }}">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                {{ get_phrase('Add class') }}
            </button>
        @endif
    </header>

    <section class="school-class-page__summary" aria-label="{{ get_phrase('Class summary') }}">
        <article><span>{{ get_phrase('Total Classes') }}</span><strong>{{ $summary->total }}</strong></article>
        <article><span>{{ get_phrase('Total Sections') }}</span><strong>{{ $summary->sections }}</strong></article>
        <article><span>{{ get_phrase('Classes With Students') }}</span><strong>{{ $summary->with_students }}</strong></article>
        <article><span>{{ get_phrase('Empty Classes') }}</span><strong>{{ $summary->empty }}</strong></article>
    </section>

    <section class="school-class-page__panel">
        <form method="GET" action="{{ route('admin.class_list') }}" class="school-class-page__filters" role="search">
            <label>
                <span>{{ get_phrase('Search classes or sections') }}</span>
                <input type="search" name="search" value="{{ $search }}" maxlength="255" placeholder="{{ get_phrase('Class or section name') }}">
            </label>
            <label>
                <span>{{ get_phrase('Usage') }}</span>
                <select name="usage">
                    <option value="">{{ get_phrase('All classes') }}</option>
                    <option value="with_students" @selected($usage === 'with_students')>{{ get_phrase('With students') }}</option>
                    <option value="empty" @selected($usage === 'empty')>{{ get_phrase('Empty classes') }}</option>
                </select>
            </label>
            <button type="submit" class="school-class-page__filter-button">{{ get_phrase('Apply filters') }}</button>
            <a href="{{ route('admin.class_list') }}" class="school-class-page__reset">{{ get_phrase('Reset') }}</a>
            @if ($classLists->isNotEmpty())
                <div class="school-class-page__exports">
                    <button type="button" data-class-export="pdf" data-class-pdf-url="{{ route('admin.class.export.pdf') }}">{{ get_phrase('PDF') }}</button>
                    <button type="button" data-class-export="print">{{ get_phrase('Print') }}</button>
                </div>
            @endif
        </form>

        @if ($classLists->isEmpty())
            <div class="school-class-page__empty" role="status">
                <img src="{{ asset('assets/images/empty_box.png') }}" alt="" width="140" height="140">
                <h2>{{ $summary->total === 0 ? get_phrase('No classes yet') : get_phrase('No matching classes') }}</h2>
                <p>{{ $summary->total === 0 ? get_phrase('Create the first class and its sections to begin.') : get_phrase('Try changing or resetting the current filters.') }}</p>
            </div>
        @else
            <div class="school-class-page__desktop">
                <table>
                    <thead>
                        <tr>
                            <th>{{ get_phrase('Class') }}</th>
                            <th>{{ get_phrase('Sections') }}</th>
                            <th>{{ get_phrase('Students') }}</th>
                            <th>{{ get_phrase('Subjects') }}</th>
                            <th class="school-class-page__actions-heading">{{ get_phrase('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($classLists as $schoolClass)
                            <tr>
                                <td><strong>{{ $schoolClass->name }}</strong></td>
                                <td>
                                    <div class="school-class-page__badges">
                                        @foreach ($schoolClass->sections as $section)
                                            <span>{{ $section->name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>{{ $schoolClass->students_count }}</td>
                                <td>{{ $schoolClass->subjects_count }}</td>
                                <td>
                                    <div class="school-class-page__actions">
                                        @if ($canManageClasses)
                                            <button type="button" data-class-open-modal data-url="{{ route('admin.edit.class', $schoolClass) }}" data-title="{{ get_phrase('Edit Class') }}">{{ get_phrase('Edit') }}</button>
                                            <form method="POST" action="{{ route('admin.class.delete', $schoolClass) }}" data-class-delete-form data-name="{{ $schoolClass->name }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button>
                                            </form>
                                        @else
                                            <span class="school-class-page__read-only">{{ get_phrase('Read only') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="school-class-page__mobile">
                @foreach ($classLists as $schoolClass)
                    <article class="school-class-page__card">
                        <header><h2>{{ $schoolClass->name }}</h2><span>{{ trans_choice(':count section|:count sections', $schoolClass->sections_count, ['count' => $schoolClass->sections_count]) }}</span></header>
                        <div class="school-class-page__badges">
                            @foreach ($schoolClass->sections as $section)<span>{{ $section->name }}</span>@endforeach
                        </div>
                        <dl>
                            <div><dt>{{ get_phrase('Students') }}</dt><dd>{{ $schoolClass->students_count }}</dd></div>
                            <div><dt>{{ get_phrase('Subjects') }}</dt><dd>{{ $schoolClass->subjects_count }}</dd></div>
                        </dl>
                        @if ($canManageClasses)
                            <div class="school-class-page__card-actions">
                                <button type="button" data-class-open-modal data-url="{{ route('admin.edit.class', $schoolClass) }}" data-title="{{ get_phrase('Edit Class') }}">{{ get_phrase('Edit') }}</button>
                                <form method="POST" action="{{ route('admin.class.delete', $schoolClass) }}" data-class-delete-form data-name="{{ $schoolClass->name }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button>
                                </form>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>

            <div class="school-class-page__pagination">{{ $classLists->links() }}</div>
        @endif
    </section>

    @if ($classLists->isNotEmpty())
        <section class="school-class-page__print" data-class-print-view aria-hidden="true">
            <h1>{{ get_phrase('Class List') }}</h1>
            <table>
                <thead><tr><th>{{ get_phrase('Class') }}</th><th>{{ get_phrase('Sections') }}</th><th>{{ get_phrase('Students') }}</th><th>{{ get_phrase('Subjects') }}</th></tr></thead>
                <tbody>
                    @foreach ($classLists as $schoolClass)
                        <tr><td>{{ $schoolClass->name }}</td><td>{{ $schoolClass->sections->pluck('name')->join(', ') }}</td><td>{{ $schoolClass->students_count }}</td><td>{{ $schoolClass->subjects_count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </section>
    @endif
</main>

<script src="{{ asset('assets/js/school-class-management.js') }}"></script>
@endsection
