<div class="school-class-form" data-class-form>
    <form method="POST" class="ajaxForm" action="{{ route('admin.create.class') }}">
        @csrf
        <label class="school-class-form__field">
            <span>{{ get_phrase('Class name') }}</span>
            <input type="text" name="name" value="{{ old('name') }}" maxlength="255" required autofocus>
            @error('name')<small>{{ $message }}</small>@enderror
        </label>

        <fieldset>
            <legend>{{ get_phrase('Sections') }}</legend>
            <p>{{ get_phrase('Every class must have at least one uniquely named section.') }}</p>
            <div data-section-list>
                @foreach (old('sections', [['name' => 'A']]) as $index => $section)
                    <div class="school-class-form__section" data-section-row>
                        <label><span>{{ get_phrase('Section name') }}</span><input type="text" name="sections[{{ $index }}][name]" value="{{ $section['name'] ?? '' }}" maxlength="255" required @error("sections.$index.name") aria-invalid="true" aria-describedby="section-error-{{ $index }}" @enderror>@error("sections.$index.name")<small id="section-error-{{ $index }}">{{ $message }}</small>@enderror</label>
                        <button type="button" data-section-remove aria-label="{{ get_phrase('Remove section') }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    </div>
                @endforeach
            </div>
            @error('sections')<small class="school-class-form__error">{{ $message }}</small>@enderror
            <button type="button" class="school-class-form__add" data-section-add><i class="bi bi-plus-lg" aria-hidden="true"></i>{{ get_phrase('Add section') }}</button>
        </fieldset>

        <div class="school-class-form__notice" data-section-notice role="alert" hidden></div>
        <button class="school-class-form__submit" type="submit">{{ get_phrase('Create class') }}</button>
    </form>
    <template data-section-template>
        <div class="school-class-form__section" data-section-row>
            <label><span>{{ get_phrase('Section name') }}</span><input type="text" name="sections[__INDEX__][name]" maxlength="255" required></label>
            <button type="button" data-section-remove aria-label="{{ get_phrase('Remove section') }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </div>
    </template>
</div>
