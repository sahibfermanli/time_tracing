<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Clients;
use App\Projects;
use App\Tasks;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProjectController extends HomeController
{
    //for manager
    public function get_projects() {
        $projects = Projects::leftJoin('users as created', 'projects.created_by', '=', 'created.id')->leftJoin('clients as c', 'projects.client_id', '=', 'c.id')->leftJoin('users as pm', 'projects.project_manager_id', '=', 'pm.id')->where(['projects.deleted'=>0])->select('projects.id', 'projects.project', 'projects.description', 'projects.created_at', 'projects.client_id', 'c.name as client_name', 'c.director as client_director', 'created.name as created_name', 'created.surname as created_surname', 'projects.project_manager_id', 'pm.name as pm_name', 'pm.surname as pm_surname')->paginate(30);
        $up_categories = Categories::where(['deleted'=>0, 'up_category'=>0])->select('id', 'category')->get();
        $project_managers = User::where(['deleted'=>0, 'role_id'=>4])->select('id', 'name', 'surname')->get();

        return view('backend.projects')->with(['projects'=>$projects, 'up_categories'=>$up_categories, 'project_managers'=>$project_managers]);
    }

    //for project manager
    public function get_projects_for_project_manager() {
        $projects = Projects::leftJoin('users as created', 'projects.created_by', '=', 'created.id')->leftJoin('clients as c', 'projects.client_id', '=', 'c.id')->where(['projects.deleted'=>0, 'project_manager_id'=>Auth::id()])->select('projects.id', 'projects.project', 'projects.description', 'projects.created_at', 'c.name as client_name', 'c.director as client_director', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);

        return view('backend.projects_for_project_manager')->with(['projects'=>$projects]);
    }

    //for manager
    public function post_projects(Request $request) {
        if ($request->type == 'add') {
            return $this->add_project($request);
        }
        else if ($request->type == 'update') {
            return $this->update_project($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_project($request);
        }
        else if ($request->type == 'show_categories') {
            return $this->show_categories($request);
        }
        else if ($request->type == 'show_clients') {
            return $this->show_clients($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add project
    private function add_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'project' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'integer'],
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            $add = Projects::create($request->all());

            if ($add) {
                $project_manager = User::where(['id'=>$request->project_manager_id])->select('send_mail', 'email', 'name', 'surname')->first();

                if ($project_manager->send_mail == 1) {
                    //send email
                    $email = $project_manager['email'];
                    $to = $project_manager['name'] . ' ' . $project_manager['surname'];
                    $message = "You have a new project:";
                    $message .= "<br>";
                    $message .= "<b>" . $request->project . "</b>";
                    $title = 'New project';

                    app('App\Http\Controllers\MailController')->get_send($email, $to, $title, $message);
                }
            }

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update project
    private function update_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'project' => ['required', 'string', 'max:255'],
            'client_id' => ['required', 'integer'],
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            Projects::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete project
    private function delete_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            $delete = Projects::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);
            if ($delete) {
                Tasks::where(['project_id'=>$request->id, 'deleted'=>0])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);
            }

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
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
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Up category not found!']);
        }
        try {
            $categories = Categories::where(['up_category'=>$request->up_id, 'deleted'=>0])->select('id', 'category')->get();

            return response(['case' => 'success', 'categories'=>$categories]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show clients
    private function show_clients(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Category not found!']);
        }
        try {
            $clients = Clients::where(['category_id'=>$request->category_id, 'deleted'=>0])->select('id', 'name')->get();

            return response(['case' => 'success', 'clients'=>$clients]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
