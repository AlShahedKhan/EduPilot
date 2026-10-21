@extends('admin.navigation')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/school-subject-management.css') }}">

<main class="school-subject-page" data-school-subject-page>
    <header class="school-subject-page__header">
        <div>
            <p class="school-subject-page__eyebrow">{{ get_phrase('Academic Management') }}</p>
            <h1>{{ get_phrase('Subjects') }}</h1>
            <p>{{ get_phrase('Manage subjects by class and academic session.') }}</p>
        </div>
        @if ($canManageSubjects)
            <button type="button" class="school-subject-page__primary" data-subject-open-modal data-url="{{ route('admin.subject.open_modal') }}" data-title="{{ get_phrase('Create Subject') }}">{{ get_phrase('Add subject') }}</button>
        @endif
    </header>

    <section class="school-subject-page__summary" aria-label="{{ get_phrase('Subject summary') }}">
        <article><span>{{ get_phrase('Total Subjects') }}</span><strong>{{ $summary->total }}</strong></article>
        <article><span>{{ get_phrase('Classes Covered') }}</span><strong>{{ $summary->classes_covered }}</strong></article>
        <article><span>{{ get_phrase('Scheduled Subjects') }}</span><strong>{{ $summary->scheduled }}</strong></article>
        <article><span>{{ get_phrase('Unused Subjects') }}</span><strong>{{ $summary->unused }}</strong></article>
    </section>

    <section class="school-subject-page__panel">
        <form method="GET" action="{{ route('admin.subject_list') }}" class="school-subject-page__filters" role="search">
            <label><span>{{ get_phrase('Search subjects') }}</span><input type="search" name="search" value="{{ $search }}" maxlength="255" placeholder="{{ get_phrase('Subject name') }}"></label>
            <label><span>{{ get_phrase('Class') }}</span><select name="class_id"><option value="">{{ get_phrase('All classes') }}</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>@endforeach</select></label>
            <label><span>{{ get_phrase('Session') }}</span><select name="session_id"><option value="">{{ get_phrase('All sessions') }}</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId === $session->id)>{{ $session->session_title }}</option>@endforeach</select></label>
            <label><span>{{ get_phrase('Usage') }}</span><select name="usage"><option value="">{{ get_phrase('All subjects') }}</option><option value="scheduled" @selected($usage === 'scheduled')>{{ get_phrase('Scheduled') }}</option><option value="unused" @selected($usage === 'unused')>{{ get_phrase('Unused') }}</option></select></label>
            <button type="submit" class="school-subject-page__filter-button">{{ get_phrase('Apply filters') }}</button>
            <a href="{{ route('admin.subject_list') }}" class="school-subject-page__reset">{{ get_phrase('Reset') }}</a>
            @if ($subjects->isNotEmpty())<div class="school-subject-page__exports"><button type="button" data-subject-export="pdf" data-subject-pdf-url="{{ route('admin.subject.export.pdf') }}">{{ get_phrase('PDF') }}</button><button type="button" data-subject-export="print">{{ get_phrase('Print') }}</button></div>@endif
        </form>

        @if ($subjects->isEmpty())
            <div class="school-subject-page__empty" role="status"><img src="{{ asset('assets/images/empty_box.png') }}" alt="" width="140" height="140"><h2>{{ $summary->total === 0 ? get_phrase('No subjects yet') : get_phrase('No matching subjects') }}</h2><p>{{ $summary->total === 0 ? get_phrase('Create the first subject to begin.') : get_phrase('Try changing or resetting the current filters.') }}</p></div>
        @else
            <div class="school-subject-page__desktop"><table><thead><tr><th>{{ get_phrase('Subject') }}</th><th>{{ get_phrase('Class') }}</th><th>{{ get_phrase('Session') }}</th><th>{{ get_phrase('Routine usage') }}</th><th>{{ get_phrase('Dependencies') }}</th><th class="school-subject-page__actions-heading">{{ get_phrase('Actions') }}</th></tr></thead><tbody>
                @foreach($subjects as $subject)<tr><td><strong>{{ $subject->name }}</strong></td><td>{{ $subject->schoolClass?->name ?? get_phrase('Unknown class') }}</td><td>{{ $subject->session?->session_title ?? get_phrase('Unknown session') }}</td><td>{{ $subject->routine_count }}</td><td>{{ $subject->dependency_label ?? get_phrase('None') }}</td><td><div class="school-subject-page__actions">@if($canManageSubjects)<button type="button" data-subject-open-modal data-url="{{ route('admin.edit.subject', $subject) }}" data-title="{{ get_phrase('Edit Subject') }}">{{ get_phrase('Edit') }}</button><form method="POST" action="{{ route('admin.subject.delete', $subject) }}" data-subject-delete-form data-name="{{ $subject->name }}">@csrf @method('DELETE')<button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button></form>@else<span class="school-subject-page__read-only">{{ get_phrase('Read only') }}</span>@endif</div></td></tr>@endforeach
            </tbody></table></div>
            <div class="school-subject-page__mobile">@foreach($subjects as $subject)<article class="school-subject-page__card"><header><h2>{{ $subject->name }}</h2><span>{{ $subject->routine_count }} {{ get_phrase('routine slots') }}</span></header><dl><div><dt>{{ get_phrase('Class') }}</dt><dd>{{ $subject->schoolClass?->name ?? get_phrase('Unknown class') }}</dd></div><div><dt>{{ get_phrase('Session') }}</dt><dd>{{ $subject->session?->session_title ?? get_phrase('Unknown session') }}</dd></div><div><dt>{{ get_phrase('Dependencies') }}</dt><dd>{{ $subject->dependency_label ?? get_phrase('None') }}</dd></div></dl>@if($canManageSubjects)<div class="school-subject-page__card-actions"><button type="button" data-subject-open-modal data-url="{{ route('admin.edit.subject', $subject) }}" data-title="{{ get_phrase('Edit Subject') }}">{{ get_phrase('Edit') }}</button><form method="POST" action="{{ route('admin.subject.delete', $subject) }}" data-subject-delete-form data-name="{{ $subject->name }}">@csrf @method('DELETE')<button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button></form></div>@endif</article>@endforeach</div>
            <div class="school-subject-page__pagination">{{ $subjects->links() }}</div>
        @endif
    </section>

    @if($subjects->isNotEmpty())<section class="school-subject-page__print" data-subject-print-view aria-hidden="true"><h1>{{ get_phrase('Subject List') }}</h1><table><thead><tr><th>{{ get_phrase('Subject') }}</th><th>{{ get_phrase('Class') }}</th><th>{{ get_phrase('Session') }}</th><th>{{ get_phrase('Routine usage') }}</th></tr></thead><tbody>@foreach($subjects as $subject)<tr><td>{{ $subject->name }}</td><td>{{ $subject->schoolClass?->name }}</td><td>{{ $subject->session?->session_title }}</td><td>{{ $subject->routine_count }}</td></tr>@endforeach</tbody></table></section>@endif
</main>
<script src="{{ asset('assets/js/school-subject-management.js') }}"></script>
@endsection
