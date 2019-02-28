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
    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                                    <label for="project">Project</label>
                                    <input id="project" type="text" required="" name="project" placeholder="project" class="form-control">
                                </div>
                                <div class="form-group row">
                                    <label for="up_category_id">Up category</label>
                                    <select id="up_category_id" class="form-control">
                                        <option value=''>Select</option>
                                        @foreach($up_categories as $up_category)
                                            <option value="{{$up_category->id}}">{{$up_category->category}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="category_id">Category</label>
                                    <select id="category_id" class="form-control">
                                        <option value=''>Please select up category</option>
                                        {{--@foreach($clients as $client)--}}
                                        {{--<option value="{{$client->id}}">{{$client->name}}</option>--}}
                                        {{--@endforeach--}}
                                    </select>
                                </div>
                                <div class="form-group row">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" id="client_id" class="form-control" required>
                                        <option value=''>Please select category</option>
                                        {{--@foreach($clients as $client)--}}
                                            {{--<option value="{{$client->id}}">{{$client->name}}</option>--}}
                                        {{--@endforeach--}}
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
            $('#project').val('');
            $('#description').val('');
            $('#client_id').val('');
            $('.modal-title').html('Add project');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var project = $('#project_'+row_id).text();
            var description = $('#description_'+row_id).text();
            var client_id = $('#client_'+row_id).attr('client_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#project_id').html(id_input);
            $('#type').val('update');
            $('#project').val(project);
            $('#description').val(description);
            $('#client_id').val(client_id);
            $('.modal-title').html('Update project');

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

        //show categories
        $('#up_category_id').change(function () {
            var up_category_id = $(this).val();
            if (up_category_id === 0 || up_category_id === '') {
                var category_option = "<option value=''>Please select up category</option>";
                $('#category_id').html(category_option);
                var client_option = "<option value=''>Please select category</option>";
                $('#client_id').html(client_option);
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
                        'up_id': up_category_id,
                        '_token': CSRF_TOKEN,
                        'type': 'show_categories'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var categories = response.categories;
                            var options = "<option value=''>Select</option>";
                            var option = '';

                            for (var i=0; i<categories.length; i++) {
                                var category = categories[i];
                                option = '<option value="' + category['id'] + '">' + category['category'] + '</option>';
                                options = options + option;
                            }

                            $('#category_id').html(options);
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
        });

        //show clients
        $('#category_id').change(function () {
            var category_id = $(this).val();
            if (category_id === 0 || category_id === '') {
                var client_option = "<option value=''>Please select category</option>";
                $('#client_id').html(client_option);
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
                        'category_id': category_id,
                        '_token': CSRF_TOKEN,
                        'type': 'show_clients'
                    },
                    success: function (response) {
                        if (response.case === 'success') {
                            swal.close();
                            var clients = response.clients;
                            console.log(clients);
                            var options = "<option value=''>Select</option>";
                            var option = '';

                            for (var i=0; i<clients.length; i++) {
                                var client = clients[i];
                                option = '<option value="' + client['id'] + '">' + client['name'] + '</option>';
                                options = options + option;
                            }

                            $('#client_id').html(options);
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
        });
    </script>
@endsection