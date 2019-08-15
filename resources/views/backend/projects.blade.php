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
            Projects
        </h5>
        <div class="card-body">
            <div>
                {!! $projects->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Client</th>
                    <th scope="col">Client role</th>
                    <th scope="col">Project's type</th>
                    <th scope="col">Description</th>
                    <th scope="col">SCT</th>
                    <th scope="col">Total payment</th>
                    <th scope="col">Third parties</th>
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
                            <td id="client_{{$project->id}}" client_id="{{$project->client_id}}" title="{{$project->client_director}}">{{$project->client_name}} {{$project->client_fob}}</td>
                            <td id="client_role_{{$project->id}}" client_role_id="{{$project->client_role_id}}">{{$project->client_role}}</td>
                            <td id="project_{{$project->id}}">{{$project->project}}</td>
                            <td id="description_{{$project->id}}">{{$project->description}}</td>
                            <td id="time_{{$project->id}}">{{$project->time}}</td>
                            <td id="currency_{{$project->id}}" currency_id="{{$project->currency_id}}">{{$project->total_payment}} {{$project->currency}}</td>
                            <td><span class="btn btn-primary btn-xs" onclick="show_third_parties({{$project->id}});">Show</span></td>
                            <td id="project_manager_{{$project->id}}" project_manager_id="{{$project->project_manager_id}}">{{$project->pm_name}} {{$project->pm_surname}}</td>
                            <td><span class="btn btn-primary btn-xs" onclick="show_team({{$project->id}}, {{$project->payment_type}});">Team</span></td>
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
                            <div id="project-display">
                                <form id="form" data-parsley-validate="" novalidate="" action="" method="post">
                                    {{csrf_field()}}
                                    <input type="hidden" id="type" name="type" value="add">
                                    <div id="project_id"></div>

                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <label for="client_id">Client</label>
                                            <select name="client_id" id="client_id" class="form-control" required>
                                                <option value=''>Select</option>
                                                @foreach($clients as $client)
                                                    <option value="{{$client->id}}">{{$client->name}} {{$client->fob}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <label for="client_role_id">Client role</label>
                                            <select name="client_role_id" id="client_role_id" class="form-control" required>
                                                <option value=''>Select</option>
                                                @foreach($client_roles as $client_role)
                                                    <option value="{{$client_role->id}}">{{$client_role->role}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <label for="third_party_id_1">Third party</label>
                                            <select name="third_party_id[1]" id="third_party_id_1" class="form-control" oninput="select_third_party(this);">
                                                <option value=''>Select</option>
                                                <option value='new_1' id="new_option">New</option>
                                                @foreach($clients as $client)
                                                    <option value="{{$client->id}}">{{$client->name}} {{$client->fob}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <label for="third_party_role_id_1">Third party role</label>
                                            <select name="third_party_role_id[1]" id="third_party_role_id_1" class="form-control">
                                                <option value=''>Select</option>
                                                @foreach($client_roles as $client_role)
                                                    <option value="{{$client_role->id}}">{{$client_role->role}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div id="another-third-party"></div>
                                    <span onclick="add_another_third_party();" class="btn btn-success btn-xs">Add new third party</span>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <label for="project">Project's type</label>
                                            <select name="project_list" id="project_list" class="form-control" required oninput="select_project_list();">
                                                <option value=''>Select</option>
                                                @foreach($project_list as $project)
                                                    <option value="{{$project->project}}">{{$project->project}}</option>
                                                @endforeach
                                                <option value="other">Other</option>
                                            </select>
                                            <input id="project_text" type="text" name="project_text" placeholder="project" class="form-control" style="display: none;">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <label for="project_manager_id">Project manager</label>
                                            <select id="project_manager_id" name="project_manager_id" class="form-control" required>
                                                <option value=''>Select</option>
                                                @foreach($project_managers as $project_manager)
                                                    <option value="{{$project_manager->id}}">{{$project_manager->name}} {{$project_manager->surname}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-12 ml-auto">
                                            <label for="description">Description</label>
                                            <textarea name="description" id="description" cols="30" rows="2" class="form-control" placeholder="description"></textarea>
                                        </div>
                                    </div>

                                    <input type="hidden" id="delete_staff_input" name="deleted_staffs">
                                    <div class="row form-group" id="team_for_update_table" style="display: none;">
                                        <div class="col-md-12 ml-auto">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Surname</th>
                                                    <th scope="col"></th>
                                                </tr>
                                                </thead>
                                                <tbody id="team_for_update_body">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="row form-group">
                                        <div class="col-md-12 ml-auto staff_class" id="staff_div_1">
                                            <label for="staff_1">Staff</label>
                                            <select name="staff[1][user_id]" id="staff_1" class="form-control" onfocus="save_old_value(this);" oninput="select_staff(this);">
                                                <option value=''>Select</option>
                                                @foreach($users as $user)
                                                    <option value="{{$user->id}}">{{$user->name}} {{$user->surname}}</option>
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
                            <div id="third-party-display" style="display: none;">
                                <span class="btn btn-warning btn-xs" onclick="back_to_add_project();">Back to project</span>
                                <form id="form2" data-parsley-validate="" novalidate="" action="" method="post">
                                    {{csrf_field()}}
                                    <input type="hidden" id="type" name="type" value="add_new_third_party">
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="name" type="text" required name="name" placeholder="Company name" class="form-control" maxlength="255">
                                        </div>
                                        <div class="col-md-6 ml-auto" id="form_of_business_type">
                                            <select oninput="select_form_of_business();" name="form_of_business_id" id="form_of_business_id" class="form-control" required>
                                                <option value="">Form of business</option>
                                                @foreach($form_of_businesses as $form_of_business)
                                                    <option value="{{$form_of_business->id}}">{{$form_of_business->title}}</option>
                                                @endforeach
                                                <option value="other">Other</option>
                                            </select>
                                            <input id="form_of_business_text" type="text" name="form_of_business_text" placeholder="Form of business" class="form-control" style="display: none;">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <select id="industry_id" class="form-control" required>
                                                <option value="">Industry</option>
                                                @foreach($industries as $industry)
                                                    <option value="{{$industry->id}}">{{$industry->category}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <select name="category_id" id="category_id" class="form-control" disabled required>
                                                <option value="">Select industry</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="director" type="text" name="director" placeholder="Representative" class="form-control" required maxlength="255">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="email" type="email" name="email" placeholder="E-mail" class="form-control" required maxlength="100" onchange="same_client_control(this, 'mail');">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="web_site" type="text" name="web_site" placeholder="WEB site" class="form-control" maxlength="255" required onchange="same_client_control(this, 'web');">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="phone" type="text" name="phone" placeholder="Phone" class="form-control" required maxlength="20" onchange="same_client_control(this, 'tel');">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <select name="country_id" id="country_id" class="form-control" required>
                                                <option value="">Country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}">{{$country->country}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="city" type="text" name="city" placeholder="city" class="form-control" required maxlength="100">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="address" type="text" name="address" placeholder="Address" class="form-control" required maxlength="255" onchange="same_client_control(this, 'address');">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="zipcode" type="text" name="zipcode" placeholder="Zip code" class="form-control" required maxlength="20">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="voen" type="text" name="voen" placeholder="TIN" class="form-control">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="account_no" type="text" name="account_no" placeholder="Account №" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="bank_name" type="text" name="bank_name" placeholder="Bank name" class="form-control">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="bank_voen" type="text" name="bank_voen" placeholder="Bank TIN" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="bank_code" type="text" name="bank_code" placeholder="Bank code" class="form-control">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="bank_m_n" type="text" name="bank_m_n" placeholder="Correspondent account" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <input id="bank_swift" type="text" name="bank_swift" placeholder="Bank SWIFT BIK" class="form-control">
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="contract_no" type="text" name="contract_no" placeholder="Contract №" class="form-control">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-md-6 ml-auto">
                                            <label for="contract_date">Contract date</label>
                                        </div>
                                        <div class="col-md-6 ml-auto">
                                            <input id="contract_date" type="date" name="contract_date" placeholder="Account" class="form-control">
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
    </div>
    <!-- /.end add modal-->

    {{--Third parties--}}
    <div class="modal fade" id="third-party-modal" tabindex="-1" role="dialog" data-backdrop="static" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Third parties</h5>
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
                                            <th scope="col">Company</th>
                                            <th scope="col">Role</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody id="third-party-body">

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
                                            <th scope="col" id="team_percentage">Percentage</th>
                                            <th scope="col" id="team_hourly_rate">Hourly rate</th>
                                            <th scope="col" id="team_currency">Currency</th>
                                            <th scope="col"></th>
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
        var tp_id = 1;
        var staff_id = 1;
        var response_tp = 0;

        $(document).ready(function () {
            $('#form').validate();
            $('#form').ajaxForm({
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

            $('#form2').validate();
            $('#form2').ajaxForm({
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
                        $('#third-party-modal').modal('hide');
                        swal.close();
                        var new_third_party_id = response.new_id;
                        var new_third_party = response.new_company;

                        $("#third_party_id_"+response_tp).append("<option selected value='" + new_third_party_id + "'>" + new_third_party + "</option>");
                        $("#third-party-display").css('display', 'none');
                        $("#project-display").css('display', 'block');
                    }
                    else {
                        // $('#third-party-modal').modal('hide');
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                    }
                }
            });
        });

        function same_client_control(e, col) {
            var val = $(e).val();
            if (val.length === 0) {
                return false;
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
                    '_token': CSRF_TOKEN,
                    'type': 'same_client_control',
                    'val': val,
                    'col': col
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var same_control = response.same;
                        if (same_control) {
                            var column = '';
                            switch (col) {
                                case 'mail':
                                    column = 'email';
                                    break;
                                case 'tel':
                                    column = 'phone';
                                    break;
                                case 'web':
                                    column = 'web site';
                                    break;
                                case 'address':
                                    column = 'address';
                                    break;
                                default:
                                    column = 'data';
                            }
                            swal({
                                title: 'The customer with this ' + column + ' already exists. Do you want to continue?',
                                type: 'warning',
                                showCancelButton: true,
                                cancelButtonText: 'Choose this client',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Continue'
                            }).then(function (result) {
                                if (!result.value) {
                                    $("#third_party_id_"+response_tp).val(response.client_id);
                                    $(".modal-title").html("Project");
                                    $("#third-party-display").css('display', 'none');
                                    $("#project-display").css('display', 'block');
                                }
                            });
                        }
                    } else {
                        swal(
                            response.title,
                            response.content,
                            response.case
                        );
                        return false;
                    }
                }
            });
        }
        
        function add_another_third_party() {
            tp_id++;
            var new_third_party = '';
            var new_third_party_role = '';
            new_third_party = '<div class="col-md-6 ml-auto">' +
                                    '<label for="third_party_id_' + tp_id + '">Third party ' + tp_id + '</label>' +
                                    '<div id="new_third_party_' + tp_id + '">' +
                                    '</div>' +
                                '</div>';

            new_third_party_role = '<div class="col-md-6 ml-auto">' +
                                    '<label for="third_party_role_id_' + tp_id + '">Third party role ' + tp_id + '</label>' +
                                        '<div id="new_third_party_role_' + tp_id + '">' +
                                        '</div>' +
                                    '</div>';

            $("#another-third-party").append('<div class="row form-group">' + new_third_party + new_third_party_role + '</div>');

            $("#third_party_id_1").clone().appendTo("#new_third_party_"+tp_id);
            $("#third_party_role_id_1").clone().appendTo("#new_third_party_role_"+tp_id);
            $("#another-third-party > .form-group > .col-md-6 > #new_third_party_" + tp_id + " > select").prop("id", "third_party_id_"+tp_id).prop("name", "third_party_id["+tp_id+"]").val("");
            $("#another-third-party > .form-group > .col-md-6 > #new_third_party_" + tp_id + " > select > #new_option").prop("value", "new_"+tp_id);
            $("#another-third-party > .form-group > .col-md-6 > #new_third_party_role_" + tp_id + " > select").prop("id", "third_party_role_id_"+tp_id).prop("name", "third_party_role_id["+tp_id+"]").val("");
        }

        function add_another_staff() {
            staff_id++;

            var new_staff = '';
            new_staff = '<div class="col-md-12' + ' ml-auto staff_class" id="staff_div_' + staff_id + '">' +
                '<label for="staff_id_' + staff_id + '">Staff ' + staff_id + '</label>' +
                '<div id="new_staff_' + staff_id + '">' +
                '</div>' +
                '</div>';

            $("#another-staff").append('<div class="row form-group">' + new_staff + '</div>');

            $("#staff_1").clone().appendTo("#new_staff_"+staff_id);
            $("#another-staff > .form-group > .staff_class > #new_staff_" + staff_id + " > select").prop("id", "staff_"+staff_id).prop("name", "staff["+staff_id+"][user_id]").val("");
            $("#currency_id_1").clone().appendTo("#new_currency_"+staff_id);
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
                        break;
                    }
                }
                return false;
            }

            if (old_value !== 0) {
                for(j = 0; j < user_arr.length; j++){
                    if ( user_arr[j] === old_value) {
                        user_arr.splice(j, 1);
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
        }

        function back_to_add_project() {
            $(".modal-title").html("Project");
            $("#third-party-display").css('display', 'none');
            $("#project-display").css('display', 'block');
        }

        function select_form_of_business() {
            var form_of_business_id = $('#form_of_business_id').val();

            if (form_of_business_id === 'other') {
                $('#form_of_business_id').css('display', 'none').prop('required', false);
                $('#form_of_business_text').css('display', 'block').prop('required', true);
            }
        }

        function select_third_party(e) {
            var third_party_id = $(e).val();
            var third_party_arr = third_party_id.split('_');
            response_tp = 0;

            if (third_party_arr[0] === 'new') {
                response_tp = third_party_arr[1];
                $(".modal-title").html("Add third party");
                $("#project-display").css('display', 'none');
                $("#third-party-display").css('display', 'block');
            }
        }

        //show categories
        $('#industry_id').change(function () {
            var up_category_id = $(this).val();
            if (up_category_id === 0 || up_category_id === '') {
                var category_option = "<option value=''>Select industry</option>";
                $('#category_id').html(category_option);
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
                            var options = "<option value=''>Category</option>";
                            var option = '';

                            for (var i=0; i<categories.length; i++) {
                                var category = categories[i];
                                option = '<option value="' + category['id'] + '">' + category['category'] + '</option>';
                                options = options + option;
                            }

                            $('#category_id').html(options).prop('disabled', false);
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

        function show_team(project_id, payment_type) {
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
                            var percentage = '<td>' + user['percentage'] + '</td>';
                            var hourly_rate = '<td>' + user['hourly_rate'] + '</td>';
                            var currency = '<td>' + user['currency'] + '</td>';
                            var btn = '<td><span onclick="delete_staff(' + user['id'] + ');"><i class="fa fa-trash" style="color: red;"></i></span></td>';
                            if (payment_type == 2 || payment_type == 3) {
                                $("#team_percentage").css('display', 'table-cell');
                                $("#team_hourly_rate").css('display', 'table-cell');
                                $("#team_currency").css('display', 'table-cell');
                            } else {
                                percentage = '';
                                hourly_rate = '';
                                currency = '';
                                $("#team_percentage").css('display', 'none');
                                $("#team_hourly_rate").css('display', 'none');
                                $("#team_currency").css('display', 'none');
                            }

                            tr = '<tr id="staff_row_' + user['id'] + '">' + row + name + surname + percentage + hourly_rate + currency + btn + '</tr>';

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

        function show_third_parties(project_id) {
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
                    'type': 'show_third_parties'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var third_parties  = response.third_parties;
                        var i = 0;
                        var tr = '';
                        var table = '';

                        for (i=0; i<third_parties.length; i++) {
                            var third_party = third_parties[i];
                            var num = i + 1;
                            var row = '<td>' + num + '</td>';
                            var company = '<td>' + third_party['name'] + ' ' + third_party['fob'] + '</td>';
                            var role = '<td>' + third_party['role'] + '</td>';
                            var btn = '<td><span onclick="delete_third_party(' + third_party['id'] + ');"><i class="fa fa-trash" style="color: red;"></i></span></td>';

                            tr = '<tr id="third_party_row_' + third_party['id'] + '">' + row + company + role + btn + '</tr>';

                            table = table + tr;
                        }

                        $('#third-party-body').html(table);

                        $('#third-party-modal').modal('show');
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

        function delete_third_party(id) {
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
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: "Post",
                        url: '',
                        data: {
                            'tp_id': id,
                            '_token': CSRF_TOKEN,
                            'type': 'delete_third_party'
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
                                $('#third_party_row_'+response.id).remove();
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

        var del_staff = '';
        function delete_staff(id) {
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
                    del_staff += id + ',';
                    $("#delete_staff_input").val(del_staff);
                    $('#staff_row_'+id).remove();
                } else {
                    return false;
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

            $("#third-party-display").css('display', 'none');
            $("#project-display").css('display', 'block');

            $('#type').val('add');
            $('#project_text').val('');
            $('#project_list').val('');
            $('#description').val('');
            $('#client_id').val('');
            $('#client_role_id').val('');
            $('#third_party_id_1').val('');
            $('#third_party_role_id_1').val('');
            $('#project_manager_id').val('');
            $('#staff').val('');
            $('.modal-title').html('Add project');
            $("#another-third-party").html("");
            $("#another-staff").html("");
            $("#staff_1").val('');
            response_tp = 0;
            tp_id = 1;
            staff_id = 1;

            $("#team_for_update_body").html('');
            $("#team_for_update_table").css('display', 'none');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            $('#project_text').css('display', 'none').prop('required', false);
            $('#project_list').css('display', 'block').prop('required', true);

            $("#third-party-display").css('display', 'none');
            $("#project-display").css('display', 'block');

            var project = $('#project_'+row_id).text();
            var description = $('#description_'+row_id).text();
            var client_id = $('#client_'+row_id).attr('client_id');
            var currency_id = $('#currency_'+row_id).attr('currency_id');
            var client_role_id = $('#client_role_'+row_id).attr('client_role_id');
            var project_manager_id = $('#project_manager_'+row_id).attr('project_manager_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#project_id').html(id_input);
            $('#type').val('update');
            $('#project_list').val(project);
            $('#project_text').val('');
            $('#description').val(description);
            $('#client_id').val(client_id);
            $('#client_role_id').val(client_role_id);
            $('#third_party_id_1').val('');
            $('#third_party_role_id_1').val('');
            $('#project_manager_id').val(project_manager_id);
            $('.modal-title').html('Update project');
            $("#another-third-party").html("");
            $("#another-staff").html("");
            $("#staff_1").val('');
            $("#currency_id").val(currency_id);
            response_tp = 0;
            tp_id = 1;
            staff_id = 1;

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
                    'type': 'show_team'
                },
                success: function (response) {
                    swal.close();
                    if (response.case === 'success') {
                        var team = response.team;
                        var tr = '';
                        var table = '';

                        for (i=0; i<team.length; i++) {
                            var user = team[i];
                            var num = i + 1;
                            var row = '<td>' + num + '</td>';
                            var name = '<td>' + user['name'] + '</td>';
                            var surname = '<td>' + user['surname'] + '</td>';
                            var btn = '<td><span onclick="delete_staff(' + user['id'] + ');"><i class="fa fa-trash" style="color: red;"></i></span></td>';

                            tr = '<tr id="staff_row_' + user['id'] + '">' + row + name + surname + btn + '</tr>';

                            table = table + tr;
                        }

                        $("#team_for_update_body").html(table);
                        $("#team_for_update_table").css('display', 'block');
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