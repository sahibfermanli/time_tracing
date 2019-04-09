<?php

namespace App\Http\Controllers;

use App\Roles;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class UserController extends HomeController
{
    public function get_users() {
        $users = User::leftJoin('users as created', 'users.created_by', '=', 'created.id')->leftJoin('roles as r', 'users.role_id', '=', 'r.id')->where(['users.deleted'=>0])->select('users.id', 'users.name', 'users.surname', 'users.email', 'users.username', 'users.role_id', 'r.role', 'r.description', 'users.created_at', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);
        $roles = Roles::where(['deleted'=>0])->select('id', 'role')->get();

        return view('backend.users')->with(['users'=>$users, 'roles'=>$roles]);
    }

    public function get_users_for_chief() {
        $users = User::leftJoin('users as created', 'users.created_by', '=', 'created.id')->leftJoin('roles as r', 'users.role_id', '=', 'r.id')->where(['users.deleted'=>0])->where('users.role_id', '<>', 3)->select('users.id', 'users.name', 'users.surname', 'users.email', 'users.username', 'users.role_id', 'r.role', 'r.description', 'users.created_at', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);
        $roles = Roles::where(['deleted'=>0])->where('id', '<>', 3)->select('id', 'role')->get();

        return view('backend.users')->with(['users'=>$users, 'roles'=>$roles]);
    }

    public function post_users(Request $request) {
        if ($request->type == 'add') {
            return $this->add_user($request);
        }
        else if ($request->type == 'update') {
            return $this->update_user($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_user($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add user
    private function add_user(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:50', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields! Or this user already exists.']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            $old_password = $request->password;

            $new_password = Hash::make($old_password);
            unset($request['password']);
            $request['password'] = $new_password;

            $add = User::create($request->all());

            if ($add) {
                //send email
                $email = $request->email;
                $to = $request->name . ' ' . $request->surname;
                $message = "You have been registered by admin.";
                $message .= "<br>";
                $message .= "Your username: " . $request->username;
                $message .= "<br>";
                $message .= "Your password: " . $old_password;
                $message .= "<br>";
                $message .= "Change your password after logging in.";
                $message .= "<br>";
                $message .= "Link: <a href='https://timetracer.edi.az/'>https://timetracer.edi.az/</a>";
                $title = 'Register';

                app('App\Http\Controllers\MailController')->get_send($email, $to, $title, $message);
            }

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update user
    private function update_user(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'username' => ['required', 'string', 'max:50'],
            'role_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            if (empty($request->password) || !isset($request->password)) {
                unset($request['password']);
            }
            else {
                $new_password = Hash::make($request->password);
                unset($request['password']);
                $request['password'] = $new_password;
            }

            User::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete user
    private function delete_user(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            User::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
