@extends('layouts.app')

@section('title', ($quan->ten_quan ?? 'Cap nhat quan') . ' | Quan Moi')

@section('content')
    @include('chu-quan.partials.restaurant-form', [
        'isEditing' => true,
        'pageTitle' => 'Cap nhat va mo rong trang quan cua ban',
        'pageSubtitle' => 'Sua ten quan, anh bia, dia chi, link affiliate va mo ta rich text de trang public hien day du hon.',
        'heroBadge' => 'Edit venue',
        'formAction' => route('chu-quan.quan.update', ['slug' => $quan->slug]),
        'formMethod' => 'PUT',
        'backUrl' => route('chu-quan.quan.show', ['slug' => $quan->slug]),
        'submitPrimaryLabel' => 'Cap nhat thong tin',
        'draftLabel' => 'Luu nhap thay doi',
    ])
@endsection
