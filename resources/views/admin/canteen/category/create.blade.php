<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.canteen.category_store') }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Category Name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name="name" placeholder="{{ get_phrase('e.g. Breakfast, Lunch') }}" required>
        </div>
        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="1">{{ get_phrase('Active') }}</option>
                <option value="0">{{ get_phrase('Inactive') }}</option>
            </select>
        </div>
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Create') }}</button>
        </div>
    </div>
</form>
