<form method="POST" enctype="multipart/form-data" class="d-block ajaxForm" action="{{ route('admin.canteen.item_update', $item->id) }}">
    @csrf
    <div class="form-row">
        <div class="fpb-7">
            <label for="category_id" class="eForm-label">{{ get_phrase('Category') }}</label>
            <select name="category_id" id="category_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="">{{ get_phrase('Select Category') }}</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $item->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fpb-7">
            <label for="name" class="eForm-label">{{ get_phrase('Food Name') }}</label>
            <input type="text" class="form-control eForm-control" id="name" name="name" value="{{ $item->name }}" required>
        </div>
        <div class="fpb-7">
            <label for="description" class="eForm-label">{{ get_phrase('Description') }}</label>
            <textarea class="form-control eForm-control" id="description" name="description" rows="3" placeholder="{{ get_phrase('Optional description') }}">{{ $item->description }}</textarea>
        </div>
        <div class="fpb-7">
            <label for="price" class="eForm-label">{{ get_phrase('Price') }}</label>
            <input type="number" step="0.01" min="0" class="form-control eForm-control" id="price" name="price" value="{{ $item->price }}" required>
        </div>
        <div class="fpb-7">
            <label for="stock_quantity" class="eForm-label">{{ get_phrase('Stock Quantity') }}</label>
            <input type="number" min="0" class="form-control eForm-control" id="stock_quantity" name="stock_quantity" value="{{ $item->stock_quantity }}" required>
        </div>
        <div class="fpb-7">
            @if ($item->image)
                <div class="mb-2">
                    <img src="{{ asset('assets/uploads/canteen/items/' . $item->image) }}" width="80" height="80" style="object-fit:cover;border-radius:8px;" alt="{{ $item->name }}">
                </div>
            @endif
            <label for="image" class="eForm-label">{{ get_phrase('Change Image') }} <small class="text-muted">({{ get_phrase('optional') }})</small></label>
            <input type="file" class="form-control eForm-control" id="image" name="image" accept="image/*">
        </div>
        <div class="fpb-7">
            <label for="status" class="eForm-label">{{ get_phrase('Status') }}</label>
            <select name="status" id="status" class="form-select eForm-select eChoice-multiple-with-remove" required>
                <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>{{ get_phrase('Available') }}</option>
                <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>{{ get_phrase('Unavailable') }}</option>
            </select>
        </div>
        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">{{ get_phrase('Update') }}</button>
        </div>
    </div>
</form>
