@extends('admin.navigation')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/school-academic-management.css') }}">

<main class="academic-page" data-academic-page>
    <header class="academic-page__header">
        <div><p class="academic-page__eyebrow">{{ get_phrase('Academic Management') }}</p><h1>{{ get_phrase('Syllabus Library') }}</h1><p>{{ get_phrase('Manage curriculum documents across classes, sections, subjects, and sessions.') }}</p></div>
        @if($canManageSyllabuses)<button type="button" class="academic-page__primary" data-academic-modal data-url="{{ route('admin.syllabus.open_modal') }}" data-title="{{ get_phrase('Create Syllabus') }}"><i class="bi bi-plus-lg"></i>{{ get_phrase('Add syllabus') }}</button>@endif
    </header>

    <section class="academic-page__summary" aria-label="{{ get_phrase('Syllabus summary') }}">
        <article><span>{{ get_phrase('Total Documents') }}</span><strong>{{ $summary->total }}</strong></article>
        <article><span>{{ get_phrase('Classes Covered') }}</span><strong>{{ $summary->classes_covered }}</strong></article>
        <article><span>{{ get_phrase('Subjects Covered') }}</span><strong>{{ $summary->subjects_covered }}</strong></article>
        <article><span>{{ get_phrase('Last 30 Days') }}</span><strong>{{ $summary->recent }}</strong></article>
    </section>

    <section class="academic-page__panel">
        <form method="GET" action="{{ route('admin.syllabus') }}" class="academic-page__filters" role="search">
            <label><span>{{ get_phrase('Search documents') }}</span><input type="search" name="search" value="{{ $search }}" maxlength="255" placeholder="{{ get_phrase('Document title') }}"></label>
            <label><span>{{ get_phrase('Class') }}</span><select name="class_id" data-dependent-class data-target="#syllabus-section" data-url="{{ route('admin.subject.sections', ['class' => '__CLASS__']) }}"><option value="">{{ get_phrase('All classes') }}</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected($classId === $class->id)>{{ $class->name }}</option>@endforeach</select></label>
            <label><span>{{ get_phrase('Section') }}</span><select id="syllabus-section" name="section_id" data-placeholder="{{ get_phrase('All sections') }}"><option value="">{{ get_phrase('All sections') }}</option>@foreach($sections as $section)<option value="{{ $section->id }}" @selected($sectionId === $section->id)>{{ $section->name }}</option>@endforeach</select></label>
            <label><span>{{ get_phrase('Subject') }}</span><select name="subject_id"><option value="">{{ get_phrase('All subjects') }}</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected($subjectId === $subject->id)>{{ $subject->name }}</option>@endforeach</select></label>
            <label><span>{{ get_phrase('Session') }}</span><select name="session_id"><option value="">{{ get_phrase('All sessions') }}</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected($sessionId === $session->id)>{{ $session->session_title }}</option>@endforeach</select></label>
            <button class="academic-page__filter-button" type="submit">{{ get_phrase('Apply filters') }}</button><a class="academic-page__reset" href="{{ route('admin.syllabus') }}">{{ get_phrase('Reset') }}</a>
            @if($syllabuses->isNotEmpty())<div class="academic-page__exports"><button type="button" data-academic-export data-export="pdf" data-pdf-url="{{ route('admin.syllabuses.export.pdf') }}">{{ get_phrase('PDF') }}</button><button type="button" data-academic-export data-export="print">{{ get_phrase('Print') }}</button></div>@endif
        </form>

        @if($syllabuses->isEmpty())
            <div class="academic-page__empty" role="status"><img src="{{ asset('assets/images/empty_box.png') }}" alt="" width="140" height="140"><h2>{{ $summary->total === 0 ? get_phrase('No syllabus documents yet') : get_phrase('No matching documents') }}</h2><p>{{ $summary->total === 0 ? get_phrase('Upload the first curriculum document to begin.') : get_phrase('Try changing or resetting the current filters.') }}</p></div>
        @else
            <div class="academic-page__desktop"><table><thead><tr><th>{{ get_phrase('Document') }}</th><th>{{ get_phrase('Class / Section') }}</th><th>{{ get_phrase('Subject') }}</th><th>{{ get_phrase('Session') }}</th><th>{{ get_phrase('Uploaded') }}</th><th class="academic-page__actions-heading">{{ get_phrase('Actions') }}</th></tr></thead><tbody>@foreach($syllabuses as $syllabus)<tr><td><strong>{{ $syllabus->title }}</strong><br><span class="academic-page__badge is-muted">{{ strtoupper(pathinfo($syllabus->file, PATHINFO_EXTENSION)) }}</span></td><td>{{ $syllabus->schoolClass?->name ?? '—' }} / {{ $syllabus->section?->name ?? '—' }}</td><td>{{ $syllabus->subject?->name ?? '—' }}</td><td>{{ $syllabus->session?->session_title ?? '—' }}</td><td>{{ $syllabus->created_at?->format('d M Y') }}</td><td><div class="academic-page__actions"><a class="academic-page__link" href="{{ asset('assets/uploads/syllabus/'.$syllabus->file) }}" target="_blank" rel="noopener">{{ get_phrase('View') }}</a>@if($canManageSyllabuses)<button type="button" data-academic-modal data-url="{{ route('admin.syllabus_edit_modal', $syllabus->id) }}" data-title="{{ get_phrase('Edit Syllabus') }}">{{ get_phrase('Edit') }}</button><form method="POST" action="{{ route('admin.syllabuses.destroy', $syllabus) }}" data-academic-delete data-name="{{ $syllabus->title }}">@csrf @method('DELETE')<button class="is-danger">{{ get_phrase('Delete') }}</button></form>@endif</div></td></tr>@endforeach</tbody></table></div>
            <div class="academic-page__mobile">@foreach($syllabuses as $syllabus)<article class="academic-page__card"><header><h2>{{ $syllabus->title }}</h2><span class="academic-page__badge">{{ strtoupper(pathinfo($syllabus->file, PATHINFO_EXTENSION)) }}</span></header><dl><div><dt>{{ get_phrase('Class / Section') }}</dt><dd>{{ $syllabus->schoolClass?->name ?? '—' }} / {{ $syllabus->section?->name ?? '—' }}</dd></div><div><dt>{{ get_phrase('Subject') }}</dt><dd>{{ $syllabus->subject?->name ?? '—' }}</dd></div><div><dt>{{ get_phrase('Session') }}</dt><dd>{{ $syllabus->session?->session_title ?? '—' }}</dd></div></dl><div class="academic-page__card-actions"><a class="academic-page__link" href="{{ asset('assets/uploads/syllabus/'.$syllabus->file) }}" target="_blank" rel="noopener">{{ get_phrase('View') }}</a>@if($canManageSyllabuses)<form method="POST" action="{{ route('admin.syllabuses.destroy', $syllabus) }}" data-academic-delete data-name="{{ $syllabus->title }}">@csrf @method('DELETE')<button class="is-danger">{{ get_phrase('Delete') }}</button></form>@endif</div></article>@endforeach</div>
            <div class="academic-page__pagination">{{ $syllabuses->links() }}</div>
        @endif
    </section>
    @if($syllabuses->isNotEmpty())<section class="academic-page__print" aria-hidden="true"><h1>{{ get_phrase('Syllabus Library') }}</h1><table><thead><tr><th>{{ get_phrase('Document') }}</th><th>{{ get_phrase('Class') }}</th><th>{{ get_phrase('Section') }}</th><th>{{ get_phrase('Subject') }}</th><th>{{ get_phrase('Session') }}</th></tr></thead><tbody>@foreach($syllabuses as $syllabus)<tr><td>{{ $syllabus->title }}</td><td>{{ $syllabus->schoolClass?->name }}</td><td>{{ $syllabus->section?->name }}</td><td>{{ $syllabus->subject?->name }}</td><td>{{ $syllabus->session?->session_title }}</td></tr>@endforeach</tbody></table></section>@endif
</main>
<script src="{{ asset('assets/js/school-academic-management.js') }}"></script>
@endsection
