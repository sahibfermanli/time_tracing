<?php

namespace App\Http\Controllers;

use App\NonBillableCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class NonBillableCodeController extends HomeController
{
    public function get_non_billable_codes() {
        $non_billable_codes = NonBillableCodes::leftJoin('users as u', 'non_billable_codes.created_by', '=', 'u.id')->where(['non_billable_codes.deleted'=>0])->select('non_billable_codes.id', 'non_billable_codes.title', 'non_billable_codes.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);

        return view('backend.non_billable_codes')->with(['non_billable_codes'=>$non_billable_codes]);
    }

    public function post_non_billable_codes(Request $request) {
        if ($request->type == 'add') {
            return $this->add_non_billable_code($request);
        }
        else if ($request->type == 'update') {
            return $this->update_non_billable_code($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_non_billable_code($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add non_billable_code
    private function add_non_billable_code(Request $request) {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            NonBillableCodes::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update non_billable_code
    private function update_non_billable_code(Request $request) {
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

            NonBillableCodes::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete non_billable_code
    private function delete_non_billable_code(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            NonBillableCodes::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
