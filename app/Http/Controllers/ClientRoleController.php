<?php

namespace App\Http\Controllers;

use App\ClientRoles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ClientRoleController extends HomeController
{
    public function get_client_roles() {
        $roles = ClientRoles::leftJoin('users as u', 'client_roles.created_by', '=', 'u.id')->where(['client_roles.deleted'=>0])->select('client_roles.id', 'client_roles.role', 'client_roles.description', 'client_roles.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);

        return view('backend.client_roles')->with(['roles'=>$roles]);
    }

    public function post_client_roles(Request $request) {
        if ($request->type == 'add') {
            return $this->add_role($request);
        }
        else if ($request->type == 'update') {
            return $this->update_role($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_role($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add role
    private function add_role(Request $request) {
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|max:30',
            'description' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            ClientRoles::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update role
    private function update_role(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'role' => 'required|string|max:30',
            'description' => 'required|string|max:100',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            ClientRoles::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete role
    private function delete_role(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            ClientRoles::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
