<?php

namespace App\Http\Controllers;

use App\FormOfBusiness;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class FormOfBusinessController extends HomeController
{
    public function get_form_of_businesses() {
        $form_of_businesses = FormOfBusiness::leftJoin('users as u', 'form_of_business.created_by', '=', 'u.id')->where(['form_of_business.deleted'=>0])->select('form_of_business.id', 'form_of_business.title', 'form_of_business.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);

        return view('backend.form_of_business')->with(['form_of_businesses'=>$form_of_businesses]);
    }

    public function post_form_of_businesses(Request $request) {
        if ($request->type == 'add') {
            return $this->add_form_of_business($request);
        }
        else if ($request->type == 'update') {
            return $this->update_form_of_business($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_form_of_business($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add form_of_business
    private function add_form_of_business(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            FormOfBusiness::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update form_of_business
    private function update_form_of_business(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'title' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            FormOfBusiness::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete form_of_business
    private function delete_form_of_business(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            FormOfBusiness::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
