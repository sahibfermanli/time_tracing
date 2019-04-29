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
            Levels
        </h5>
        <div class="card-body">
            <div>
                {!! $levels->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Level</th>
                    <th scope="col">Description</th>
                    <th scope="col">Percentage</th>
                    <th scope="col">Hourly rate</th>
                    <th scope="col">Currency</th>
                    <th scope="col">Created date</th>
                    <th scope="col">Created by</th>
                </tr>
                </thead>
                <tbody>
                    @php($row = 0)
                    @foreach($levels as $level)
                        @php($row++)
                        <tr onclick="row_select({{$level->id}});" id="row_{{$level->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="level_{{$level->id}}">{{$level->level}}</td>
                            <td id="description_{{$level->id}}">{{$level->description}}</td>
                            <td id="percentage_{{$level->id}}">{{$level->percentage}}</td>
                            <td id="hourly_rate_{{$level->id}}">{{$level->hourly_rate}}</td>
                            <td id="currency_{{$level->id}}" currency_id="{{$level->currency_id}}">{{$level->currency}}</td>
                            <td>{{$level->created_at}}</td>
                            <td>{{$level->created_name}} {{$level->created_surname}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $levels->links(); !!}
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
                                <div id="level_id"></div>
                                <div class="form-group row">
                                    <label for="level" class="col-3 col-lg-2 col-form-label text-right">Level</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="level" type="text" required="" name="level" maxlength="50" placeholder="level" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-3 col-lg-2 col-form-label text-right">Description</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="description" type="text" name="description" maxlength="100" placeholder="description" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="percentage" class="col-3 col-lg-2 col-form-label text-right">Percentage (%)</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="percentage" type="number" required="" name="percentage" min="0" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="hourly_rate" class="col-3 col-lg-2 col-form-label text-right">Hourly rate</label>
                                    <div class="col-9 col-lg-10">
                                        <input id="hourly_rate" type="number" required="" name="hourly_rate" min="0" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="currency_id" class="col-3 col-lg-2 col-form-label text-right">Currency</label>
                                    <div class="col-9 col-lg-10">
                                        <select name="currency_id" id="currency_id" class="form-control" required>
                                            <option value="">Select</option>
                                            @foreach($currencies as $currency)
                                                <option value="{{$currency->id}}">{{$currency->currency}}</option>
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
            $('#level').val('');
            $('#description').val('');
            $('#percentage').val('');
            $('#hourly_rate').val('');
            $('#currency_id').val('');
            $('.modal-title').html('Add level');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var level = $('#level_'+row_id).text();
            var description = $('#description_'+row_id).text();
            var percentage = $('#percentage_'+row_id).text();
            var hourly_rate = $('#hourly_rate_'+row_id).text();
            var currency_id = $('#currency_'+row_id).attr('currency_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#level_id').html(id_input);
            $('#type').val('update');
            $('#level').val(level);
            $('#description').val(description);
            $('#percentage').val(percentage);
            $('#hourly_rate').val(hourly_rate);
            $('#currency_id').val(currency_id);
            $('.modal-title').html('Update level');

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