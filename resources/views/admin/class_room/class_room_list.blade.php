@extends('admin.navigation')

@section('content')
<link rel="stylesheet" href="{{ asset('assets/css/school-class-room-management.css') }}">

<main class="school-room-page" data-school-room-page>
    <header class="school-room-page__header">
        <div>
            <p class="school-room-page__eyebrow">{{ get_phrase('Academic Management') }}</p>
            <h1>{{ get_phrase('Class Rooms') }}</h1>
            <p>{{ get_phrase('Track teaching spaces and their timetable usage.') }}</p>
        </div>
        @if ($canManageClassRooms)
            <button type="button" class="school-room-page__primary" data-room-open-modal data-url="{{ route('admin.class_room.open_modal') }}" data-title="{{ get_phrase('Create Class Room') }}">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>{{ get_phrase('Add class room') }}
            </button>
        @endif
    </header>

    <section class="school-room-page__summary" aria-label="{{ get_phrase('Class room summary') }}">
        <article><span>{{ get_phrase('Total Rooms') }}</span><strong>{{ $summary->total }}</strong></article>
        <article><span>{{ get_phrase('In Use') }}</span><strong>{{ $summary->in_use }}</strong></article>
        <article><span>{{ get_phrase('Available') }}</span><strong>{{ $summary->available }}</strong></article>
        <article><span>{{ get_phrase('Routine Slots') }}</span><strong>{{ $summary->routine_slots }}</strong></article>
    </section>

    <section class="school-room-page__panel">
        <form method="GET" action="{{ route('admin.class_room_list') }}" class="school-room-page__filters" role="search">
            <label><span>{{ get_phrase('Search rooms') }}</span><input type="search" name="search" value="{{ $search }}" maxlength="255" placeholder="{{ get_phrase('Room name') }}"></label>
            <label>
                <span>{{ get_phrase('Usage') }}</span>
                <select name="usage">
                    <option value="">{{ get_phrase('All rooms') }}</option>
                    <option value="in_use" @selected($usage === 'in_use')>{{ get_phrase('In use') }}</option>
                    <option value="available" @selected($usage === 'available')>{{ get_phrase('Available') }}</option>
                </select>
            </label>
            <button type="submit" class="school-room-page__filter-button">{{ get_phrase('Apply filters') }}</button>
            <a href="{{ route('admin.class_room_list') }}" class="school-room-page__reset">{{ get_phrase('Reset') }}</a>
            @if ($classRooms->isNotEmpty())
                <div class="school-room-page__exports">
                    <button type="button" data-room-export="pdf" data-room-pdf-url="{{ route('admin.class_room.export.pdf') }}">{{ get_phrase('PDF') }}</button>
                    <button type="button" data-room-export="print">{{ get_phrase('Print') }}</button>
                </div>
            @endif
        </form>

        @if ($classRooms->isEmpty())
            <div class="school-room-page__empty" role="status">
                <img src="{{ asset('assets/images/empty_box.png') }}" alt="" width="140" height="140">
                <h2>{{ $summary->total === 0 ? get_phrase('No class rooms yet') : get_phrase('No matching class rooms') }}</h2>
                <p>{{ $summary->total === 0 ? get_phrase('Create the first room to begin planning routine locations.') : get_phrase('Try changing or resetting the current filters.') }}</p>
            </div>
        @else
            <div class="school-room-page__desktop">
                <table>
                    <thead><tr><th>{{ get_phrase('Room') }}</th><th>{{ get_phrase('Routine Slots') }}</th><th>{{ get_phrase('Status') }}</th><th class="school-room-page__actions-heading">{{ get_phrase('Actions') }}</th></tr></thead>
                    <tbody>
                        @foreach ($classRooms as $classRoom)
                            <tr>
                                <td><strong>{{ $classRoom->name }}</strong></td>
                                <td>{{ $classRoom->routines_count }}</td>
                                <td><span class="school-room-page__status {{ $classRoom->routines_count > 0 ? 'is-used' : 'is-available' }}">{{ $classRoom->routines_count > 0 ? get_phrase('In use') : get_phrase('Available') }}</span></td>
                                <td>
                                    <div class="school-room-page__actions">
                                        @if ($canManageClassRooms)
                                            <button type="button" data-room-open-modal data-url="{{ route('admin.edit.class_room', $classRoom) }}" data-title="{{ get_phrase('Edit Class Room') }}">{{ get_phrase('Edit') }}</button>
                                            <form method="POST" action="{{ route('admin.class_room.delete', $classRoom) }}" data-room-delete-form data-name="{{ $classRoom->name }}">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button>
                                            </form>
                                        @else
                                            <span class="school-room-page__read-only">{{ get_phrase('Read only') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="school-room-page__mobile">
                @foreach ($classRooms as $classRoom)
                    <article class="school-room-page__card">
                        <header><h2>{{ $classRoom->name }}</h2><span class="school-room-page__status {{ $classRoom->routines_count > 0 ? 'is-used' : 'is-available' }}">{{ $classRoom->routines_count > 0 ? get_phrase('In use') : get_phrase('Available') }}</span></header>
                        <dl><div><dt>{{ get_phrase('Routine Slots') }}</dt><dd>{{ $classRoom->routines_count }}</dd></div></dl>
                        @if ($canManageClassRooms)
                            <div class="school-room-page__card-actions">
                                <button type="button" data-room-open-modal data-url="{{ route('admin.edit.class_room', $classRoom) }}" data-title="{{ get_phrase('Edit Class Room') }}">{{ get_phrase('Edit') }}</button>
                                <form method="POST" action="{{ route('admin.class_room.delete', $classRoom) }}" data-room-delete-form data-name="{{ $classRoom->name }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="is-danger">{{ get_phrase('Delete') }}</button>
                                </form>
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="school-room-page__pagination">{{ $classRooms->links() }}</div>
        @endif
    </section>

    @if ($classRooms->isNotEmpty())
        <section class="school-room-page__print" data-room-print-view aria-hidden="true">
            <h1>{{ get_phrase('Class Room List') }}</h1>
            <table><thead><tr><th>{{ get_phrase('Room') }}</th><th>{{ get_phrase('Routine Slots') }}</th><th>{{ get_phrase('Status') }}</th></tr></thead><tbody>
                @foreach ($classRooms as $classRoom)<tr><td>{{ $classRoom->name }}</td><td>{{ $classRoom->routines_count }}</td><td>{{ $classRoom->routines_count > 0 ? get_phrase('In use') : get_phrase('Available') }}</td></tr>@endforeach
            </tbody></table>
        </section>
    @endif
</main>

<script src="{{ asset('assets/js/school-class-room-management.js') }}"></script>
@endsection
