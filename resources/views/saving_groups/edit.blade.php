@extends('layouts.sidebar')

@section('content')
    <div class="container">
        <h1>Edit Saving Group</h1>

        <form action="{{ route('saving_groups.update', $savingGroup->id) }}" method="POST">
            @csrf
            @method('PUT')
            <!-- The rest of the form similar to create.blade.php but with prefilled values -->
        </form>
    </div>
@endsection
