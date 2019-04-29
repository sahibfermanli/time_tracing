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
                    <th scope="col">Task description</th>
                    <th scope="col">Project</th>
                    <th scope="col">User</th>
                    <th scope="col">Deadline</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                        <tr onclick="row_select({{$task->id}}, {{$task->project_id}});" id="row_{{$task->id}}" class="rows">
                            <th scope="row">{{$task->id}}</th>
                            <td id="task_{{$task->id}}">{{$task->task}}</td>
                            <td id="project_{{$task->id}}" project_id="{{$task->project_id}}">{{$task->project}}</td>
                            <td style="width: 50px;"><span class="btn btn-primary btn-xs" onclick="show_users({{$task->id}});">Show</span></td>
                            <td id="deadline_{{$task->id}}">{{substr($task->deadline, 0, 10)}}</td>
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
                                    <label for="user_id_1">User</label>
                                    <select name="user_id[1]" id="user_id_1" class="form-control" required>
                                        <option value=''>None</option>
                                    </select>
                                </div>
                                <div id="another-user"></div>
                                <span onclick="add_another_user();" class="btn btn-success btn-xs">Add new user</span>
                                <div class="form-group row">
                                    <label for="deadline">Deadline</label>
                                    <input id="deadline" type="date" required="" name="deadline" class="form-control">
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
        var user_id = 1;

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

        function add_another_user() {
            user_id++;
            var new_user = '';
            new_user = '<label for="user_id_' + user_id + '">User ' + user_id + '</label>' +
                '<div id="new_user_' + user_id + '"  style="width: 100%;">' +
                '</div>' +
                '</div>';

            $("#another-user").append('<div class="form-group row">' + new_user + '</div>');

            $("#user_id_1").clone().appendTo("#new_user_"+user_id);
            $("#another-user > .form-group > #new_user_" + user_id + " > select").prop("id", "user_id_"+user_id).prop("name", "user_id["+user_id+"]").prop('required', false).val("");
        }

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

                        $('select[id^="user_id_"]').html(options);
                        // $('#user_id').html(options);
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
            $('#user_id').html("<option value=''>None</option>");

            $('#type').val('add');
            $('#task').val('');
            $('#client_id').val('').prop('required', true);
            $('#project_id').val('');
            $('#user_id').val('');
            $('#deadline').val('');
            $('.modal-title').html('Add task');
            $("#project_id").prop("disabled", true).html("<option value=''>Select client</option>");
            $("#another-user").html('');
            user_id = 1;

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

                        $('#user_id_1').html(options);

                        var task = $('#task_'+row_id).text();
                        var project_id = $('#project_'+row_id).attr('project_id');
                        var project = $('#project_'+row_id).text();
                        var deadline = $('#deadline_'+row_id).text();
                        var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

                        $('#task_id').html(id_input);
                        $('#type').val('update');
                        $('#task').val(task);
                        $('#client_id').val('').prop('required', false);
                        $('#project_id').html("<option value='" + project_id + "' selected>" + project + "</option>").prop("disabled", false);
                        $('#deadline').val(deadline);
                        $('.modal-title').html('Update task');
                        $("#another-user").html('');
                        user_id = 1;

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