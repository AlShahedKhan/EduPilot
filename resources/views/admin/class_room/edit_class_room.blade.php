<div class="school-room-form">
    <form method="POST" class="ajaxForm" action="{{ route('admin.class_room.update', $classRoom) }}">
        @csrf
        @method('PATCH')
        <label>
            <span>{{ get_phrase('Room name') }}</span>
            <input type="text" name="name" value="{{ old('name', $classRoom->name) }}" maxlength="255" required autofocus>
            @error('name')<small>{{ $message }}</small>@enderror
        </label>
        <button type="submit">{{ get_phrase('Save changes') }}</button>
    </form>
</div>
