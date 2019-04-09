@extends('backend.app')
@section('content')
    <div class="first-section">
        @if(session('display') == 'block')
            <div class="alert alert-{{session('class')}}" role="alert">
                {{session('message')}}
            </div>
        @endif

        <div style="clear:both;"></div>
        <div style="position: relative;">
            <div>
                <div class="form-group col-lg-1" style="display: inline-block; padding-left: 0 !important;">
                    <select class="form-control form-control-sm" id="start_time" oninput="select_start_time();">
                        <option value="">Start</option>
                        @foreach($fields as $start_time)
                            @php($start = substr($start_time->start_time, 0, 5))
                            <option value="{{$start}}">{{$start}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-1" style="display: inline-block;">
                    <select class="form-control form-control-sm" id="end_time" oninput="select_end_time();" disabled>
                        <option value="">End</option>
                    </select>
                </div>
                <div class="form-group col-lg-3" style="display: inline-block;">
                    <button type="button" class="btn btn-primary btn-xs" id="work_btn" disabled onclick="add_work_with_time();">Add work</button>
                </div>
            </div>

            @php($field_no = 0)
            @foreach($fields as $field)
                @php($field_color = '#c5eace')
                @php($field_border_color = '#28a745')
                @php($work = 'Empty')
                @php($task = '')
                @php($cursor = 'pointer')
                @php($modal = '1')
                @php($start_time = substr($field->start_time, 0, 5))
                @php($end_time = substr($field->end_time, 0, 5))
                @foreach($full_fields as $full_field)
                    @if($field->id == $full_field->field_id)
                        @php($field_color = $full_field->color)
                        @php($work = "<p>".$full_field->work."</p>")
                        @php($task = "<h5>".$full_field->task."</h5>")
                        @php($cursor = "default")
                        @php($modal = "0")
                        @break
                    @endif
                @endforeach
                @php($field_no++)
                <div class="hoverWrapper" onclick="add_work_modal({{$modal}}, {{$field->id}}, '{{$start_time}}', '{{$end_time}}');" style="cursor: {{$cursor}};">
                    <div class="zaman-2 firstchild" data-toggle="tooltip" data-placement="top" data-original-title="{{$start_time}}-{{$end_time}}" style="background-color: {{$field_color}}; border-color: {{$field_border_color}};"><center><span>{{$field_no}}</span></center></div>
                    <div class="hoverShow1">
                        {!! $task !!}
                        {!! $work !!}
                    </div>
                </div>
            @endforeach
        </div>
        <div style="clear:both;"></div>
        <div class="card" style="top: 10px;">
            <div class="card-body">
                <div>
                    <div class="form-group col-lg-3" style="display: inline-block; padding-left: 0 !important;">
                        <select class="form-control form-control-sm" id="task_id_where" oninput="get_works_where(this, 'task');">
                            <option value="">Task</option>
                            @foreach($tasks as $task)
                                <option value="{{$task->id}}">{{$task->task}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3" style="display: inline-block; padding-left: 0 !important;">
                        <select class="form-control form-control-sm" id="project_id_where" oninput="get_works_where(this, 'project');">
                            <option value="">Project</option>
                            @foreach($projects as $project)
                                <option value="{{$project->id}}">{{$project->project}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-lg-3" style="display: inline-block; padding-left: 0 !important;">
                        <input type="date" class="form-control form-control-sm" id="date_where" oninput="get_works_where(this, 'date');">
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
                            <th scope="col">Date</th>
                            <th scope="col">Interval</th>
                            <th scope="col">Project</th>
                            <th scope="col">Task</th>
                            <th scope="col">Work</th>
                        </tr>
                        </thead>
                        <tbody id="works_table">
                        <?php
                        $row = 0;
                        $yellow_count = 0;
                        $red_count = 0;
                        $same_work = '';
                        $start_time = '??:??';
                        $end_time = '??:??';
                        $tr = '';
                        $completed = false;

                        foreach ($works as $work) {
                            if ($work->completed == 1) {
                                $completed = true;
                            }

                            $end_time = substr($work->end_time, 0, 5);
                            if ($same_work != $work->same_work) {
                                $same_work = $work->same_work;
                                $start_time = substr($work->start_time, 0, 5);

                                $row++;
                                echo $tr;
                            }

                            if (strlen($work->work) > 40) {
                                $work_text = substr($work->work, 0, 40) . '...';
                            } else {
                                $work_text = $work->work;
                            }

                            if ($work->color == 'yellow') {
                                $yellow_count++;
                            } else {
                                $red_count++;
                            }

                            $tr = '<tr style="background-color:' . $work->color . '; color: black;">';
                            $tr .= '<th scope="row">' . $row . '</th>';
                            $tr .= '<td>' . date_format($work->created_at, "Y-m-d") . '</td>';
                            $tr .= '<td>' . $start_time . ' - ' . $end_time . '</td>';
                            $tr .= '<td title="' . $work->project_desc . '">' . $work->project . '</td>';
                            $tr .= '<td>' . $work->task . '</td>';
                            $tr .= '<td title="' . $work->work . '">' . $work_text . '</td>';
                            $tr .= '</tr>';
                        }

                        echo $tr;
                        ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <div id="complete-btn">
                    @if(!$completed)
                        <button type="button" class="btn btn-success btn-xs" style="float: right;" onclick="complete_works();">Complete</button>
                    @else
                        <button type="button" class="btn btn-warning btn-xs" style="float: right;" disabled>Complete</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div style="clear:both;"></div>

    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-data">
                        <div class="remark-modal"></div>
                        <div class="card-body">
                            <form id="form" data-parsley-validate="" novalidate="" action="" method="post">
                                {{csrf_field()}}
                                <div id="field_id"></div>
                                <div id="color_div">
                                    <input type="hidden" name="color" value="red">
                                </div>
                                <div id="form_type"></div>
                                <div class="form-group row">
                                    <label for="project_id">Project</label>
                                    <select id="project_id" class="form-control" required oninput="select_project();">
                                        <option value="">Select project</option>
                                        @foreach($projects as $project)
                                            <option value="{{$project->id}}">{{$project->project}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="task_id">Task</label>
                                    <select name="task_id" id="task_id" class="form-control" required disabled>
                                        <option value="">Select task</option>
                                    </select>
                                </div>
                                <label class="custom-control custom-checkbox" id="work_type" oninput="change_work_type();">
                                    <input type="checkbox" class="custom-control-input"><span class="custom-control-label">select from list</span>
                                </label>
                                <div class="form-group row" id="non_billable_code_div" style="display: none;">
                                    <label for="non_billable_code_id">Non billable code</label>
                                    <select name="del" id="non_billable_code" class="form-control">
                                        <option value="">Select</option>
                                        @foreach($non_billable_codes as $non_billable_code)
                                            <option value="{{$non_billable_code->title}}">{{$non_billable_code->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row" id="work_div">
                                    <label for="work_text">What have I done?</label>
                                    <textarea name="work" id="work_text" cols="30" rows="5" class="form-control" maxlength="4000"></textarea>
                                </div>
                                <div class="row pt-2 pt-sm-5 mt-1">
                                    <div class="col-sm-6 pl-0">
                                        <button type="submit" class="btn btn-space btn-primary">Submit</button>
                                        <button type="reset" class="btn btn-space btn-secondary">Clear</button>
                                    </div>
                                </div>
                            </form>
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
        var field_id;
        var work_type = false;

        $(document).ready(function () {
            $('form').validate();
            $('form').ajaxForm({
                beforeSubmit:function () {
                    //loading
                    swal ({
                        title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                        text: 'Loading, please wait...',
                        showConfirmButton: false
                    });
                },
                success:function (response) {
                    if (response.case === 'success') {
                        location.reload();
                    }
                    else {
                        $('#add-modal').modal('hide');
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        });

        function complete_works() {
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
                    '_token': CSRF_TOKEN,
                    'type': 'complete_works'
                },
                success: function (response) {
                    if (response.case === 'success') {
                        $('#complete-btn').html('<button type="button" class="btn btn-warning btn-xs" style="float: right;" disabled>Complete</button>');
                    }

                    swal(
                        response.title,
                        response.content,
                        response.case
                    );
                }
            });
        }

        function select_project() {
            var project_id = $('#project_id').val();
            if (project_id === '' || project_id === 0) {
                $('#task_id').html('<option value="">Select task</option>').prop('disabled', true);
                return false;
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
                        'type': 'select_project_for_tasks'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var tasks = response.tasks;
                            var options = "<option value=''>Select task</option>";
                            var option = '';

                            for (var i=0; i<tasks.length; i++) {
                                var task = tasks[i];
                                option = '<option value="' + task['id'] + '">' + task['task'] + '</option>';
                                options = options + option;
                            }

                            $('#task_id').html(options).prop('disabled', false);
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

        function add_work_modal(show_modal, id, start_time, end_time) {
            if (show_modal === 1) {
                var title = start_time + '-' + end_time;
                $('.modal-title').html(title);

                field_id = id;
                $('#field_id').html("<input name='field_id' type='hidden' value='" + id + "'>");
                $('#form_type').html('<input type="hidden" name="type" value="add_work_with_field">');

                $('#add-modal').modal('show');
            }
            else {
                return false;
            }
        }

        function change_work_type() {
            if (work_type === false) {
                work_type = true;
                //secim
                $('#work_text').val('');
                $('#work_div').css('display', 'none');
                $('#non_billable_code_div').css('display', 'block');
                $('#work_text').attr('name', 'del');
                $('#non_billable_code').attr('name', 'work');
                $('#color_div').html('<input type="hidden" name="color" value="yellow">');
            } else {
                work_type = false;
                //elle
                $('#non_billable_code').val('');
                $('#non_billable_code_div').css('display', 'none');
                $('#work_div').css('display', 'block');
                $('#work_text').attr('name', 'work');
                $('#non_billable_code').attr('name', 'del');
                $('#color_div').html('<input type="hidden" name="color" value="red">');
            }
        }

        function select_start_time() {
            var start_time = $('#start_time').val();
            if (start_time === '') {
                $('#end_time').html('<option value="">End</option>').prop('disabled', true);
                $('#work_btn').prop('disabled', true);
                return false;
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
                        'start_time': start_time,
                        '_token': CSRF_TOKEN,
                        'type': 'select_start_time'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var end_times = response.end_times;
                            var options = "<option value=''>End</option>";
                            var option = '';

                            for (var i=0; i<end_times.length; i++) {
                                var end = end_times[i];
                                option = '<option value="' + end['end_time'].substr(0, 5) + '">' + end['end_time'].substr(0, 5) + '</option>';
                                options = options + option;
                            }

                            $('#end_time').html(options).prop('disabled', false);
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

        function select_end_time() {
            var end_time = $('#end_time').val();
            if (end_time === '') {
                $('#work_btn').prop('disabled', true);
            }
            else {
                $('#work_btn').prop('disabled', false);
            }
        }

        function add_work_with_time() {
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            if (start_time === '' || end_time === '') {
                swal(
                    'Warning',
                    'Please select start time and end time',
                    'warning'
                );
                return false;
            }
            else {
                var title = start_time + '-' + end_time;
                $('.modal-title').html(title);

                $('#field_id').html("<input name='start_time' type='hidden' value='" + start_time + "'><input name='end_time' type='hidden' value='" + end_time + "'>");
                $('#form_type').html('<input type="hidden" name="type" value="add_work_with_time">');

                $('#add-modal').modal('show');
            }
        }

        function get_works_where(e, column) {
            var value = $(e).val();

            if (value === '') {
                return false;
            }

            $('#complete-btn').html('');

            if (column === 'date') {
                $('#task_id_where').val('');
                $('#project_id_where').val('');
            } else if (column === 'task') {
                $('#date_where').val('');
                $('#project_id_where').val('');
            } else if (column === 'project') {
                $('#task_id_where').val('');
                $('#date_where').val('');
            }
            else {
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
                        var same_work = '';
                        var start_time = '??:??';
                        var end_time = '??:??';

                        for (var i=0; i<works.length; i++) {
                            var work = works[i];

                            end_time = work['end_time'].substr(0, 5);
                            if (same_work !== work['same_work']) {
                                same_work = work['same_work'];
                                start_time = work['start_time'].substr(0, 5);

                                row++;
                                table = table + tr;
                            }

                            if (work['color'] === 'red') {
                                new_red_count++;
                            } else if (work['color'] === 'yellow') {
                                new_yellow_count++;
                            }

                            tr = '<tr style="background-color: ' + work['color'] + '; color: black;">';
                            var row_id = '<th scope="row">' + row + '</th>';
                            var date = '<td>' + work['created_at'].substr(0, 10) + '</td>';
                            var interval = '<td>' + start_time + ' - ' + end_time + '</td>';
                            var project = '<td title="' + work['project_desc'] + '">' + work['project'] + '</td>';
                            var task = '<td>' + work['task'] + '</td>';
                            var work_text = work['work'];
                            var work_small_text = '';
                            if (work_text.length > 40) {
                                work_small_text = work_text.substr(0, 40) + '...';
                            }
                            else {
                                work_small_text = work_text;
                            }
                            var work_td = '<td title="' + work_text + '">' + work_small_text + '</td>';

                            tr = tr + row_id + date + interval + project + task + work_td;
                            tr = tr + '</tr>';
                        }

                        table = table + tr;

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