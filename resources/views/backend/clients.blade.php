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
            Clients
        </h5>
        <div class="panel panel-default">
            <div class="panel-body">
                <div id="search-inputs-area" class="search-areas">
                    <input type="text" class="form-control search-input" id="search_values" column_name="name" placeholder="name" value="{{$search_arr['name']}}">
                    <input type="text" class="form-control search-input" id="search_values" column_name="representative" placeholder="representative" value="{{$search_arr['representative']}}">
                    <input type="text" class="form-control search-input" id="search_values" column_name="email" placeholder="email" value="{{$search_arr['email']}}">
                    <input type="text" class="form-control search-input" id="search_values" column_name="phone" placeholder="phone" value="{{$search_arr['phone']}}">
                    <input type="text" class="form-control search-input" id="search_values" column_name="contract" placeholder="contract №" value="{{$search_arr['contract']}}">
                    <select class="form-control search-input" id="search_values" column_name="country" style="min-width: 170px;">
                        <option value="">Country</option>
                        @foreach($countries as $country)
                            @if($country->id == $search_arr['country'])
                                <option selected value="{{$country->id}}">{{$country->country}}</option>
                            @else
                                <option value="{{$country->id}}">{{$country->country}}</option>
                            @endif
                        @endforeach
                    </select>
                    <select class="form-control search-input" style="min-width: 170px;" id="search_industry_id">
                        <option value="">Industry</option>
                        @foreach($industries as $industry)
                            <option value="{{$industry->id}}">{{$industry->category}}</option>
                        @endforeach
                    </select>
                    <select class="form-control search-input search_category_id" id="search_values" column_name="category" style="min-width: 170px;">
                        <option value="">Select industry</option>
                    </select>
                    <button type="button" class="btn btn-primary" onclick="search_data();">Search</button>
                </div>
                <div id="search-type-area" class="search-areas">
                    <label for="date_search">Search by date</label>
                    <input type="checkbox" id="date_search" placeholder="max" onclick="date_area();">
                    <span class="btn" onclick="today_for_date_area();">Today</span>
                </div>
                <div id="search-date-area" class="search-areas">
                    <label for="start_date">Start</label>
                    <input type="date" id="search_values" column_name="start_date" class="form-control search-input start_date_search" value="{{$search_arr['start_date']}}">
                    <label for="end_date">End</label>
                    <input type="date" id="search_values" column_name="end_date" class="form-control search-input end_date_search" value="{{$search_arr['end_date']}}">
                </div>
            </div>
        </div>
        <div class="card-body">
            <div>
                {!! $clients->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.name')">Name</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.director')">Representative</th>
                    <th scope="col" class="columns" onclick="sort_by('c.category')">Category</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.email')">E-mail</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.phone')">Phone</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.web_site')">Web site</th>
                    <th scope="col" class="columns" onclick="sort_by('ct.country')">Country</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.city')">City</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.address')">Address</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.zipcode')">Zip code</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.voen')">TIN</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.account_no')">Account №</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.bank_name')">Bank name</th>
                    <th scope="col" class="columns" onclick="sort_by('bank_voen')">Bank TIN</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.bank_code')">Bank code</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.bank_m_n')">Bank correspondent account</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.bank_swift')">Bank SWIFT BIK</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.contract_no')">Contract №</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.contract_date')">Contract date</th>
                    <th scope="col" class="columns" onclick="sort_by('created.name')">Created by</th>
                    <th scope="col" class="columns" onclick="sort_by('clients.created_at')">Created date</th>
                </tr>
                </thead>
                <tbody>
                @php($row = 0)
                    @foreach($clients as $client)
                        @php($row++)
                        <tr ondblclick="get_projects({{$client->id}})" onclick="row_select({{$client->id}});" id="row_{{$client->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="name_{{$client->id}}" form_of_business_id="{{$client->form_of_business_id}}" company_name="{{$client->name}}">{{$client->name}} {{$client->form_of_business}}</td>
                            <td id="director_{{$client->id}}">{{$client->director}}</td>
                            <td id="category_{{$client->id}}" category_id="{{$client->category_id}}">{{$client->category}}</td>
                            <td id="email_{{$client->id}}">{{$client->email}}</td>
                            <td id="phone_{{$client->id}}">{{$client->phone}}</td>
                            <td id="web_site_{{$client->id}}"><a target="_blank" href="{{$client->web_site}}">{{$client->web_site}}</a></td>
                            <td id="country_{{$client->id}}" country_id="{{$client->country_id}}">{{$client->country}}</td>
                            <td id="city_{{$client->id}}">{{$client->city}}</td>
                            <td id="address_{{$client->id}}">{{$client->address}}</td>
                            <td id="zipcode_{{$client->id}}">{{$client->zipcode}}</td>
                            <td id="voen_{{$client->id}}">{{$client->voen}}</td>
                            <td id="account_no_{{$client->id}}">{{$client->account_no}}</td>
                            <td id="bank_name_{{$client->id}}">{{$client->bank_name}}</td>
                            <td id="bank_voen_{{$client->id}}">{{$client->bank_voen}}</td>
                            <td id="bank_code_{{$client->id}}">{{$client->bank_code}}</td>
                            <td id="bank_m_n_{{$client->id}}">{{$client->bank_m_n}}</td>
                            <td id="bank_swift_{{$client->id}}">{{$client->bank_swift}}</td>
                            <td id="contract_no_{{$client->id}}">{{$client->contract_no}}</td>
                            <td id="contract_date_{{$client->id}}">{{$client->contract_date}}</td>
                            <td>{{$client->created_name}} {{$client->created_surname}}</td>
                            <td>{{$client->created_at}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div>
                {!! $clients->links(); !!}
            </div>
        </div>
    </div>

    <!-- start add modal-->
    <div class="modal fade" id="add-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" data-backdrop="static"
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
                    <div class="container-fluid">
                        <div class="remark-modal"></div>
                        <div class="card-body">
                            <form id="form" data-parsley-validate="" novalidate="" action="" method="post">
                                {{csrf_field()}}
                                <input type="hidden" id="type" name="type" value="add">
                                <div id="client_id"></div>

                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="name" type="text" required name="name" placeholder="* Company name" class="form-control" maxlength="255">
                                    </div>
                                    <div class="col-md-6 ml-auto" id="form_of_business_type">
                                        <select oninput="select_form_of_business();" name="form_of_business_id" id="form_of_business_id" class="form-control" required>
                                            <option value="">* Form of business</option>
                                            @foreach($form_of_businesses as $form_of_business)
                                                <option value="{{$form_of_business->id}}">{{$form_of_business->title}}</option>
                                            @endforeach
                                            <option value="other">Other</option>
                                        </select>
                                        <input id="form_of_business_text" type="text" name="form_of_business_text" placeholder="* Form of business" class="form-control" style="display: none;">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <select id="industry_id" class="form-control" required>
                                            <option value="">* Industry</option>
                                            @foreach($industries as $industry)
                                                <option value="{{$industry->id}}">{{$industry->category}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <select name="category_id" id="category_id" class="form-control" disabled required>
                                            <option value="">* Select industry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="director" type="text" name="director" placeholder="* Representative" class="form-control" required maxlength="255">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="email" type="email" name="email" placeholder="* E-mail" class="form-control" required maxlength="100" onchange="same_client_control(this, 'mail');">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="web_site" type="text" name="web_site" placeholder="* WEB site" class="form-control" maxlength="255" required onchange="same_client_control(this, 'web');">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="phone" type="text" name="phone" placeholder="* Phone" class="form-control" required maxlength="20" onchange="same_client_control(this, 'tel');">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <select name="country_id" id="country_id" class="form-control" required>
                                            <option value="">* Country</option>
                                            @foreach($countries as $country)
                                                <option value="{{$country->id}}">{{$country->country}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="city" type="text" name="city" placeholder="* City" class="form-control" required maxlength="100">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="address" type="text" name="address" placeholder="* Address" class="form-control" required maxlength="255" onchange="same_client_control(this, 'address');">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="zipcode" type="text" name="zipcode" placeholder="* Zip code" class="form-control" required maxlength="20">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="voen" type="text" name="voen" placeholder="TIN" class="form-control" maxlength="100">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="account_no" type="text" name="account_no" placeholder="Account №" class="form-control" maxlength="100">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="bank_name" type="text" name="bank_name" placeholder="Bank name" class="form-control" maxlength="255">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="bank_voen" type="text" name="bank_voen" placeholder="Bank TIN" class="form-control" maxlength="100">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="bank_code" type="text" name="bank_code" placeholder="Bank code" class="form-control" maxlength="30">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="bank_m_n" type="text" name="bank_m_n" placeholder="Correspondent account" class="form-control" maxlength="100">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="bank_swift" type="text" name="bank_swift" placeholder="Bank SWIFT BIK" class="form-control" maxlength="50">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="contract_no" type="text" name="contract_no" placeholder="Contract №" class="form-control" maxlength="50">
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
    <!-- /.end add modal-->
@endsection

@section('css')
    <link rel="stylesheet" href="/css/sweetalert2.min.css">

    <style>
        th, td {
            white-space: nowrap;
        }
    </style>
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
                        let content_message = '';
                        if (response.type === 'validation') {
                            let validation_message = '';
                            $.each(response.content, function(index, value)
                            {
                                if (value.length !== 0)
                                {
                                    for (let i = 0; i < value.length; i++) {
                                        validation_message += value[i] + '\n';
                                    }
                                }
                            });
                            content_message = validation_message;
                        } else {
                            content_message = response.content;
                            $('#add-modal').modal('hide');
                        }
                        swal(
                            response.title,
                            content_message,
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

        function select_form_of_business() {
            var form_of_business_id = $('#form_of_business_id').val();

            if (form_of_business_id === 'other') {
                $('#form_of_business_id').css('display', 'none').prop('required', false);
                $('#form_of_business_text').css('display', 'block').prop('required', true);
            }
        }

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
                                title: 'The client with this ' + column + ' already exists. Do you want to continue?',
                                type: 'warning',
                                showCancelButton: true,
                                cancelButtonText: 'Cancel',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Continue'
                            }).then(function (result) {
                                if (!result.value) {
                                    $('#add-modal').modal('hide');
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

        function add_modal() {
            $('#category_id').html('<option value="">Select industry</option>').prop('disabled', true);

            $('#form_of_business_text').css('display', 'none').prop('required', false);
            $('#form_of_business_id').css('display', 'block').prop('required', true);

            $('#type').val('add');
            $('#name').val('');
            $('#director').val('');
            $('#web_site').val('');
            $('#email').val('');
            $('#phone').val('');
            $('#address').val('');
            $('#city').val('');
            $('#zipcode').val('');
            $('#voen').val('');
            $('#account_no').val('');
            $('#bank_name').val('');
            $('#bank_voen').val('');
            $('#bank_code').val('');
            $('#bank_m_n').val('');
            $('#bank_swift').val('');
            $('#contract_no').val('');
            $('#contract_date').val('');
            $('#category_id').val('');
            $('#industry_id').val('');
            $('#form_of_business_id').val('');
            $('#country_id').val('');
            $('.modal-title').html('Add client');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var category_id = $('#category_'+row_id).attr('category_id');
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
                    'type': 'show_all_categories'
                },
                success: function (response) {
                    if (response.case === 'success') {
                        swal.close();
                        var categories = response.categories;
                        var options = "";
                        var option = '';

                        for (var i=0; i<categories.length; i++) {
                            var category = categories[i];
                            if (category_id == category['id']) {
                                option = '<option selected value="' + category['id'] + '">' + category['category'] + '</option>';
                            } else {
                                option = '<option value="' + category['id'] + '">' + category['category'] + '</option>';
                            }
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
                        return false;
                    }
                }
            });

            $('#form_of_business_text').css('display', 'none').prop('required', false);
            $('#form_of_business_id').css('display', 'block').prop('required', true);

            $('#industry_id').prop('required', false);

            var name = $('#name_'+row_id).attr('company_name');
            var director = $('#director_'+row_id).text();
            var web_site = $('#web_site_'+row_id).text();
            var email = $('#email_'+row_id).text();
            var phone = $('#phone_'+row_id).text();
            var address = $('#address_'+row_id).text();
            var city = $('#city_'+row_id).text();
            var zipcode = $('#zipcode_'+row_id).text();
            var voen = $('#voen_'+row_id).text();
            var account_no = $('#account_no_'+row_id).text();
            var bank_name = $('#bank_name_'+row_id).text();
            var bank_voen = $('#bank_voen_'+row_id).text();
            var bank_code = $('#bank_code_'+row_id).text();
            var bank_m_n = $('#bank_m_n_'+row_id).text();
            var bank_swift = $('#bank_swift_'+row_id).text();
            var contract_no = $('#contract_no_'+row_id).text();
            var contract_date = $('#contract_date_'+row_id).text();
            var form_of_business_id = $('#name_'+row_id).attr('form_of_business_id');
            var country_id = $('#country_'+row_id).attr('country_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#client_id').html(id_input);
            $('#type').val('update');
            $('#name').val(name);
            $('#director').val(director);
            $('#web_site').val(web_site);
            $('#email').val(email);
            $('#phone').val(phone);
            $('#address').val(address);
            $('#city').val(city);
            $('#zipcode').val(zipcode);
            $('#voen').val(voen);
            $('#account_no').val(account_no);
            $('#bank_name').val(bank_name);
            $('#bank_voen').val(bank_voen);
            $('#bank_code').val(bank_code);
            $('#bank_m_n').val(bank_m_n);
            $('#bank_swift').val(bank_swift);
            $('#contract_no').val(contract_no);
            $('#contract_date').val(contract_date);
            $('#form_of_business_id').val(form_of_business_id);
            $('#country_id').val(country_id);
            $('.modal-title').html('Update client');

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

        //show categories
        $('#industry_id').change(function () {
            var up_category_id = $(this).val();
            if (up_category_id === 0 || up_category_id === '') {
                var category_option = "<option value=''>* Select industry</option>";
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

        //show categories
        $('#search_industry_id').change(function () {
            var up_category_id = $(this).val();
            if (up_category_id === 0 || up_category_id === '') {
                var category_option = "<option value=''>Select industry</option>";
                $('.search_category_id').html(category_option);
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

                            $('.search_category_id').html(options).prop('disabled', false);
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

        function get_projects(client_id) {
            let old_url = window.location.href;
            let url_arr = old_url.split("/clients");
            let url;
            url = url_arr[0] + '/projects?search=1&client=' + client_id;
            location.href = url;
        }
    </script>
@endsection
