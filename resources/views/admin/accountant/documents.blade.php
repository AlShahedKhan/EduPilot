@extends('admin.navigation')

@section('content')
<div class="mainSection-title"><div class="row"><div class="col-12"><div class="d-flex justify-content-between align-items-center flex-wrap gr-15"><div class="d-flex flex-column"><h4>{{ get_phrase('Accountant Documents') }}</h4><ul class="d-flex align-items-center eBreadcrumb-2"><li><a href="{{ route('admin.dashboard') }}">{{ get_phrase('Home') }}</a></li><li><a href="{{ route('admin.accountant') }}">{{ get_phrase('Accountants') }}</a></li><li><a href="{{ route('admin.accountant.documents', $accountant) }}">{{ get_phrase('Documents') }}</a></li></ul></div></div></div></div></div>
<div class="row g-4">
    <div class="col-lg-6"><div class="eSection-wrap"><div class="eMain"><p class="column-title">{{ get_phrase('Files') }}</p>
        @forelse ($documents as $key => $fileName)
            <div class="d-flex justify-content-between align-items-center documents mb-2"><a href="{{ asset('assets/uploads/user-docs/'.$accountant->id.'/'.basename($fileName)) }}" target="_blank" rel="noopener">{{ str($key)->replace('-', ' ')->title() }}</a><form method="POST" action="{{ route('admin.accountant.documents.destroy', [$accountant, $key]) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-link text-danger" aria-label="{{ get_phrase('Remove document') }}"><i class="bi bi-trash" aria-hidden="true"></i></button></form></div>
        @empty
            <div class="empty_box center center2"><img class="mb-3" width="100" src="{{ asset('assets/images/empty_box.png') }}" alt="" /><span class="d-flex w-100">{{ get_phrase('No documents uploaded') }}</span></div>
        @endforelse
    </div></div></div>
    <div class="col-lg-6"><div class="eSection-wrap"><div class="eMain"><p class="column-title">{{ get_phrase('Upload document') }}</p><form action="{{ route('admin.accountant.documents.store', $accountant) }}" method="POST" enctype="multipart/form-data">@csrf<div class="mb-3"><label for="accountant-document-name" class="eForm-label">{{ get_phrase('File name') }}</label><input class="form-control eForm-control @error('file_name') is-invalid @enderror" id="accountant-document-name" type="text" name="file_name" value="{{ old('file_name') }}" maxlength="100" required />@error('file_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><div class="mb-3"><label for="accountant-document-file" class="eForm-label">{{ get_phrase('File') }}</label><input class="form-control eForm-control-file @error('file') is-invalid @enderror" id="accountant-document-file" type="file" name="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.csv,.txt" required />@error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror</div><button type="submit" class="btn-form float-end">{{ get_phrase('Upload') }}</button></form></div></div></div>
</div>
@endsection
