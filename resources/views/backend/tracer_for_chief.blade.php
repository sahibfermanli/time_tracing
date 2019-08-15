@extends('backend.app')
@section('content')
    <div class="card">
        <div class="card-body">
            <div>
                {{--<div class="form-group col-lg-3" style="display: inline-block; padding-left: 0 !important;">--}}
                    {{--<select class="form-control form-control-sm" id="user_id_where"--}}
                            {{--oninput="get_works_where(this, 'user');">--}}
                        {{--<option value="">User</option>--}}
                        {{--@foreach($users as $user)--}}
                            {{--<option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">--}}
                    {{--<select class="form-control form-control-sm" id="task_id_where"--}}
                            {{--oninput="get_works_where(this, 'task');">--}}
                        {{--<option value="">Task</option>--}}
                        {{--@foreach($tasks as $task)--}}
                            {{--<option value="{{$task->id}}">{{$task->task}}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">--}}
                    {{--<select class="form-control form-control-sm" id="project_id_where"--}}
                            {{--oninput="get_works_where(this, 'project');">--}}
                        {{--<option value="">Project</option>--}}
                        {{--@foreach($projects as $project)--}}
                            {{--<option value="{{$project->id}}">{{$project->project}}</option>--}}
                        {{--@endforeach--}}
                    {{--</select>--}}
                {{--</div>--}}
                {{--<div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">--}}
                    {{--<input type="date" class="form-control form-control-sm" id="date_where"--}}
                           {{--oninput="get_works_where(this, 'date');">--}}
                {{--</div>--}}
                <div class="form-group" style="display: inline-block; padding-left: 0 !important;" id="chart-btn">
                    <button type="button" class="btn btn-primary btn-xs" onclick="show_chart();">Show chart</button>
                </div>
                <div class="form-group" style="display: none; padding-left: 0 !important;" id="back-btn">

                </div>
            </div>
            <div class="card col-lg-6" id="chart-area" style="display: none;">
                <div class="card-body" id="chart-projects">
                    <canvas id="pieChartProjects"></canvas>
                </div>
                <div class="card-body" id="chart-tasks" style="display: none;">
                    <canvas id="pieChartTasks"></canvas>
                </div>
                <div class="card-body" id="chart-works" style="display: none;">
                    <canvas id="pieChartWorks"></canvas>
                </div>
            </div>
            <div>
                <table class="table table-bordered" id="projects_table">
                    <thead>
                    <?php
                    //billable sum
                    $minute = $billable_sum * 10;
                    $hour = round($minute/60, 1);
                    $p_billable_sum = $hour . " hour(s)";

                    //non billable sum
                    $minute = $non_billable_sum * 10;
                    $hour = round($minute/60, 1);
                    $p_non_billable_sum = $hour . " hour(s)";
                    ?>
                    <tr>
                        <th colspan="2">Total:</th>
                        <td>{{$p_billable_sum}}</td>
                        <td>{{$p_non_billable_sum}}</td>
                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Project</th>
                        <th scope="col">Billable</th>
                        <th scope="col">Non billable</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($row = 0)
                    @php($billable = 0)
                    @php($non_billable = 0)
                    @foreach($projects as $project)
                        @php($row++)
                        @php($billable += $project->billable)
                        @php($non_billable += $project->non_billable)
                        <?php
                            //billable
                            $minute = $project->billable * 10;
                            $hour = round($minute / 60, 1);
                            $p_billable = $hour . " hour(s)";

                            //non billable
                            $minute = $project->non_billable * 10;
                            $hour = round($minute / 60, 1);
                            $p_non_billable = $hour . " hour(s)";
                        ?>
                        <tr ondblclick="show_tasks({{$project->id}});">
                            <th scope="row">{{$row}}</th>
                            <td title="{{$project->description}}">{{$project->project}}</td>
                            <td>{{$p_billable}}</td>
                            <td>{{$p_non_billable}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <table class="table table-bordered" id="tasks_table" style="display: none;">
                    <thead>
                    <tr>
                        <th colspan="3">Total:</th>
                        <th id="sum_billable_task"></th>
                        <th id="sum_non_billable_task"></th>
                    </tr>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Task</th>
                        <th scope="col">User</th>
                        <th scope="col">Billable</th>
                        <th scope="col">Non billable</th>
                    </tr>
                    </thead>
                    <tbody id="tasks_body">

                    </tbody>
                </table>

                <table class="table table-bordered" id="works_table" style="display: none;">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Date</th>
                        <th scope="col">Interval</th>
                        <th scope="col">Work</th>
                        <th scope="col">User</th>
                    </tr>
                    </thead>
                    <tbody id="works_body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" href="/css/sweetalert2.min.css">

    <style>
        td {
            white-space: nowrap !important;
        }
    </style>
@endsection

@section('js')
    <script src="/js/jquery.form.min.js"></script>
    <script src="/js/jquery.validate.min.js"></script>
    <script src="/js/sweetalert2.min.js"></script>

    <script src="/js/Chart.bundle.js"></script>

    <script>
        var billable = {{$billable}};
        var non_billable = {{$non_billable}};
        var task_billable = 0;
        var task_non_billable = 0;
        var work_billable = 0;
        var work_non_billable = 0;

        function calculate_time(field) {
            var minute = field * 10;
            var time;

            var hour = minute / 60;
            // minute = minute - (hour * 60);

            time = hour.toFixed(1) + " hour(s)";

            return time;
        }

        function show_chart() {
            $('#chart-area').css('display', 'block');
            $('#chart-btn').html('<button type="button" class="btn btn-danger btn-xs" onclick="hide_chart();">Hide chart</button>');
        }

        function hide_chart() {
            $('#chart-area').css('display', 'none');
            $('#chart-btn').html('<button type="button" class="btn btn-primary btn-xs" onclick="show_chart();">Show chart</button>');
        }

        $(document).ready(function(){
            //pie
            var ctxProjects = document.getElementById("pieChartProjects").getContext('2d');
            var myPieChartProjects = new Chart(ctxProjects, {
                type: 'pie',
                data: {
                    labels: ["Billable", "Non billable"],
                    datasets: [{
                        data: [billable, non_billable],
                        backgroundColor: ["#F7464A", "#FDB45C"],
                        hoverBackgroundColor: ["#FF5A5E", "#FFC870"]
                    }]
                },
                options: {
                    responsive: true
                }
            });
        });

        function show_tasks(project_id) {
            if (project_id === 0 || project_id === '') {
                swal(
                    'Warning',
                    'Please select project',
                    'warning'
                );
            }
            else {
                swal({
                    title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                    text: 'Loading, please wait...',
                    showConfirmButton: false
                });
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "Post",
                    url: '',
                    data: {
                        'project_id': project_id,
                        '_token': CSRF_TOKEN,
                        'type': 'show_tasks'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var tasks = response.tasks;
                            var table = '';
                            var tr = '';
                            var row = 0;
                            task_billable = 0;
                            task_non_billable = 0;

                            for (var i=0; i<tasks.length; i++) {
                                var task = tasks[i];
                                row = i + 1;

                                tr = '<tr ondblclick="show_works(' + task['id'] + ')">';

                                task_billable += task['billable'];
                                task_non_billable += task['non_billable'];

                                var task_td = '<td>' + task['task'] + '</td>';
                                var user = '<td>' + task['name'] + ' ' + task['surname'] + '</td>';
                                var billable_td = '<td>' + calculate_time(task['billable']) + '</td>';
                                var non_billable_td = '<td>' + calculate_time(task['non_billable']) + '</td>';

                                tr = tr + '<th scope="row">' + row + '</th>' + task_td + user + billable_td + non_billable_td + '</tr>';

                                table = table + tr;
                            }

                            $("#sum_billable_task").html(calculate_time(task_billable));
                            $("#sum_non_billable_task").html(calculate_time(task_non_billable));

                            $('#tasks_body').html(table);

                            $('#projects_table').css('display', 'none');
                            $('#tasks_table').css('display', 'table');

                            $('#back-btn').html('<button type="button" class="btn btn-warning btn-xs" onclick="back_to_projects();">Back to projects</button>').css('display', 'inline-block');

                            $('#chart-projects').css('display', 'none');
                            $('#chart-works').css('display', 'none');

                            if (task_billable !== 0 || task_non_billable !== 0) {
                                $('#chart-tasks').css('display', 'block');
                            }

                            var ctxTasks = document.getElementById("pieChartTasks").getContext('2d');
                            var myPieChartTasks = new Chart(ctxTasks, {
                                type: 'pie',
                                data: {
                                    labels: ["Billable", "Non billable"],
                                    datasets: [{
                                        data: [task_billable, task_non_billable],
                                        backgroundColor: ["#F7464A", "#FDB45C"],
                                        hoverBackgroundColor: ["#FF5A5E", "#FFC870"]
                                    }]
                                },
                                options: {
                                    responsive: true
                                }
                            });
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
        }

        function back_to_projects() {
            $('#tasks_table').css('display', 'none');
            $('#works_table').css('display', 'none');
            $('#projects_table').css('display', 'table');
            $('#tasks_body').html('');
            $('#works_body').html('');
            $('#back-btn').css('display', 'none');

            task_billable = 0;
            task_non_billable = 0;
            work_billable = 0;
            work_non_billable = 0;

            $('#chart-tasks').css('display', 'none');
            $('#chart-works').css('display', 'none');
            $('#chart-projects').css('display', 'block');
        }

        function show_works(task_id) {
            if (task_id === 0 || task_id === '') {
                swal(
                    'Warning',
                    'Please select task',
                    'warning'
                );
            }
            else {
                swal({
                    title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                    text: 'Loading, please wait...',
                    showConfirmButton: false
                });
                var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: "Post",
                    url: '',
                    data: {
                        'task_id': task_id,
                        '_token': CSRF_TOKEN,
                        'type': 'show_works'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var works = response.works;
                            var table = '';
                            var tr = '';
                            var row = 0;
                            work_billable = 0;
                            work_non_billable = 0;
                            var start_time = '??:??';
                            var end_time = '??:??';
                            var same_work = '';

                            for (var i=0; i<works.length; i++) {
                                var work = works[i];

                                end_time = work['end_time'].substr(0, 5);
                                if (same_work !== work['same_work']) {
                                    same_work = work['same_work'];
                                    start_time = work['start_time'].substr(0, 5);

                                    row++;
                                    table = table + tr;
                                }

                                tr = '<tr>';

                                if (work['color'] === 'red') {
                                    work_billable++;
                                } else if (work['color'] === 'yellow') {
                                    work_non_billable++;
                                }

                                var work_text = '';
                                if (work['work'].length > 50) {
                                    work_text = work['work'].substr(0, 50) + '...';
                                }
                                else {
                                    work_text = work['work'];
                                }


                                var date = '<td>' + work['created_at'].substr(0, 10) + '</td>';
                                var interval = '<td>' + start_time + ' - ' + end_time + '</td>';
                                var work_td = '<td title="' + work['work'] + '">' + work_text + '</td>';
                                var user = '<td>' + work['name'] + ' ' + work['surname'] + '</td>';

                                tr = tr + '<th scope="row" style="color: ' + work['color'] + '">' + row + '</th>' + date + interval + work_td + user + '</tr>';
                            }

                            table = table + tr;

                            $('#works_body').html(table);

                            $('#projects_table').css('display', 'none');
                            $('#tasks_table').css('display', 'none');
                            $('#works_table').css('display', 'table');

                            $('#back-btn').html('<button type="button" class="btn btn-warning btn-xs" onclick="back_to_tasks();">Back to tasks</button>').css('display', 'inline-block');

                            $('#chart-projects').css('display', 'none');
                            $('#chart-tasks').css('display', 'none');

                            if (work_billable !== 0 || work_non_billable !== 0) {
                                $('#chart-works').css('display', 'block');
                            }

                            var ctxWorks = document.getElementById("pieChartWorks").getContext('2d');
                            var myPieChartWorks = new Chart(ctxWorks, {
                                type: 'pie',
                                data: {
                                    labels: ["Billable", "Non billable"],
                                    datasets: [{
                                        data: [work_billable, work_non_billable],
                                        backgroundColor: ["#F7464A", "#FDB45C"],
                                        hoverBackgroundColor: ["#FF5A5E", "#FFC870"]
                                    }]
                                },
                                options: {
                                    responsive: true
                                }
                            });
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
        }

        function back_to_tasks() {
            $('#works_table').css('display', 'none');
            $('#projects_table').css('display', 'none');
            $('#tasks_table').css('display', 'table');
            $('#works_body').html('');
            $('#back-btn').html('<button type="button" class="btn btn-warning btn-xs" onclick="back_to_projects();">Back to projects</button>').css('display', 'inline-block');

            work_billable = 0;
            work_non_billable = 0;

            $('#chart-projects').css('display', 'none');
            $('#chart-works').css('display', 'none');
            $('#chart-tasks').css('display', 'block');
        }

    </script>
@endsection