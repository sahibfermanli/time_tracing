@extends('backend.app')

@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Report
        </h5>
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="search-inputs-area" class="search-areas">
                    <select class="form-control search-input" id="user_id" style="min-width: 170px;">
                        <option value="">Staff</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                        @endforeach
                    </select>
                    <select class="form-control search-input" id="client_id" style="min-width: 170px;" oninput="change_client(this);">
                        <option value="">Client</option>
                        @foreach($clients as $client)
                            <option value="{{$client->id}}">{{$client->name}} {{$client->fob}}</option>
                        @endforeach
                    </select>
                    <select class="form-control search-input" id="project_id" style="min-width: 170px;" oninput="change_project(this);">
                        <option value="">Projects</option>
                        @foreach($projects as $project)
                            <option value="{{$project->id}}">{{$project->project}} | {{$project->client}} {{$project->fob}}</option>
                        @endforeach
                    </select>
                    <select class="form-control search-input" id="task_id" style="min-width: 170px;">
                        <option value="">Task</option>
                        @foreach($tasks as $task)
                            <option value="{{$task->id}}">{{$task->task}} | {{$project->project}} | {{$project->client}} {{$project->fob}}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-primary" onclick="show_report();">Search</button>
                </div>
                <div id="search-type-area" class="search-areas">
                    <label for="date_search">Search by date</label>
                    <input type="checkbox" id="date_search" placeholder="max" onclick="date_area();">
                    <span class="btn" onclick="today_for_date_area();">Today</span>
                </div>
                <div id="search-date-area" class="search-areas">
                    <label for="start_date">Start</label>
                    <input type="date" id="start_date"class="form-control search-input start_date_search">
                    <label for="end_date">End</label>
                    <input type="date" id="end_date" class="form-control search-input end_date_search">
                </div>
            </div>
        </div>
        <div class="row" id="report" style="display: none;">
            <div class="col-md-3" style="margin-left: 15px; display: inline-block;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted">Total</h5>
                            <h2 class="mb-0" id="total_time"></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin-left: 15px; display: inline-block;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted" style="color: green !important;">Billable</h5>
                            <h2 class="mb-0" id="billable_time" style="color: green !important;"></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="margin-left: 15px; display: inline-block;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-inline-block">
                            <h5 class="text-muted" style="color: red !important;">Non billable</h5>
                            <h2 class="mb-0" id="non_billable_time" style="color: red !important;"></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/sweetalert2.min.css">
@endsection

@section('js')
    <script src="/js/jquery.form.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
    <script src="/js/sweetalert2.min.js"></script>

    <script>
        function change_client(e) {
            let c_id = $(e).val();

            if (c_id === '') {
                $('#project_id').html("<option value=''>Project</option>");
                $('#task_id').html("<option value=''>Task</option>");
                return false;
            }

            swal({
                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                text: 'Loading, please wait...',
                showConfirmButton: false
            });
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "Post",
                url: '{{route("report_change_client")}}',
                data: {
                    'client_id': c_id,
                    '_token': CSRF_TOKEN,
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var projects = response.projects;
                        var i = 0;
                        var options = "<option value=''>Project</option>";
                        var option = '';

                        for (i=0; i<projects.length; i++) {
                            var project = projects[i];
                            option = '<option value="' + project['id'] + '">' + project['project'] + '</option>';
                            options = options + option;
                        }

                        $('#project_id').html(options);

                        var tasks = response.tasks;
                        i = 0;
                        options = "<option value=''>Task</option>";

                        for (i=0; i<tasks.length; i++) {
                            var task = tasks[i];
                            option = '<option value="' + task['id'] + '">' + task['task'] + ' | ' + task['project'] + '</option>';
                            options = options + option;
                        }

                        $("#task_id").html(options);
                    }
                    else {
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        }

        function change_project(e) {
            let p_id = $(e).val();

            if (p_id === '') {
                $('#task_id').html("<option value=''>Task</option>");
                return false;
            }

            swal({
                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                text: 'Loading, please wait...',
                showConfirmButton: false
            });
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "Post",
                url: '{{route("report_change_project")}}',
                data: {
                    'project_id': p_id,
                    '_token': CSRF_TOKEN,
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var tasks = response.tasks;
                        var i = 0;
                        var options = "<option value=''>Task</option>";

                        for (i=0; i<tasks.length; i++) {
                            var task = tasks[i];
                            let option = '<option value="' + task['id'] + '">' + task['task'] + '</option>';
                            options = options + option;
                        }

                        $("#task_id").html(options);
                    }
                    else {
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        }

        function calculate_time(field) {
            var minute = field * 10;
            var time;

            var hour = minute / 60;
            // minute = minute - (hour * 60);

            time = hour.toFixed(1) + " hour(s)";

            return time;
        }

        function show_report() {
            let user_id = $("#user_id").val();
            let client_id = $("#client_id").val();
            let project_id = $("#project_id").val();
            let task_id = $("#task_id").val();
            let start_date = $("#start_date").val();
            let end_date = $("#end_date").val();

            swal({
                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                text: 'Loading, please wait...',
                showConfirmButton: false
            });
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: "Post",
                url: '{{route("show_report")}}',
                data: {
                    'user_id': user_id,
                    'client_id': client_id,
                    'project_id': project_id,
                    'task_id': task_id,
                    'start_date': start_date,
                    'end_date': end_date,
                    '_token': CSRF_TOKEN,
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        let total = response.total;
                        let billable = response.billable;
                        let non_billable = response.non_billable;

                        $("#total_time").html(calculate_time(total));
                        $("#billable_time").html(calculate_time(billable));
                        $("#non_billable_time").html(calculate_time(non_billable));

                        $("#report").css("display", 'block');
                    }
                    else {
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        }
    </script>
@endsection