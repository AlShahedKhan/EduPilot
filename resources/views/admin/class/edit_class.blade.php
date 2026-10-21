<div class="school-class-form" data-class-form>
    <form method="POST" class="ajaxForm" action="{{ route('admin.class.update', $schoolClass) }}">
        @csrf
        @method('PATCH')
        <label class="school-class-form__field">
            <span>{{ get_phrase('Class name') }}</span>
            <input type="text" name="name" value="{{ old('name', $schoolClass->name) }}" maxlength="255" required autofocus>
            @error('name')<small>{{ $message }}</small>@enderror
        </label>

        <fieldset>
            <legend>{{ get_phrase('Sections') }}</legend>
            <p>{{ get_phrase('Removing a linked section is blocked to protect academic records.') }}</p>
            <div data-section-list>
                @foreach (old('sections', $schoolClass->sections->map(fn ($section) => ['id' => $section->id, 'name' => $section->name])->all()) as $index => $section)
                    <div class="school-class-form__section" data-section-row data-section-id="{{ $section['id'] ?? '' }}">
                        @if (filled($section['id'] ?? null))<input type="hidden" name="sections[{{ $index }}][id]" value="{{ $section['id'] }}">@endif
                        <label><span>{{ get_phrase('Section name') }}</span><input type="text" name="sections[{{ $index }}][name]" value="{{ $section['name'] ?? '' }}" maxlength="255" required @error("sections.$index.name") aria-invalid="true" aria-describedby="section-error-{{ $index }}" @enderror>@error("sections.$index.name")<small id="section-error-{{ $index }}">{{ $message }}</small>@enderror</label>
                        <button type="button" data-section-remove aria-label="{{ get_phrase('Remove section') }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
                    </div>
                @endforeach
            </div>
            <div data-removed-sections>
                @foreach (old('removed_section_ids', []) as $removedSectionId)<input type="hidden" name="removed_section_ids[]" value="{{ $removedSectionId }}">@endforeach
            </div>
            @error('sections')<small class="school-class-form__error">{{ $message }}</small>@enderror
            @error('removed_section_ids')<small class="school-class-form__error">{{ $message }}</small>@enderror
            <button type="button" class="school-class-form__add" data-section-add><i class="bi bi-plus-lg" aria-hidden="true"></i>{{ get_phrase('Add section') }}</button>
        </fieldset>

        <div class="school-class-form__notice" data-section-notice role="alert" hidden></div>
        <button class="school-class-form__submit" type="submit">{{ get_phrase('Save changes') }}</button>
    </form>
    <template data-section-template>
        <div class="school-class-form__section" data-section-row>
            <label><span>{{ get_phrase('Section name') }}</span><input type="text" name="sections[__INDEX__][name]" maxlength="255" required></label>
            <button type="button" data-section-remove aria-label="{{ get_phrase('Remove section') }}"><i class="bi bi-trash" aria-hidden="true"></i></button>
        </div>
    </template>
</div>
