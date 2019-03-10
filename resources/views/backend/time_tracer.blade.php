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
                        {{--@php($field_border_color = $full_field->color)--}}
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
    </div>
    <div style="clear:both;"></div>

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
                                <div id="field_id"></div>
                                <div id="color_div">
                                    <input type="hidden" name="color" value="red">
                                </div>
                                <div id="form_type"></div>
                                <div class="form-group row">
                                    <label for="task_id">Task</label>
                                    <select name="task_id" id="task_id" class="form-control" required>
                                        <option value="">Select task</option>
                                        @foreach($tasks as $task)
                                            <option value="{{$task->id}}">{{$task->task}}</option>
                                        @endforeach
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
    </script>
@endsection