<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Clients;
use App\Countries;
use App\FormOfBusiness;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClientController extends HomeController
{
    public function get_clients() {
        $clients = Clients::leftJoin('users as created', 'clients.created_by', '=', 'created.id')->leftJoin('categories as c', 'clients.category_id', '=', 'c.id')->leftJoin('countries as ct', 'clients.country_id', '=', 'ct.id')->leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->select('clients.*', 'created.name as created_name', 'created.surname as created_surname', 'c.category as category', 'clients.country_id', 'ct.country', 'clients.city', 'clients.form_of_business_id', 'fob.title as form_of_business')->paginate(30);
        $industries = Categories::where('up_category', '=', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $categories = Categories::where('up_category', '<>', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $form_of_businesses = FormOfBusiness::where(['deleted'=>0])->select('id', 'title')->get();
        $countries = Countries::where(['deleted'=>0])->select('id', 'country')->get();

        return view('backend.clients')->with(['clients'=>$clients, 'categories'=>$categories, 'form_of_businesses'=>$form_of_businesses, 'countries'=>$countries, 'industries'=>$industries]);
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
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
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
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
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
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
