<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.canteen.category_update', $category->id) }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Category Name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name="name" value="{{ $category->name }}" required>
        </div>
        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="1" {{ $category->status == 1 ? 'selected' : '' }}>{{ get_phrase('Active') }}</option>
                <option value="0" {{ $category->status == 0 ? 'selected' : '' }}>{{ get_phrase('Inactive') }}</option>
            </select>
        </div>
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
        </div>
    </div>
</form>
