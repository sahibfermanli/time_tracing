<?php

namespace App\Http\Controllers;

use App\Clients;
use App\Projects;
use App\Tasks;
use App\TaskUser;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TaskController extends HomeController
{
    //for manager
    public function get_tasks() {
        $tasks = Tasks::leftJoin('users as created', 'tasks.created_by', '=', 'created.id')->leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted'=>0, 'p.deleted'=>0])->orderBy('tasks.id', 'DESC')->select('tasks.id', 'tasks.task', 'tasks.created_at', 'tasks.deadline', 'tasks.project_id', 'p.project', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();

        return view('backend.tasks')->with(['tasks'=>$tasks, 'clients'=>$clients]);
    }

    //for project manager
    public function get_tasks_for_project_manager() {
        $tasks = Tasks::leftJoin('users as created', 'tasks.created_by', '=', 'created.id')->leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted'=>0, 'p.deleted'=>0, 'p.project_manager_id'=>Auth::id()])->select('tasks.id', 'tasks.task', 'tasks.created_at', 'tasks.deadline', 'tasks.project_id', 'p.project', 'created.name as created_name', 'created.surname as created_surname')->paginate(30);
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();

        return view('backend.tasks')->with(['tasks'=>$tasks, 'clients'=>$clients]);
    }

    //for manager
    public function post_tasks(Request $request) {
        if ($request->type == 'add') {
            return $this->add_task($request);
        }
        else if ($request->type == 'update') {
            return $this->update_task($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_task($request);
        }
        else if ($request->type == 'get_users') {
            return $this->get_users($request);
        }
        else if ($request->type == 'get_projects_selected_user') {
            return $this->get_projects_selected_user($request);
        }
        else if ($request->type == 'show_users') {
            return $this->show_users($request);
        }
        else if ($request->type == 'delete_user') {
            return $this->delete_user($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    private function get_users(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Task not found!']);
        }
        try {
            $users = Team::leftJoin('users as u', 'team.user_id', '=', 'u.id')->where(['team.project_id'=>$request->project_id, 'team.deleted'=>0, 'u.deleted'=>0, 'u.role_id'=>2])->select('u.id', 'u.name', 'u.surname')->get();

            return response(['case' => 'success', 'users'=>$users]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function show_users(Request $request) {
        $validator = Validator::make($request->all(), [
            'task_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Task not found!']);
        }
        try {
            $users = TaskUser::leftJoin('users as u', 'task_user.user_id', '=', 'u.id')->where(['task_user.task_id'=>$request->task_id, 'task_user.deleted'=>0])->select('task_user.id', 'u.name', 'u.surname')->get();

            return response(['case' => 'success', 'users'=>$users]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function delete_user(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'User not found!']);
        }
        try {
            $current_date = Carbon::now();

            TaskUser::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function get_projects_selected_user(Request $request) {
        $validator = Validator::make($request->all(), [
            'client_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Client not found!']);
        }
        try {
            $projects = Projects::where(['client_id'=>$request->client_id, 'deleted'=>0])->select('id', 'project')->get();

            return response(['case' => 'success', 'projects'=>$projects]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add task
    private function add_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
            'deadline' => ['required', 'date'],
            'task' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);
            $users = $request->user_id;
            unset($request['user_id']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            if (count($users) == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You must select at least one user!']);
            }

            $request->merge(['created_by'=>Auth::id()]);

            $add = Tasks::create($request->all());

            $user_arr = array();
            if ($add) {
                for ($i=1; $i<=count($users); $i++) {
                    if (!empty($users[$i]) && $users[$i] != 0) {
                        //same user control
                        if (in_array($users[$i], $user_arr)) {
                            continue;
                        } else {
                            array_push($user_arr, $users[$i]);
                        }

                        TaskUser::create(['user_id'=>$users[$i], 'task_id'=>$add->id, 'created_by'=>Auth::id()]);
                    }
                }

                $users_in_task = User::whereIn('id', $user_arr)->select('send_mail', 'email', 'name', 'surname')->get();

                $email_arr = array();
                $to_arr = array();
                foreach ($users_in_task as $user_in_task) {
                    if ($user_in_task->send_mail == 1 && $user_in_task->deleted == 0) {
                        array_push($email_arr, $user_in_task['email']);
                        array_push($to_arr, $user_in_task['name'] . ' ' . $user_in_task['surname']);
                    }
                }

                if (count($email_arr) > 0) {
                    //send email
                    $message = "You have a new task:";
                    $message .= "<br>";
                    $message .= "<b>" . $request->task . "</b>";
                    $message .= "<br>";
                    $message .= "Deadline: " . $request->deadline;
                    $title = 'New task';

                    app('App\Http\Controllers\MailController')->get_send($email_arr, $to_arr, $title, $message);
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

    //update task
    private function update_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'project_id' => ['required', 'integer'],
            'deadline' => ['required', 'date'],
            'task' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);
            $send_mail = false;
            $users = $request->user_id;
            unset($request['user_id']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            $update = Tasks::where(['id'=>$request->id])->update($request->all());

            $user_arr = array();
            if ($update) {
                for ($i=1; $i<=count($users); $i++) {
                    if (!empty($users[$i]) && $users[$i] != 0) {
                        //same user control
                        if (in_array($users[$i], $user_arr)) {
                            continue;
                        } else {
                            if (TaskUser::where(['task_id'=>$request->id, 'user_id'=>$users[$i], 'deleted'=>0])->count() > 0) {
                                continue;
                            }
                            array_push($user_arr, $users[$i]);
                        }

                        TaskUser::create(['user_id'=>$users[$i], 'task_id'=>$request->id, 'created_by'=>Auth::id()]);
                    }
                }

                $users_in_task = User::whereIn('id', $user_arr)->select('send_mail', 'email', 'name', 'surname')->get();

                $email_arr = array();
                $to_arr = array();
                foreach ($users_in_task as $user_in_task) {
                    if ($user_in_task->send_mail == 1 && $user_in_task->deleted == 0) {
                        array_push($email_arr, $user_in_task['email']);
                        array_push($to_arr, $user_in_task['name'] . ' ' . $user_in_task['surname']);
                    }
                }

                if (count($email_arr) > 0) {
                    //send email
                    $message = "You have a new task:";
                    $message .= "<br>";
                    $message .= "<b>" . $request->task . "</b>";
                    $message .= "<br>";
                    $message .= "Deadline: " . $request->deadline;
                    $title = 'New task';

                    app('App\Http\Controllers\MailController')->get_send($email_arr, $to_arr, $title, $message);
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
