@extends('layouts.sidebar')

@section('content')
<div class="container">
    <h2>إضافة حوالة</h2>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <input hidden type="text"  name="saving_group_id" value="{{$savingGroup->id}}">
            @error('saving_group_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="subscriber" class="form-label">المشترك</label>
            <select class="form-control" name="subscriber_id" required>
                <option value="">اختيار المشترك</option>
                @foreach ($savingGroup->subscribers as $subscriber)
                    <option value="{{ $subscriber->id }}">{{ $subscriber->full_name }}</option>
                @endforeach
            </select>
            @error('subscriber_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror

        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">المبلغ</label>
            <input type="number" min="1" step="0.01" class="form-control" name="amount" required>
            @error('amount')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">وصف (اختياري)</label>
            <input type="text"  class="form-control" name="description" >
            @error('description')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">نوع الحوالة</label>
            <select class="form-control" name="status" required>
                <option value="صادر">صادر</option>
                <option value="وارد">وارد</option>
            </select>
            @error('status')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">إضافة الحوالة</button>
    </form>
</div>
@endsection