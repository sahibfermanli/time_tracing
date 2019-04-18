@extends('backend.app')
@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Users
            <button style="float: right;" type="button" class="btn btn-primary btn-xs" onclick="add_modal();">Add</button>
            <button disabled id="update_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-warning btn-xs" onclick="update_modal();">Update</button>
            <button disabled id="delete_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-danger btn-xs" onclick="del();">Delete</button>
        </h5>
        <div class="card-body">
            <div>
                {!! $users->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Surname</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Username</th>
                    <th scope="col">Role</th>
                    <th scope="col">Level</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr onclick="row_select({{$user->id}});" id="row_{{$user->id}}" class="rows">
                            <th scope="row">{{$user->id}}</th>
                            <td id="name_{{$user->id}}">{{$user->name}}</td>
                            <td id="surname_{{$user->id}}">{{$user->surname}}</td>
                            <td id="email_{{$user->id}}">{{$user->email}}</td>
                            <td id="username_{{$user->id}}">{{$user->username}}</td>
                            <td id="role_{{$user->id}}" role_id="{{$user->role_id}}" title="{{$user->description}}">{{$user->role}}</td>
                            <td id="level_{{$user->id}}" level_id="{{$user->level_id}}" title="{{$user->level_description}}">{{$user->level}}</td>
                            <td>{{$user->created_at}}</td>
                            <td>{{$user->created_name}} {{$user->created_surname}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $users->links(); !!}
            </div>
        </div>
    </div>

    <!-- start add modal-->
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
                                <input type="hidden" id="type" name="type" value="add">
                                <div id="user_id"></div>
                                <div class="form-group row">
                                    <label for="name" class="col-3 col-lg-2 col-form-label text-right">Name</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="name" type="text" required="" name="name" placeholder="name" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="surname" class="col-3 col-lg-2 col-form-label text-right">Surname</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="surname" type="text" required="" name="surname" placeholder="surname" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="email" class="col-3 col-lg-2 col-form-label text-right">E-mail</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="email" type="email" required="" name="email" placeholder="e-mail" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-3 col-lg-2 col-form-label text-right">Username</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="username" type="text" required="" name="username" placeholder="username" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="password" class="col-3 col-lg-2 col-form-label text-right">Password</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="password" type="password" name="password" placeholder="password" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="role_id" class="col-3 col-lg-2 col-form-label text-right">Role</label>
                                    <div class="col-9 col-lg-10">
                                        <select name="role_id" id="role_id" class="form-control" required>
                                            @foreach($roles as $role)
                                                <option value="{{$role->id}}">{{$role->role}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="level_id" class="col-3 col-lg-2 col-form-label text-right">Level</label>
                                    <div class="col-9 col-lg-10">
                                        <select name="level_id" id="level_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($levels as $level)
                                                <option value="{{$level->id}}">{{$level->level}}</option>
                                            @endforeach
                                        </select>
                                    </div>
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

        function row_select(id) {
            row_id = id;

            $(".rows").css('background-color', 'white');
            $('#row_'+row_id).css('background-color', 'rgba(230, 230, 242, .5)');

            $('#update_btn').prop('disabled', false);
            $('#delete_btn').prop('disabled', false);
        }

        function add_modal() {
            $('#type').val('add');
            $('#name').val('');
            $('#surname').val('');
            $('#email').val('');
            $('#username').val('');
            $('#password').val('');
            $('#role_id').val(1);
            $('#level_id').val('');
            $('.modal-title').html('Add user');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var name = $('#name_'+row_id).text();
            var surname = $('#surname_'+row_id).text();
            var email = $('#email_'+row_id).text();
            var username = $('#username_'+row_id).text();
            var role_id = $('#role_'+row_id).attr('role_id');
            var level_id = $('#level_'+row_id).attr('level_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#user_id').html(id_input);
            $('#type').val('update');
            $('#name').val(name);
            $('#surname').val(surname);
            $('#email').val(email);
            $('#username').val(username);
            $('#password').val('');
            $('#role_id').val(role_id);
            $('#level_id').val(level_id);
            $('.modal-title').html('Update user');

            $('#add-modal').modal('show');
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