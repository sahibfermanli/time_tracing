@extends('backend.app')

@section('buttons')
@endsection

@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Logs
        </h5>
        <div class="card-body">
            <div>
                {!! $logs->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Action</th>
                    <th scope="col">Table</th>
                    <th scope="col">Error</th>
                    <th scope="col">User</th>
                    <th scope="col">Created date</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                        <tr onclick="row_select({{$log->id}});" id="row_{{$log->id}}" class="rows">
                            <th scope="row">{{$log->id}}</th>
                            <td>{{$log->action}}</td>
                            <td>{{$log->table}}</td>
                            <td>{{$log->error_str}}</td>
                            <td>{{$log->username}}</td>
                            <td>{{$log->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $logs->links(); !!}
            </div>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('js')
@endsection
