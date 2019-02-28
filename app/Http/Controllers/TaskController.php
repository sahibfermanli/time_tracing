<?php

namespace App\Http\Controllers;

use App\Projects;
use App\Tasks;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TaskController extends HomeController
{
    public function get_tasks() {
        $tasks = Tasks::leftJoin('users as created', 'tasks.created_by', '=', 'created.id')->leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->leftJoin('users as u', 'tasks.user_id', '=', 'u.id')->where(['tasks.deleted'=>0])->select('tasks.id', 'tasks.task', 'tasks.description', 'tasks.created_at', 'tasks.project_id', 'p.project', 'tasks.user_id', 'tasks.user_date', 'u.name as user_name', 'u.surname as user_surname', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);
        $projects = Projects::where(['deleted'=>0])->select('id', 'project')->get();
        $users = User::where(['deleted'=>0])->select('id', 'name', 'surname')->get();

        return view('backend.tasks')->with(['tasks'=>$tasks, 'projects'=>$projects, 'users'=>$users]);
    }

    public function post_tasks(Request $request) {
        if ($request->type == 'add') {
            return $this->add_task($request);
        }
        if ($request->type == 'update') {
            return $this->update_task($request);
        }
        if ($request->type == 'delete') {
            return $this->delete_task($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add task
    private function add_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
            'task' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            if (!empty($request->user_id) && isset($request->user_id) && $request->user_id != 0) {
                $current_date = Carbon::now();
                $request->merge(['user_date'=>$current_date]);
            }

            $request->merge(['created_by'=>Auth::id()]);

            Tasks::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update task
    private function update_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'project_id' => ['required', 'integer'],
            'task' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            if (!empty($request->user_id) && isset($request->user_id) && $request->user_id != 0) {
                $task = Tasks::where(['id'=>$request->id])->select('user_id')->first();
                if ($task->user_id != $request->user_id) {
                    $current_date = Carbon::now();
                    $request->merge(['user_date'=>$current_date]);
                }
            }
            else {
                $request->merge(['user_date'=>null]);
            }

            Tasks::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete task
    private function delete_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            Tasks::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
