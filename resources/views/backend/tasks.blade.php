@extends('backend.app')

@section('buttons')
    <button style="float: right;" type="button" class="btn btn-primary btn-xs" onclick="add_modal();">Add</button>
    <button disabled id="update_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-warning btn-xs" onclick="update_modal();">Update</button>
    <button disabled id="delete_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-danger btn-xs" onclick="del();">Delete</button>
@endsection

@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Tasks
        </h5>
        <div class="card-body">
            <div>
                {!! $tasks->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Client</th>
                    <th scope="col">Project</th>
                    <th scope="col">Task description</th>
                    <th scope="col">Staff</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">SCT</th>
                    <th scope="col">Fix payment</th>
                    <th scope="col">Total payment</th>
                    <th scope="col">Payment type</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                    @php($row = 0)
                    @foreach($tasks as $task)
                        @php($row++)
                        @php($pay_type = '')
                        @switch($task->payment_type)
                            @case(1)
                            @php($pay_type = 'Fixed Fee')
                            @break
                            @case(2)
                            @php($pay_type = 'Cap Fee')
                            @break
                            @case(3)
                            @php($pay_type = 'Hourly rate')
                            @break
                            @case(4)
                            @php($pay_type = 'Monthly Fee')
                            @break
                            @default()
                            @php($pay_type = '')
                        @endswitch
                        <tr onclick="row_select({{$task->id}}, {{$task->project_id}});" id="row_{{$task->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="client_{{$task->id}}" client_id="{{$task->client_id}}">{{$task->client}} {{$task->fob}}</td>
                            <td id="project_{{$task->id}}" project_id="{{$task->project_id}}">{{$task->project}}</td>
                            <td id="task_{{$task->id}}">{{$task->task}}</td>
                            <td style="width: 50px;"><span class="btn btn-primary btn-xs" onclick="show_users({{$task->id}});">Show</span></td>
                            <td id="deadline_{{$task->id}}">{{substr($task->deadline, 0, 10)}}</td>
                            <td id="time_{{$task->id}}">{{$task->time}}</td>
                            <td id="payment_{{$task->id}}" currency_id="{{$task->currency_id}}">{{$task->payment}} {{$task->currency}}</td>
                            <td id="currency_{{$task->id}}" currency_id="{{$task->currency_id}}">{{$task->total_payment}} {{$task->currency}}</td>
                            <td id="payment_type_{{$task->id}}" payment_type="{{$task->payment_type}}">{{$pay_type}}</td>
                            <td>{{$task->created_at}}</td>
                            <td>{{$task->created_name}} {{$task->created_surname}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $tasks->links(); !!}
            </div>
        </div>
    </div>

    <!-- start add modal-->
    <div class="modal fade" id="add-modal" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="exampleModalLabel"
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
                                <input type="hidden" id="type" name="type" value="add">
                                <div class="form-group row">
                                    <label for="client_id">Client</label>
                                    <select id="client_id" class="form-control" oninput="select_client(this);" required>
                                        <option value=''>Select</option>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}">{{$client->name}} {{$client->fob}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="project_id">Project</label>
                                    <select name="project_id" id="project_id" class="form-control" required oninput="change_project();" disabled>
                                        <option value=''>Select client</option>
                                    </select>
                                </div>
                                <div id="task_id"></div>
                                <div class="form-group row">
                                    <label for="task">Task description</label>
                                    <textarea name="task" id="task" cols="30" rows="5" class="form-control" placeholder="task description" required></textarea>
                                </div>
                                <div class="form-group row">
                                    <label for="deadline">Deadline</label>
                                    <input id="deadline" type="date" required="" name="deadline" class="form-control">
                                </div>

                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <label for="time">Scheduled time for the task</label>
                                        <input id="time" type="number" name="time" placeholder="time (hour)" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <label for="payment_type">Payment type</label>
                                        <select id="payment_type" name="payment_type" class="form-control" required oninput="select_payment_type(this);">
                                            <option value="1">Fixed Fee</option>
                                            <option value="2">Cap Fee</option>
                                            <option value="3">Hourly rate</option>
                                            <option value="4">Monthly Fee</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group" id="fix_div">
                                    <div class="col-md-6 ml-auto">
                                        <label for="fix_payment">Amount of legal fees</label>
                                        <input id="fix_payment" type="number" name="payment" placeholder="fix payment" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <label for="currency_id">Currency</label>
                                        <select name="currency_id" id="currency_id" class="form-control">
                                            <option value=''>Select</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-12 ml-auto staff_class" id="staff_div_1">
                                        <label for="staff_1">Staff</label>
                                        <select name="staff[1][user_id]" id="staff_1" class="form-control" onfocus="save_old_value(this);" oninput="select_staff(this);">
                                            <option value=''>None</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 ml-auto" id="percentage_div_1" style="display: none;">
                                        <label for="percentage_1">Percentage</label>
                                        <input id="percentage_1" type="number" name="staff[1][percentage]" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-3 ml-auto" id="hourly_rate_div_1" style="display: none;">
                                        <label for="hourly_rate_1">Hourly rate</label>
                                        <input id="hourly_rate_1" type="number" name="staff[1][hourly_rate]" class="form-control" min="0">
                                    </div>
                                    <div class="col-md-2 ml-auto" id="currency_div_1" style="display: none;">
                                        <label for="currency_id_1">Currency</label>
                                        <select name="staff[1][currency_id]" id="currency_id_1" class="form-control">
                                            <option value=''>-</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}">{{$currency->currency}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div id="another-staff"></div>
                                <span onclick="add_another_staff();" class="btn btn-warning btn-xs">Add new staff</span>

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
    <!-- /.end add modal-->

    {{-- users modal --}}
    <div class="modal fade" id="users-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Users</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="modal-body-data">
                        <div>
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Surname</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="users-body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
        var row_id = 0;
        var p_id = 0;
        var c_id = 0;
        var staff_id = 1;
        var pay_type = 1; //fix

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
                        // $('#add-modal').modal('hide');
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        });

        function row_select(id, project_id) {
            row_id = id;
            p_id = project_id;

            $(".rows").css('background-color', 'white');
            $('#row_'+row_id).css('background-color', 'rgba(230, 230, 242, .5)');

            $('#update_btn').prop('disabled', false);
            $('#delete_btn').prop('disabled', false);
        }

        //

        function select_payment_type(e, type=1) {
            if (type === 1) {
                pay_type = $(e).val();
            } else if (type === 2) {
                pay_type = e;
            } else {
                swal(
                    'Oops',
                    'Please select payment type',
                    'warning'
                );
            }
            // payment types:
            // 1: fix
            // 2: fix + hourly rate
            // 3: hourly rate
            // 4: monthly

            switch (pay_type) {
                case '1':
                    $("#fix_div").css('display', 'flex');
                    $("#payment").prop("placeholder", 'fix payment');
                    $('div[id^="staff_div"]').removeClass('col-md-4').addClass('col-md-12');
                    $('div[id^="percentage_div"]').css('display', 'none');
                    $('div[id^="hourly_rate_div"]').css('display', 'none');
                    $('div[id^="currency_div"]').css('display', 'none');

                    $('td[id^="team_for_update_percentage"]').css('display', 'none');
                    $('td[id^="team_for_update_hourly_rate"]').css('display', 'none');
                    $('td[id^="team_for_update_currency"]').css('display', 'none');
                    $("#team_for_update_percentage").css('display', 'none');
                    $("#team_for_update_hourly_rate").css('display', 'none');
                    $("#team_for_update_currency").css('display', 'none');

                    break;
                case '2':
                    $("#fix_div").css('display', 'flex');
                    $("#payment").prop("placeholder", 'fix payment');
                    $('div[id^="staff_div"]').removeClass('col-md-12').addClass('col-md-4');
                    $('div[id^="percentage_div"]').css('display', 'block');
                    $('div[id^="hourly_rate_div"]').css('display', 'block');
                    $('div[id^="currency_div"]').css('display', 'block');

                    $('td[id^="team_for_update_percentage"]').css('display', 'table-cell');
                    $('td[id^="team_for_update_hourly_rate"]').css('display', 'table-cell');
                    $('td[id^="team_for_update_currency"]').css('display', 'table-cell');
                    $("#team_for_update_percentage").css('display', 'table-cell');
                    $("#team_for_update_hourly_rate").css('display', 'table-cell');
                    $("#team_for_update_currency").css('display', 'table-cell');

                    break;
                case '3':
                    $("#fix_div").css('display', 'none');
                    $('div[id^="staff_div"]').removeClass('col-md-12').addClass('col-md-4');
                    $('div[id^="percentage_div"]').css('display', 'block');
                    $('div[id^="hourly_rate_div"]').css('display', 'block');
                    $('div[id^="currency_div"]').css('display', 'block');

                    $('td[id^="team_for_update_percentage"]').css('display', 'table-cell');
                    $('td[id^="team_for_update_hourly_rate"]').css('display', 'table-cell');
                    $('td[id^="team_for_update_currency"]').css('display', 'table-cell');
                    $("#team_for_update_percentage").css('display', 'table-cell');
                    $("#team_for_update_hourly_rate").css('display', 'table-cell');
                    $("#team_for_update_currency").css('display', 'table-cell');

                    break;
                case '4':
                    $("#fix_div").css('display', 'flex');
                    $("#payment").prop("placeholder", 'monthly rate');
                    $('div[id^="staff_div"]').removeClass('col-md-4').addClass('col-md-12');
                    $('div[id^="percentage_div"]').css('display', 'none');
                    $('div[id^="hourly_rate_div"]').css('display', 'none');
                    $('div[id^="currency_div"]').css('display', 'none');

                    $('td[id^="team_for_update_percentage"]').css('display', 'none');
                    $('td[id^="team_for_update_hourly_rate"]').css('display', 'none');
                    $('td[id^="team_for_update_currency"]').css('display', 'none');
                    $("#team_for_update_percentage").css('display', 'none');
                    $("#team_for_update_hourly_rate").css('display', 'none');
                    $("#team_for_update_currency").css('display', 'none');

                    break;
                default:
                    swal(
                        'Oops',
                        'Please select payment type',
                        'warning'
                    );
            }
        }

        function add_another_staff() {
            staff_id++;
            var div_style = 'none';
            var col_no = '12';

            switch (pay_type) {
                case '1':
                    div_style = 'none';
                    col_no = '12';
                    break;
                case '2':
                    div_style = 'block';
                    col_no = '4';
                    break;
                case '3':
                    div_style = 'block';
                    col_no = '4';
                    break;
                case '4':
                    div_style = 'none';
                    col_no = '12';
                    break;
                default:
                    div_style = 'none';
                    col_no = '12';
            }

            var new_staff = '';
            var new_percentage = '';
            var new_hourly_rate = '';
            var currency = '';
            new_staff = '<div class="col-md-' + col_no + ' ml-auto staff_class" id="staff_div_' + staff_id + '">' +
                '<label for="staff_id_' + staff_id + '">Staff ' + staff_id + '</label>' +
                '<div id="new_staff_' + staff_id + '">' +
                '</div>' +
                '</div>';

            new_percentage = '<div class="col-md-3 ml-auto" id="percentage_div_' + staff_id + '" style="display: ' + div_style + ';">' +
                '<label for="percentage_id_' + staff_id + '">Percentage ' + staff_id + '</label>' +
                '<div id="new_percentage_' + staff_id + '">' +
                '<input id="percentage_' + staff_id + '" type="number" name="staff[' + staff_id + '][percentage]" class="form-control" min="0">' +
                '</div>' +
                '</div>';

            new_hourly_rate = '<div class="col-md-3 ml-auto" id="hourly_rate_div_' + staff_id + '" style="display: ' + div_style + ';">' +
                '<label for="hourly_rate_id_' + staff_id + '">Hourly rate ' + staff_id + '</label>' +
                '<div id="new_hourly_rate_' + staff_id + '">' +
                '<input id="hourly_rate_' + staff_id + '" type="number" name="staff[' + staff_id + '][hourly_rate]" class="form-control" min="0">' +
                '</div>' +
                '</div>';

            currency = '<div class="col-md-2 ml-auto" id="currency_div_' + staff_id + '" style="display: ' + div_style + ';">' +
                '<label for="currency_id_' + staff_id + '">Currency ' + staff_id + '</label>' +
                '<div id="new_currency_' + staff_id + '">' +
                '</div>' +
                '</div>';

            $("#another-staff").append('<div class="row form-group">' + new_staff + new_percentage + new_hourly_rate + currency + '</div>');

            $("#staff_1").clone().appendTo("#new_staff_"+staff_id);
            $("#another-staff > .form-group > .staff_class > #new_staff_" + staff_id + " > select").prop("id", "staff_"+staff_id).prop("name", "staff["+staff_id+"][user_id]").val("");
            $("#currency_id_1").clone().appendTo("#new_currency_"+staff_id);
            $("#another-staff > .form-group > .col-md-2 > #new_currency_" + staff_id + " > select").prop("id", "currency_id_"+staff_id).prop("name", "staff["+staff_id+"][currency_id]").val("");
        }

        var old_value = 0;
        function save_old_value(e) {
            var id = $(e).val();
            if (id !== '') {
                old_value = id;
            } else {
                old_value = 0;
            }
        }

        var user_arr = [];
        function select_staff(e) {
            var id = $(e).val();
            var attr = $(e).attr("id");
            var no = attr.split("_")[1];
            var j = 0;

            if (id === '' || id === 0 || id === null) {
                for(j = 0; j < user_arr.length; j++){
                    if ( user_arr[j] === old_value) {
                        user_arr.splice(j, 1);
                        console.log(user_arr);
                        break;
                    }
                }
                $("#hourly_rate_"+no).val(0);
                $("#percentage_"+no).val(0);
                return false;
            }

            console.log(user_arr);
            if (old_value !== 0) {
                for(j = 0; j < user_arr.length; j++){
                    if ( user_arr[j] === old_value) {
                        user_arr.splice(j, 1);
                        console.log(user_arr);
                        break;
                    }
                }
            }

            if(jQuery.inArray(id, user_arr) !== -1) {
                swal(
                    'Stop',
                    'This staff already exists',
                    'warning'
                );
                $(e).val('');
                return false;
            } else {
                user_arr.push(id);
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
                    'id': id,
                    '_token': CSRF_TOKEN,
                    'type': 'select_staff'
                },
                success: function (response) {
                    if (response.case === 'success') {
                        swal.close();
                        var staff = response.staff;

                        $("#hourly_rate_"+no).val(staff['hourly_rate']);
                        $("#percentage_"+no).val(staff['percentage']);
                        $("#currency_id_"+no).val(staff['currency_id']);
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

        //

        function show_users(task_id) {
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
                    'type': 'show_users'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var users  = response.users;
                        var i = 0;
                        var tr = '';
                        var table = '';

                        for (i=0; i<users.length; i++) {
                            var user = users[i];
                            var num = i + 1;
                            var row = '<td>' + num + '</td>';
                            var name = '<td>' + user['name'] + '</td>';
                            var surname = '<td>' + user['surname'] + '</td>';
                            var btn = '<td><span onclick="delete_user(' + user['id'] + ');"><i class="fa fa-trash" style="color: red;"></i></span></td>';

                            tr = '<tr id="user_row_' + user['id'] + '">' + row + name + surname + btn + '</tr>';

                            table = table + tr;
                        }

                        $('.modal-title').html('Users');
                        $('#users-body').html(table);

                        $('#users-modal').modal('show');
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

        function delete_user(id) {
            swal({
                title: 'Do you approve the deletion?',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then(function (result) {
                if (result.value) {
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
                            'id': id,
                            '_token': CSRF_TOKEN,
                            'type': 'delete_user'
                        },
                        success: function (response) {
                            swal.close();
                            if (response.case === 'success') {
                                $('#user_row_'+response.id).remove();
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
                } else {
                    return false;
                }
            });
        }

        function select_client(e) {
            c_id = $(e).val();

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
                    'client_id': c_id,
                    '_token': CSRF_TOKEN,
                    'type': 'get_projects_selected_user'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var projects = response.projects;
                        var i = 0;
                        var options = "<option value=''>Select</option>";

                        for (i=0; i<projects.length; i++) {
                            var project = projects[i];
                            var option = '<option value="' + project['id'] + '">' + project['project'] + '</option>';
                            options = options + option;
                        }

                        $('#project_id').html(options).prop("disabled", false);
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

        function change_project() {
            p_id = $('#project_id').val();

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
                    'project_id': p_id,
                    '_token': CSRF_TOKEN,
                    'type': 'get_users'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var users = response.users;
                        var i = 0;
                        var options = "<option value=''>None</option>";

                        for (i=0; i<users.length; i++) {
                            var user = users[i];
                            var option = '<option value="' + user['id'] + '">' + user['name'] + ' ' + user['surname'] + '</option>';
                            options = options + option;
                        }

                        $('select[id^="staff_"]').html(options);
                        // $('#staff_id').html(options);
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

        function add_modal() {
            $('#staff_id').html("<option value=''>None</option>");

            $('#type').val('add');
            $('#task').val('');
            $('#client_id').val('').prop('required', true);
            $('#project_id').val('');
            $('#staff_id').val('');
            $('#deadline').val('');
            $('.modal-title').html('Add task');
            $("#project_id").prop("disabled", true).html("<option value=''>Select client</option>");
            $("#another-user").html('');
            $("#staff_1").val('');
            $("#percentage_1").val('');
            $("#hourly_rate_1").val('');
            $("#currency_id_1").val('');
            $("#fix_payment").val('');
            $("#payment_type").val(1);
            $("#currency_id").val('');
            $('#time').val('');
            staff_id = 1;

            $('#add-modal').modal('show');
        }

        function update_modal() {
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
                    'project_id': p_id,
                    '_token': CSRF_TOKEN,
                    'type': 'get_users'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var users = response.users;
                        var i = 0;
                        var options = "<option value=''>None</option>";

                        for (i=0; i<users.length; i++) {
                            var user = users[i];
                            var option = '<option value="' + user['id'] + '">' + user['name'] + ' ' + user['surname'] + '</option>';
                            options = options + option;
                        }

                        $('#staff_1').html(options);

                        var task = $('#task_'+row_id).text();
                        var project_id = $('#project_'+row_id).attr('project_id');
                        var client_id = $('#client_'+row_id).attr('client_id');
                        var project = $('#project_'+row_id).text();
                        var deadline = $('#deadline_'+row_id).text();
                        var id_input = '<input type="hidden" name="id" value="' + row_id + '">';
                        var payment = $('#payment_'+row_id).text();
                        var payment_type = $('#payment_type_'+row_id).attr('payment_type');
                        var currency_id = $('#currency_'+row_id).attr('currency_id');
                        var time = $('#time_'+row_id).text();

                        $('#task_id').html(id_input);
                        $('#type').val('update');
                        $('#task').val(task);
                        $('#client_id').val(client_id).prop('required', false);
                        $('#project_id').html("<option value='" + project_id + "' selected>" + project + "</option>").prop("disabled", false);
                        $('#deadline').val(deadline);
                        $('.modal-title').html('Update task');
                        $("#another-user").html('');
                        $("#staff_1").val('');
                        $("#percentage_1").val('');
                        $("#hourly_rate_1").val('');
                        $("#currency_id_1").val('');
                        $("#fix_payment").val(parseFloat(payment));
                        $("#payment_type").val(payment_type);
                        $("#currency_id").val(currency_id);
                        $('#time').val(time);
                        staff_id = 1;

                        select_payment_type(payment_type, 2);

                        $('#add-modal').modal('show');
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

        function del() {
            swal({
                title: 'Do you approve the deletion?',
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes!'
            }).then(function (result) {
                if (result.value) {
                    id = row_id;
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: "Post",
                        url: '',
                        data: {
                            'id': id,
                            '_token': CSRF_TOKEN,
                            'type': 'delete'
                        },
                        beforeSubmit: function () {
                            //loading
                            swal({
                                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Please wait...</span>',
                                text: 'Loading, please wait...',
                                showConfirmButton: false
                            });
                        },
                        success: function (response) {
                            if (response.case === 'success') {
                                $('#row_'+response.id).remove();
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
                } else {
                    return false;
                }
            });
        }
    </script>
@endsection