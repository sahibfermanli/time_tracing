@extends('backend.app')
@section('content')
    @if(session('display') == 'block')
        <div class="alert alert-{{session('class')}}" role="alert">
            {{session('message')}}
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            Clients
            <button style="float: right;" type="button" class="btn btn-primary btn-xs" onclick="add_modal();">Add</button>
            <button disabled id="update_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-warning btn-xs" onclick="update_modal();">Update</button>
            <button disabled id="delete_btn" style="float: right; margin-right: 5px;" type="button" class="btn btn-danger btn-xs" onclick="del();">Delete</button>
        </h5>
        <div class="card-body">
            <div>
                {!! $clients->links(); !!}
            </div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Director</th>
                    <th scope="col">Category</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Address</th>
                    <th scope="col">Zip code</th>
                    <th scope="col">TIN</th>
                    <th scope="col">Account №</th>
                    <th scope="col">Bank name</th>
                    <th scope="col">Bank TIN</th>
                    <th scope="col">Bank code</th>
                    <th scope="col">Bank correspondent account</th>
                    <th scope="col">Bank SWIFT BIK</th>
                    <th scope="col">Contract №</th>
                    <th scope="col">Contract date</th>
                    <th scope="col">Created by</th>
                    <th scope="col">Created date</th>
                </tr>
                </thead>
                <tbody>
                @php($row = 0)
                    @foreach($clients as $client)
                        @php($row++)
                        <tr onclick="row_select({{$client->id}});" id="row_{{$client->id}}" class="rows">
                            <th scope="row">{{$row}}</th>
                            <td id="name_{{$client->id}}">{{$client->name}}</td>
                            <td id="director_{{$client->id}}">{{$client->director}}</td>
                            <td id="category_{{$client->id}}" category_id="{{$client->category_id}}">{{$client->category}}</td>
                            <td id="email_{{$client->id}}">{{$client->email}}</td>
                            <td id="phone_{{$client->id}}">{{$client->phone}}</td>
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
                    <div class="container-fluid">
                        <div class="remark-modal"></div>
                        <div class="card-body">
                            <form id="form" data-parsley-validate="" novalidate="" action="" method="post">
                                {{csrf_field()}}
                                <input type="hidden" id="type" name="type" value="add">
                                <div id="client_id"></div>

                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="name" type="text" required="" name="name" placeholder="Company name" class="form-control">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="director" type="text" name="director" placeholder="Director name" class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-12 ml-auto">
                                        <select name="category_id" id="category_id" class="form-control">
                                            <option value="0">Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->category}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="email" type="email" name="email" placeholder="E-mail" class="form-control">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="phone" type="text" name="phone" placeholder="Phone" class="form-control">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6 ml-auto">
                                        <input id="address" type="text" name="address" placeholder="Address" class="form-control">
                                    </div>
                                    <div class="col-md-6 ml-auto">
                                        <input id="zipcode" type="text" name="zipcode" placeholder="Zip code" class="form-control">
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
            $('#director').val('');
            $('#email').val('');
            $('#phone').val('');
            $('#address').val('');
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
            $('#category_id').val(0);
            $('.modal-title').html('Add client');

            $('#add-modal').modal('show');
        }

        function update_modal() {
            var name = $('#name_'+row_id).text();
            var director = $('#director_'+row_id).text();
            var email = $('#email_'+row_id).text();
            var phone = $('#phone_'+row_id).text();
            var address = $('#address_'+row_id).text();
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
            var category_id = $('#category_'+row_id).attr('category_id');
            var id_input = '<input type="hidden" name="id" value="' + row_id + '">';

            $('#client_id').html(id_input);
            $('#type').val('update');
            $('#name').val(name);
            $('#director').val(director);
            $('#email').val(email);
            $('#phone').val(phone);
            $('#address').val(address);
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
            $('#category_id').val(category_id);
            $('.modal-title').html('Update client');

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