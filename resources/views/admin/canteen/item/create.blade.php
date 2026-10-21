<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.canteen.item_store') }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="category_id" class="eForm-label">{{ get_phrase('Category') }}</label>
            <select name="category_id" id="category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select Category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Food Name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name="name" placeholder="{{ get_phrase('e.g. Fried Rice') }}" required>
        </div>
        <div class="fpb-7">
            <label for="description" class="eForm-label">{{ get_phrase('Description') }}</label>
            <textarea class="form-control eForm-control" id="description" name="description" rows="3" placeholder="{{ get_phrase('Optional description') }}"></textarea>
        </div>
        <div class="fpb-7">
            <label for="price" class="eForm-label">{{ get_phrase('Price') }}</label>
            <input type="number" step="0.01" min="0" class="form-control eForm-control" id="price" name="price" placeholder="0.00" required>
        </div>
        <div class="fpb-7">
            <label for="stock_quantity" class="eForm-label">{{ get_phrase('Stock Quantity') }}</label>
            <input type="number" min="0" class="form-control eForm-control" id="stock_quantity" name="stock_quantity" placeholder="0" required>
        </div>
        <div class="fpb-7">
            <label for="image" class="eForm-label">{{ get_phrase('Image') }} <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <input type="file" class="form-control eForm-control" id="image" name="image" accept="image/*">
        </div>
        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="1">{{ get_phrase('Available') }}</option>
                <option value="0">{{ get_phrase('Unavailable') }}</option>
            </select>
        </div>
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Create') }}</button>
        </div>
    </div>
</form>
