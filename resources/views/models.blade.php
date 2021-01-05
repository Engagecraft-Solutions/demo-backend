@extends('layout')

@section('content')
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Created at</th>
        </tr>
        </thead>
        <tbody>@foreach ($models as $model)
            <tr>
                <td>{{ $model->id }}</td>
                <td>{{ $model->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
        {{ $models->links() }}
    </table>
@endsection
