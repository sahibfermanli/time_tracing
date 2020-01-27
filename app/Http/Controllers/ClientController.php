<?php

namespace App\Http\Controllers;

use App\ActionLogs;
use App\Categories;
use App\Clients;
use App\Countries;
use App\FormOfBusiness;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClientController extends HomeController
{
    public function get_clients() {
        $query = Clients::leftJoin('users as created', 'clients.created_by', '=', 'created.id')
            ->leftJoin('categories as c', 'clients.category_id', '=', 'c.id')
            ->leftJoin('countries as ct', 'clients.country_id', '=', 'ct.id')
            ->leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')
            ->where(['clients.deleted'=>0]);

        $search_arr = array(
            'name' => '',
            'representative' => '',
            'email' => '',
            'phone' => '',
            'contract' => '',
            'country' => '',
            'category' => '',
            'start_date' => '',
            'end_date' => ''
        );

        if (!empty(Input::get('name')) && Input::get('name') != ''  && Input::get('name') != null) {
            $where_name = Input::get('name');
            $query->where('clients.name', 'LIKE', '%'.$where_name.'%');
            $search_arr['name'] = $where_name;
        }

        if (!empty(Input::get('representative')) && Input::get('representative') != ''  && Input::get('representative') != null) {
            $where_representative = Input::get('representative');
            $query->where('clients.director', 'LIKE', '%'.$where_representative.'%');
            $search_arr['representative'] = $where_representative;
        }

        if (!empty(Input::get('email')) && Input::get('email') != ''  && Input::get('email') != null) {
            $where_email = Input::get('email');
            $query->where('clients.email', 'LIKE', '%'.$where_email.'%');
            $search_arr['email'] = $where_email;
        }

        if (!empty(Input::get('phone')) && Input::get('phone') != ''  && Input::get('phone') != null) {
            $where_phone = Input::get('phone');
            $query->where('clients.phone', 'LIKE', '%'.$where_phone.'%');
            $search_arr['phone'] = $where_phone;
        }

        if (!empty(Input::get('contract')) && Input::get('contract') != ''  && Input::get('contract') != null) {
            $where_contract = Input::get('contract');
            $query->where('clients.contract_no', 'LIKE', '%'.$where_contract.'%');
            $search_arr['contract'] = $where_contract;
        }

        if (!empty(Input::get('country')) && Input::get('country') != ''  && Input::get('country') != null) {
            $where_country = Input::get('country');
            $query->where('clients.country_id', $where_country);
            $search_arr['country'] = $where_country;
        }

        if (!empty(Input::get('category')) && Input::get('category') != ''  && Input::get('category') != null) {
            $where_category = Input::get('category');
            $query->where('clients.category_id', $where_category);
            $search_arr['category'] = $where_category;
        }

        if (!empty(Input::get('start_date')) && Input::get('start_date') != ''  && Input::get('start_date') != null) {
            $where_start_date = Input::get('start_date');
            $query->where('clients.created_at', '>=', $where_start_date);
            $search_arr['start_date'] = $where_start_date;
        }

        if (!empty(Input::get('end_date')) && Input::get('end_date') != ''  && Input::get('end_date') != null) {
            $where_end_date = Input::get('end_date');
            $search_arr['end_date'] = $where_end_date;
            $where_end_date = new DateTime($where_end_date);
            $where_end_date = $where_end_date->modify('+1 day');
            $query->where('clients.created_at', '<=', $where_end_date);
        }

        //short by start
        $short_by = 'clients.id';
        $shortType = 'DESC';
        if (!empty(Input::get('shortBy')) && Input::get('shortBy') != ''  && Input::get('shortBy') != null) {
            $short_by = Input::get('shortBy');
        }
        if (!empty(Input::get('shortType')) && Input::get('shortType') != ''  && Input::get('shortType') != null) {
            $short_type = Input::get('shortType');
            if ($short_type == 2) {
                $shortType = 'DESC';
            } else {
                $shortType = 'ASC';
            }
        }
        //short by finish

        $clients = $query->orderBy($short_by, $shortType)
            ->select('clients.*', 'created.name as created_name', 'created.surname as created_surname', 'c.category as category', 'ct.country', 'fob.title as form_of_business')
            ->paginate(30);

        $industries = Categories::where('up_category', '=', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $categories = Categories::where('up_category', '<>', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $form_of_businesses = FormOfBusiness::where(['deleted'=>0])->select('id', 'title')->get();
        $countries = Countries::where(['deleted'=>0])->select('id', 'country')->get();

        return view('backend.clients')->with(['clients'=>$clients, 'categories'=>$categories, 'form_of_businesses'=>$form_of_businesses, 'countries'=>$countries, 'industries'=>$industries, 'search_arr'=>$search_arr]);
    }

    public function post_clients(Request $request) {
        if ($request->type == 'add') {
            return $this->add_client($request);
        }
        else if ($request->type == 'update') {
            return $this->update_client($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_client($request);
        }
        else if ($request->type == 'show_categories') {
            return $this->show_categories($request);
        }
        else if ($request->type == 'show_all_categories') {
            return $this->show_all_categories();
        }
        else if ($request->type == 'same_client_control') {
            return $this->same_client_control($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //same client control
    private function same_client_control(Request $request) {
        $validator = Validator::make($request->all(), [
            'val' => 'required',
            'col' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Value not found!']);
        }
        try {
            $value = $request->val;
            $column = '';
            switch ($request->col) {
                case 'mail':
                    $column = 'email';
                    break;
                case 'tel':
                    $column = 'phone';
                    break;
                case 'web':
                    $column = 'web_site';
                    break;
                case 'address':
                    $column = 'address';
                    break;
                default:
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Column not found!']);
            }

            $same = false;
            if (Clients::where([$column=>$value, 'deleted'=>0])->count() > 0) {
                $same = true;
            } else {
                $same = false;
            }

            return response(['case' => 'success', 'same'=>$same]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show categories
    private function show_categories(Request $request) {
        $validator = Validator::make($request->all(), [
            'up_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Industry not found!']);
        }
        try {
            $categories = Categories::where(['up_category'=>$request->up_id, 'deleted'=>0])->select('id', 'category')->get();

            return response(['case' => 'success', 'categories'=>$categories]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show categories
    private function show_all_categories() {
        try {
            $categories = Categories::where(['deleted'=>0])->where('up_category', '<>', 0)->select('id', 'category')->get();

            return response(['case' => 'success', 'categories'=>$categories]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add client
    private function add_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'form_of_business_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'city' => ['required', 'string', 'max:100'],
            'director' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer'],
            'email' => ['required', 'string', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'web_site' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:20'],
            'voen' => ['nullable', 'string', 'max:100'],
            'account_no' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_voen' => ['nullable', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:30'],
            'bank_m_n' => ['nullable', 'string', 'max:100'],
            'bank_swift' => ['nullable', 'string', 'max:50'],
            'contract_no' => ['nullable', 'string', 'max:50'],
            'contract_date' => ['nullable', 'date'],
        ]);
        if ($validator->fails()) {
            $validate_arr = $validator->errors()->toArray();
            $validate_str = json_encode($validator->errors()->toJson());
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => 'validate',
                'table' => 'clients - add',
                'error_str' => $validate_str,
                'row_id' => 0
            ]);
            return response(['case' => 'warning', 'title' => 'Warning!', 'type'=>'validation', 'content' => $validate_arr]);
        }
        try {
            unset($request['id']);

            if (isset($request->form_of_business_text) && !empty($request->form_of_business_text)) {
                unset($request['form_of_business_id']);

                $form_of_business = FormOfBusiness::create(['title'=>$request->form_of_business_text, 'created_by'=>Auth::id()]);
                $request['form_business_id'] = $form_of_business->id;
            } else {
                unset($request['form_of_business_text']);
            }

            $request->merge(['created_by'=>Auth::id()]);

            Clients::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => 'catch',
                'table' => 'clients - add',
                'error_str' => $e->getMessage(),
                'row_id' => 0
            ]);
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update client
    private function update_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'form_of_business_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'city' => ['required', 'string', 'max:100'],
            'director' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer'],
            'email' => ['required', 'string', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'web_site' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:20'],
            'voen' => ['nullable', 'string', 'max:100'],
            'account_no' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_voen' => ['nullable', 'string', 'max:100'],
            'bank_code' => ['nullable', 'string', 'max:30'],
            'bank_m_n' => ['nullable', 'string', 'max:100'],
            'bank_swift' => ['nullable', 'string', 'max:50'],
            'contract_no' => ['nullable', 'string', 'max:50'],
            'contract_date' => ['nullable', 'date'],
        ]);
        if ($validator->fails()) {
            $validate_arr = $validator->errors()->toArray();
            $validate_str = json_encode($validator->errors()->toJson());
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => 'validate',
                'table' => 'clients - update',
                'error_str' => $validate_str,
                'row_id' => 0
            ]);
            return response(['case' => 'warning', 'title' => 'Warning!', 'type'=>'validation', 'content' => $validate_arr]);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            if (isset($request->form_of_business_text) && !empty($request->form_of_business_text)) {
                unset($request['form_of_business_id']);

                $form_of_business = FormOfBusiness::create(['title'=>$request->form_of_business_text, 'created_by'=>Auth::id()]);
                $request['form_business_id'] = $form_of_business->id;
            } else {
                unset($request['form_of_business_text']);
            }

            Clients::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => 'catch',
                'table' => 'clients - update',
                'error_str' => $e->getMessage(),
                'row_id' => 0
            ]);
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete client
    private function delete_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            Clients::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            ActionLogs::create([
                'user_id' => Auth::id(),
                'action' => 'catch',
                'table' => 'clients - delete',
                'error_str' => $e->getMessage(),
                'row_id' => 0
            ]);
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
