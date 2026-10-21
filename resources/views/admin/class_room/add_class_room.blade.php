<div class="school-room-form">
    <form method="POST" class="ajaxForm" action="{{ route('admin.create.class_room') }}">
        @csrf
        <label>
            <span>{{ get_phrase('Room name') }}</span>
            <input type="text" name="name" value="{{ old('name') }}" maxlength="255" required autofocus>
            @error('name')<small>{{ $message }}</small>@enderror
        </label>
        <button type="submit">{{ get_phrase('Create room') }}</button>
    </form>
</div>
