@extends('backend.app')
@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Client categories
            <button style="float: right;" type="button" class="btn btn-primary btn-xs" onclick="add_modal();">Add</button>
            <button disabled id="update_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-warning btn-xs" onclick="update_modal();">Update</button>
            <button disabled id="delete_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-danger btn-xs" onclick="del();">Delete</button>
        </h5>
        <div class="card-body">
            <div>
                {!! $categories->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Industry</th>
                    <th scope="col">Category</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                @php($row = 0)
                    @foreach($categories as $category)
                        @php($row++)
                        <tr onclick="row_select({{$category->id}});" id="row_{{$category->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="up_category_{{$category->id}}" up_id="{{$category->up_id}}">{{$category->up_category}}</td>
                            <td id="category_{{$category->id}}">{{$category->category}}</td>
                            <td>{{$category->created_at}}</td>
                            <td>{{$category->created_name}} {{$category->created_surname}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $categories->links(); !!}
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
                                <div id="category_id"></div>
                                <div class="form-group row">
                                    <div class="col-9 col-lg-10">
                                        <label for="up_category">Industry </label>
                                        <select name="up_category" id="up_category" class="form-control">
                                            <option value="0">None</option>
                                            @foreach($up_categories as $up_category)
                                                <option value="{{$up_category->id}}">{{$up_category->category}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-9 col-lg-10">
                                        <label for="category">Category</label>
                                        <input id="category" type="text" required="" name="category" placeholder="category" class="form-control">
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
            $('#category').val('');
            $('#up_category').val(0);
            $('#category_id').html('');
            $('.modal-title').html('Add category');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var category = $('#category_'+row_id).text();
            var up_category = $('#up_category_'+row_id).attr('up_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#category_id').html(id_input);
            $('#type').val('update');
            $('#category').val(category);
            $('#up_category').val(up_category);
            $('.modal-title').html('Update category');

            $('#add-modal').modal('show');
        }

        function del() {
            swal({
                title: 'Do you approve the deletion?',
                text: 'Subcategories of this category will also be deleted during this process.',
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
                                location.reload();
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