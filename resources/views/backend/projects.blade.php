@extends('backend.app')
@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Projects
            <button style="float: right;" type="button" class="btn btn-primary btn-xs" onclick="add_modal();">Add</button>
            <button disabled id="update_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-warning btn-xs" onclick="update_modal();">Update</button>
            <button disabled id="delete_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-danger btn-xs" onclick="del();">Delete</button>
        </h5>
        <div class="card-body">
            <div>
                {!! $projects->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Project</th>
                    <th scope="col">Description</th>
                    <th scope="col">Client</th>
                    <th scope="col">Project manager</th>
                    <th scope="col" style="width: 60px !important;">Team</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                @php($row = 0)
                    @foreach($projects as $project)
                        @php($row++)
                        <tr onclick="row_select({{$project->id}});" id="row_{{$project->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="project_{{$project->id}}">{{$project->project}}</td>
                            <td id="description_{{$project->id}}">{{$project->description}}</td>
                            <td id="client_{{$project->id}}" client_id="{{$project->client_id}}" title="{{$project->client_director}}">{{$project->client_name}}</td>
                            <td id="project_manager_{{$project->id}}" project_manager_id="{{$project->project_manager_id}}">{{$project->pm_name}} {{$project->pm_surname}}</td>
                            <td><span class="btn btn-primary btn-xs" onclick="show_team({{$project->id}});">Team</span></td>
                            <td>{{$project->created_at}}</td>
                            <td>{{$project->created_name}} {{$project->created_surname}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $projects->links(); !!}
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
                                <div id="project_id"></div>
                                <div class="form-group row">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value=''>Select</option>
                                        @foreach($clients as $client)
                                            <option value="{{$client->id}}">{{$client->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="project">Project</label>
                                    <select name="project_list" id="project_list" class="form-control" required oninput="select_project_list();">
                                        <option value=''>Select</option>
                                        @foreach($project_list as $project)
                                            <option value="{{$project->project}}">{{$project->project}}</option>
                                        @endforeach
                                        <option value="other">Other</option>
                                    </select>
                                    <input id="project_text" type="text" name="project_text" placeholder="project" class="form-control" style="display: none;">
                                </div>
                                <div class="form-group row">
                                    <label for="project_manager_id">Project manager</label>
                                    <select id="project_manager_id" name="project_manager_id" class="form-control">
                                        <option value=''>Select</option>
                                        @foreach($project_managers as $project_manager)
                                            <option value="{{$project_manager->id}}">{{$project_manager->name}} {{$project_manager->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="staff">Staff</label>
                                    <select id="staff" name="staff[]" class="form-control" multiple>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control" placeholder="description"></textarea>
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

    {{-- team modal --}}
    <div class="modal fade" id="team-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Team</h5>
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
                                        </tr>
                                        </thead>
                                        <tbody id="team-body">

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

        function show_team(project_id) {
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
                    'type': 'show_team'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var team  = response.team;
                        var i = 0;
                        var tr = '';
                        var table = '';

                        for (i=0; i<team.length; i++) {
                            var user = team[i];
                            var num = i + 1;
                            var row = '<td>' + num + '</td>';
                            var name = '<td>' + user['name'] + '</td>';
                            var surname = '<td>' + user['surname'] + '</td>';

                            tr = '<tr>' + row + name + surname + '</tr>';

                            table = table + tr;
                        }

                        $('.modal-title').html('Team');
                        $('#team-body').html(table);

                        $('#team-modal').modal('show');
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

        function select_project_list() {
            var project = $('#project_list').val();

            if (project === 'other') {
                $('#project_list').css('display', 'none').prop('required', false);
                $('#project_text').css('display', 'block').prop('required', true).prop('autofocus', true);
            }
        }

        function row_select(id) {
            row_id = id;

            $(".rows").css('background-color', 'white');
            $('#row_'+row_id).css('background-color', 'rgba(230, 230, 242, .5)');

            $('#update_btn').prop('disabled', false);
            $('#delete_btn').prop('disabled', false);
        }

        function add_modal() {
            $('#project_text').css('display', 'none').prop('required', false);
            $('#project_list').css('display', 'block').prop('required', true);

            $('#type').val('add');
            $('#project_text').val('');
            $('#project_list').val('');
            $('#description').val('');
            $('#client_id').val('');
            $('#project_manager_id').val('');
            $('#staff').val('');
            $('.modal-title').html('Add project');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            $('#project_text').css('display', 'none').prop('required', false);
            $('#project_list').css('display', 'block').prop('required', true);

            var project = $('#project_'+row_id).text();
            var description = $('#description_'+row_id).text();
            var client_id = $('#client_'+row_id).attr('client_id');
            var project_manager_id = $('#project_manager_'+row_id).attr('project_manager_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#project_id').html(id_input);
            $('#type').val('update');
            $('#project_list').val(project);
            $('#project_text').val('');
            $('#description').val(description);
            $('#client_id').val(client_id);
            $('#project_manager_id').val(project_manager_id);
            $('.modal-title').html('Update project');

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
                    'project_id': row_id,
                    '_token': CSRF_TOKEN,
                    'type': 'show_team_for_update_project'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var team  = response.team;
                        var team_arr = [];

                        for (var i=0; i<team.length; i++) {
                            team_arr.push(team[i]['user_id']);
                        }

                        console.log(team_arr);

                        $('#staff').val(team_arr);
                    }
                }
            });

            $('#add-modal').modal('show');
        }

        function del() {
            swal({
                title: 'Do you approve the deletion?',
                text: 'If you delete this project, its tasks will also be deleted.',
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