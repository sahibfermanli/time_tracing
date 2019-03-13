@extends('backend.app')
@section('content')
    <div class="card">
        <div class="card-body">
            <div>
                <div class="form-group col-lg-3" style="display: inline-block; padding-left: 0 !important;">
                    <select class="form-control form-control-sm" id="user_id_where"
                            oninput="get_works_where(this, 'user');">
                        <option value="">User</option>
                        @foreach($users as $user)
                            <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">
                    <select class="form-control form-control-sm" id="task_id_where"
                            oninput="get_works_where(this, 'task');">
                        <option value="">Task</option>
                        @foreach($tasks as $task)
                            <option value="{{$task->id}}">{{$task->task}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">
                    <select class="form-control form-control-sm" id="project_id_where"
                            oninput="get_works_where(this, 'project');">
                        <option value="">Project</option>
                        @foreach($projects as $project)
                            <option value="{{$project->id}}">{{$project->project}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;">
                    <input type="date" class="form-control form-control-sm" id="date_where"
                           oninput="get_works_where(this, 'date');">
                </div>
                <div class="form-group col-lg-2" style="display: inline-block; padding-left: 0 !important;" id="chart-btn">
                    <button type="button" class="btn btn-primary btn-xs" onclick="show_chart();">Show chart</button>
                </div>
            </div>
            <div class="card col-lg-6" id="chart-area" style="display: none;">
                <div class="card-body" id="chart-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
            <div>
                <table class="table table-bordered">
                    <thead>
                    <tr style="background-color: green; color: black;">
                        <th scope="col">#</th>
                        <th scope="col">User</th>
                        <th scope="col">Date</th>
                        <th scope="col">Interval</th>
                        <th scope="col">Project</th>
                        <th scope="col">Task</th>
                        <th scope="col">Work</th>
                    </tr>
                    </thead>
                    <tbody id="works_table">
                    @php($row = 0)
                    @php($yellow_count = 0)
                    @php($red_count = 0)
                    @foreach($works as $work)
                        @php($row++)
                        @php($start_time = substr($work->start_time, 0, 5))
                        @php($end_time = substr($work->end_time, 0, 5))
                        @if(strlen($work->work) > 40)
                            @php($work_text = substr($work->work, 0, 40) . '...')
                        @else
                            @php($work_text = $work->work)
                        @endif

                        @if($work->color == 'yellow')
                            @php($yellow_count++)
                        @elseif($work->color == 'red')
                            @php($red_count++)
                        @endif
                        <tr style="background-color: {{$work->color}}; color: black;">
                            <th scope="row">{{$row}}</th>
                            <td>{{$work->name}} {{$work->surname}}</td>
                            <td>{{date_format($work->created_at, "Y-m-d")}}</td>
                            <td>{{$start_time}} - {{$end_time}}</td>
                            <td title="{{$work->project_desc}}">{{$work->project}}</td>
                            <td title="{{$work->task_desc}}">{{$work->task}}</td>
                            <td title="{{$work->work}}">{{$work_text}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
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

    <script src="/js/Chart.bundle.js"></script>

    <script>
        var red_count = {{$red_count}};
        var yellow_count = {{$yellow_count}};

        function show_chart() {
            $('#chart-area').css('display', 'block');
            $('#chart-btn').html('<button type="button" class="btn btn-warning btn-xs" onclick="hide_chart();">Hide chart</button>');
        }

        function hide_chart() {
            $('#chart-area').css('display', 'none');
            $('#chart-btn').html('<button type="button" class="btn btn-primary btn-xs" onclick="show_chart();">Show chart</button>');
        }

        //pie
        var ctxP = document.getElementById("pieChart").getContext('2d');
        var myPieChart = new Chart(ctxP, {
            type: 'pie',
            data: {
                labels: ["Billable", "Non billable"],
                datasets: [{
                    data: [red_count, yellow_count],
                    backgroundColor: ["#F7464A", "#FDB45C"],
                    hoverBackgroundColor: ["#FF5A5E", "#FFC870"]
                }]
            },
            options: {
                responsive: true
            }
        });

    </script>

    <script>
        function get_works_where(e, column) {
            var value = $(e).val();

            if (value === '') {
                return false;
            }

            if (column === 'date') {
                $('#task_id_where').val('');
                $('#project_id_where').val('');
                $('#user_id_where').val('');
            } else if (column === 'task') {
                $('#date_where').val('');
                $('#project_id_where').val('');
                $('#user_id_where').val('');
            } else if (column === 'project') {
                $('#task_id_where').val('');
                $('#date_where').val('');
                $('#user_id_where').val('');
            } else if (column === 'user') {
                $('#task_id_where').val('');
                $('#date_where').val('');
                $('#project_id_where').val('');
            } else {
                swal(
                    'Warning',
                    'Column not found!',
                    'warning'
                );
            }

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
                    'column': column,
                    'value': value,
                    '_token': CSRF_TOKEN,
                    'type': 'get_works_where'
                },
                success: function (response) {
                    if (response.case === 'success') {
                        swal.close();
                        var works = response.works;
                        var table = '';
                        var tr = '';
                        var row = 0;
                        var new_red_count = 0;
                        var new_yellow_count = 0;

                        for (var i = 0; i < works.length; i++) {
                            row++;
                            var work = works[i];

                            if (work['color'] === 'red') {
                                new_red_count++;
                            } else if (work['color'] === 'yellow') {
                                new_yellow_count++;
                            }

                            tr = '<tr style="background-color: ' + work['color'] + '; color: black;">';
                            var row_id = '<th scope="row">' + row + '</th>';
                            var user = '<td>' + work['name'] + ' ' + work['surname'] + '</td>';
                            var date = '<td>' + work['created_at'].substr(0, 10) + '</td>';
                            var interval = '<td>' + work['start_time'].substr(0, 5) + ' - ' + work['end_time'].substr(0, 5) + '</td>';
                            var project = '<td title="' + work['project_desc'] + '">' + work['project'] + '</td>';
                            var task = '<td title="' + work['task_desc'] + '">' + work['task'] + '</td>';
                            var work_text = work['work'];
                            var work_small_text = '';
                            if (work_text.length > 40) {
                                work_small_text = work_text.substr(0, 40) + '...';
                            } else {
                                work_small_text = work_text;
                            }
                            var work_td = '<td title="' + work_text + '">' + work_small_text + '</td>';

                            tr = tr + row_id + user + date + interval + project + task + work_td;
                            tr = tr + '</tr>';
                            table = table + tr;
                        }

                        $('#works_table').html(table);

                        $('#chart-body').html('<canvas id="pieChart"></canvas>');

                        var ctxP = document.getElementById("pieChart").getContext('2d');
                        var myPieChart = new Chart(ctxP, {
                            type: 'pie',
                            data: {
                                labels: ["Billable", "Non billable"],
                                datasets: [{
                                    data: [new_red_count, new_yellow_count],
                                    backgroundColor: ["#F7464A", "#FDB45C"],
                                    hoverBackgroundColor: ["#FF5A5E", "#FFC870"]
                                }]
                            },
                            options: {
                                responsive: true
                            }
                        });
                    } else {
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