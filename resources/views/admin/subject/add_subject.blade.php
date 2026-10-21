<div class="school-subject-form" data-subject-form><form method="POST" class="ajaxForm" action="{{ route('admin.create.subject') }}">@csrf
    <label><span>{{ get_phrase('Subject name') }}</span><input type="text" name="name" value="{{ old('name') }}" maxlength="255" required autofocus>@error('name')<small>{{ $message }}</small>@enderror</label>
    <label><span>{{ get_phrase('Class') }}</span><select name="class_id" required><option value="">{{ get_phrase('Select a class') }}</option>@foreach($classes as $class)<option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>{{ $class->name }}</option>@endforeach</select>@error('class_id')<small>{{ $message }}</small>@enderror</label>
    <label><span>{{ get_phrase('Session') }}</span><select name="session_id" required><option value="">{{ get_phrase('Select a session') }}</option>@foreach($sessions as $session)<option value="{{ $session->id }}" @selected(old('session_id', $runningSessionId) == $session->id)>{{ $session->session_title }}</option>@endforeach</select>@error('session_id')<small>{{ $message }}</small>@enderror</label>
    <button type="submit">{{ get_phrase('Create subject') }}</button>
</form></div>
