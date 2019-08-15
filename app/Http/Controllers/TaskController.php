<?php

namespace App\Http\Controllers;

use App\Clients;
use App\Currencies;
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
        $tasks = Tasks::leftJoin('users as created', 'tasks.created_by', '=', 'created.id')
            ->leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')
            ->leftJoin('clients as c', 'p.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->leftJoin('currencies as cur', 'tasks.currency_id', '=', 'cur.id')
            ->where(['tasks.deleted'=>0, 'p.deleted'=>0])
            ->orderBy('tasks.id', 'DESC')
            ->select('tasks.id', 'tasks.task', 'tasks.created_at', 'tasks.deadline', 'tasks.project_id', 'p.project', 'created.name as created_name', 'created.surname as created_surname', 'c.name as client', 'fob.title as fob', 'p.client_id', 'tasks.currency_id', 'cur.currency', 'tasks.time', 'tasks.payment', 'tasks.total_payment', 'tasks.payment_type')
            ->paginate(30);
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
        $currencies = Currencies::where(['deleted'=>0])->select('id', 'currency')->get();

        return view('backend.tasks')->with(['tasks'=>$tasks, 'clients'=>$clients, 'currencies'=>$currencies]);
    }

    //for project manager
    public function get_tasks_for_project_manager() {
        $tasks = Tasks::leftJoin('users as created', 'tasks.created_by', '=', 'created.id')
            ->leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')
            ->leftJoin('clients as c', 'p.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->leftJoin('currencies as cur', 'tasks.currency_id', '=', 'cur.id')
            ->where(['tasks.deleted'=>0, 'p.deleted'=>0, 'p.project_manager_id'=>Auth::id()])
            ->select('tasks.id', 'tasks.task', 'tasks.created_at', 'tasks.deadline', 'tasks.project_id', 'p.project', 'created.name as created_name', 'created.surname as created_surname', 'c.name as client', 'fob.title as fob', 'p.client_id', 'tasks.currency_id', 'cur.currency', 'tasks.time', 'tasks.payment', 'tasks.total_payment', 'tasks.payment_type')
            ->paginate(30);
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
        $currencies = Currencies::where(['deleted'=>0])->select('id', 'currency')->get();

        return view('backend.tasks')->with(['tasks'=>$tasks, 'clients'=>$clients, 'currencies'=>$currencies]);
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
        else if ($request->type == 'select_staff') {
            return $this->select_staff($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //select staff
    private function select_staff(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Staff not found!']);
        }
        try {
            $staff = User::leftJoin('user_levels as l', 'users.level_id', '=', 'l.id')->where(['users.id'=>$request->id])->select('percentage', 'hourly_rate', 'currency_id')->first();

            return response(['case' => 'success', 'staff'=>$staff]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
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
            $task_users = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')
                ->where(['task_user.deleted'=>0, 't.project_id'=>$request->project_id, 't.deleted'=>0])
                ->select('task_user.user_id')
                ->get();
            $users = Team::leftJoin('users as u', 'team.user_id', '=', 'u.id')
                ->where(['team.project_id'=>$request->project_id, 'team.deleted'=>0, 'u.deleted'=>0])
                ->whereNotIn('team.user_id', $task_users)
                ->select('u.id', 'u.name', 'u.surname')
                ->get();

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
            'time' => ['required', 'integer'],
            'payment_type' => ['required', 'integer'],
            'project_id' => ['required', 'integer'],
            'deadline' => ['required', 'date'],
            'task' => ['required', 'string', 'max:255'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);
            $staff = $request->staff;
            unset($request['staff']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            if (count($staff) == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You must select at least one user!']);
            }

            $request->merge(['created_by'=>Auth::id()]);

            $project_old_data = Projects::where('id', $request->project_id)->select('time', 'total_payment', 'currency_id')->first();

            $cur_id = 0;
            $cur_control = true;
            if (!empty($request->currency_id)) {
                $cur_id = $request->currency_id;
            }

            $project_cur = 0;
            if ($project_old_data->currency_id != null && !empty($project_old_data->currency_id)) {
                $project_cur = $project_old_data->currency_id;

                if ($cur_id != 0) {
                    if ($cur_id != $project_cur) {
                        $cur_control = false;
                    }
                }
            }

            $fix_pay = 0;
            $total_pay = 0;
            if (!empty($request->payment) && $request->payment != 0 && $request->payment != '') {
                $fix_pay = $request->payment;
            }
            $time = $request->time;

            $total_percentage = 0;
            if ($request->payment_type != 1 && $request->payment_type != 4) {
                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        if (!empty($staff[$i]['percentage'])) {
                            $total_percentage += $staff[$i]['percentage'];
                        }
                    }

                    if ($cur_id != 0) {
                        if (!empty($staff[$i]['currency_id'])) {
                            if ($cur_id != $staff[$i]['currency_id']) {
                                $cur_control = false;
                                break;
                            }
                        }
                    }

                    if ($project_cur != 0) {
                        if (!empty($staff[$i]['currency_id'])) {
                            if ($project_cur != $staff[$i]['currency_id']) {
                                $cur_control = false;
                                break;
                            }
                        }
                    }
                }

                if (!$cur_control) {
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => "Currencies is not same!"]);
                }

                if ($total_percentage != 100) {
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => "Total percentage must be equal to 100!"]);
                }
            }

            $staff_control = false;
            $pay_type = $request->payment_type;
            // payment types:
            // 1: fix
            // 2: fix + hourly rate
            // 3: hourly rate
            // 4: monthly
            switch ($pay_type) {
                case 1:
                    $staff_control = false;
                    break;
                case 2:
                    $staff_control = true;
                    break;
                case 3:
                    $staff_control = true;
                    break;
                case 4:
                    $staff_control = false;
                    break;
                default:
                    return response(['case' => 'warning', 'title' => 'Error!', 'content' => 'Payment type error!']);
            }

            $add = Tasks::create($request->all());

            $user_arr = array();
            if ($add) {
                $total_pay += $fix_pay;
                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        //same staff control
                        if (in_array($staff[$i]['user_id'], $user_arr)) {
                            continue;
                        } else {
                            array_push($user_arr, $staff[$i]['user_id']);
                        }

                        $staff[$i]['task_id'] = $add->id;
                        $staff[$i]['created_by'] = Auth::id();
                        if ($staff_control == true) {
                            if (!empty($staff[$i]['percentage']) && !empty($staff[$i]['hourly_rate']) && !empty($staff[$i]['currency_id'])) {
                                TaskUser::create($staff[$i]);
                                $total_pay += $staff[$i]['hourly_rate'] * (($time * $staff[$i]['percentage']) / 100);
//                                array_push($staff_mail_arr, $staff[$i]['user_id']);
                            }
                        } else {
                            TaskUser::create($staff[$i]);
//                            array_push($staff_mail_arr, $staff[$i]['user_id']);
                        }
                    }
                }

                Tasks::where(['id'=>$add->id])->update(['total_payment'=>$total_pay]);
                $project_new_time = $project_old_data->time + $time;
                $project_new_payment = $project_old_data->total_payment + $total_pay;
                Projects::where('id', $request->project_id)->update(['time'=>$project_new_time, 'total_payment'=>$project_new_payment, 'currency_id'=>$request->currency_id]);

                $users_in_task = User::whereIn('id', $user_arr)->select('send_mail', 'email', 'name', 'surname')->get();

                $email_arr = array();
                $to_arr = array();
                foreach ($users_in_task as $user_in_task) {
                    if ($user_in_task->send_mail == 1) {
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
            return response(['case' => 'error', 'title' => 'Error!', 'content' => $e->getMessage()]);
        }
    }

    //update task
    private function update_task(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'time' => ['required', 'integer'],
            'payment_type' => ['required', 'integer'],
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
            $staff = $request->staff;
            unset($request['staff']);

            if ($request->project_id == 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Please select project!']);
            }

            $project_old_data = Projects::where('id', $request->project_id)->select('time', 'total_payment', 'currency_id')->first();

            $cur_id = 0;
            $cur_control = true;
            if (!empty($request->currency_id)) {
                $cur_id = $request->currency_id;
            }

            $project_cur = 0;
            if ($project_old_data->currency_id != null && !empty($project_old_data->currency_id)) {
                $project_cur = $project_old_data->currency_id;

                if ($cur_id != 0) {
                    if ($cur_id != $project_cur) {
                        $cur_control = false;
                    }
                }
            }

            $fix_pay = 0;
            $total_pay = 0;
            if (!empty($request->payment) && $request->payment != 0 && $request->payment != '') {
                $fix_pay = $request->payment;
            }
            $time = $request->time;

            $total_percentage = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')
                ->where(['task_user.deleted'=>0, 't.deleted'=>0, 't.project_id'=>$request->project_id])
                ->sum('task_user.percentage');
            if ($request->payment_type != 1 && $request->payment_type != 4) {
                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        if (!empty($staff[$i]['percentage'])) {
                            $total_percentage += $staff[$i]['percentage'];
                        }
                    }

                    if ($cur_id != 0) {
                        if (!empty($staff[$i]['currency_id'])) {
                            if ($cur_id != $staff[$i]['currency_id']) {
                                $cur_control = false;
                                break;
                            }
                        }
                    }

                    if ($project_cur != 0) {
                        if (!empty($staff[$i]['currency_id'])) {
                            if ($project_cur != $staff[$i]['currency_id']) {
                                $cur_control = false;
                                break;
                            }
                        }
                    }
                }

                if (!$cur_control) {
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => "Currencies is not same!"]);
                }

                if ($total_percentage != 100) {
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => "Total percentage must be equal to 100!"]);
                }
            }

            $staff_control = false;
            $pay_type = $request->payment_type;
            // payment types:
            // 1: fix
            // 2: fix + hourly rate
            // 3: hourly rate
            // 4: monthly
            switch ($pay_type) {
                case 1:
                    $staff_control = false;
                    break;
                case 2:
                    $staff_control = true;
                    break;
                case 3:
                    $staff_control = true;
                    break;
                case 4:
                    $staff_control = false;
                    break;
                default:
                    return response(['case' => 'warning', 'title' => 'Error!', 'content' => 'Payment type error!']);
            }

            //

            $update = Tasks::where(['id'=>$request->id])->update($request->all());

            $user_arr = array();
            if ($update) {
                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        //same staff control
                        if (in_array($staff[$i]['user_id'], $user_arr)) {
                            continue;
                        } else {
                            if (TaskUser::where(['task_id'=>$request->id, 'user_id'=>$staff[$i]['user_id'], 'deleted'=>0])->count() > 0) {
                                continue;
                            }
                            array_push($user_arr, $staff[$i]['user_id']);
                        }

                        $staff[$i]['task_id'] = $request->id;
                        $staff[$i]['created_by'] = Auth::id();
                        if ($staff_control == true) {
                            if (!empty($staff[$i]['percentage']) && !empty($staff[$i]['hourly_rate']) && !empty($staff[$i]['currency_id'])) {
                                TaskUser::create($staff[$i]);
                                $total_pay += $staff[$i]['hourly_rate'] * (($time * $staff[$i]['percentage']) / 100);
                            }
                        } else {
                            TaskUser::create($staff[$i]);
                        }
                    }
                }

                Tasks::where(['id'=>$request->id])->update(['total_payment'=>$total_pay]);
                $project_new_time = $project_old_data->time + $time;
                $project_new_payment = $project_old_data->total_payment + $total_pay;
                Projects::where('id', $request->project_id)->update(['time'=>$project_new_time, 'total_payment'=>$project_new_payment, 'currency_id'=>$request->currency_id]);

                $users_in_task = User::whereIn('id', $user_arr)->select('send_mail', 'email', 'name', 'surname')->get();

                $email_arr = array();
                $to_arr = array();
                foreach ($users_in_task as $user_in_task) {
                    if ($user_in_task->send_mail == 1) {
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

            $delete = Tasks::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            if ($delete) {
                $old_task = Tasks::where(['id'=>$request->id])->select('time', 'total_payment', 'project_id')->first();
                $project_old_data = Projects::where('id', $old_task->project_id)->select('time', 'total_payment', 'currency_id')->first();
                $cur_id = $project_old_data->currency_id;
                if (Tasks::where(['project_id'=>$old_task->project_id, 'deleted'=>0])->count() == 0) {
                    $cur_id = null;
                }
                if (!empty($project_old_data->time) && $project_old_data->time != null) {
                    $project_new_time = $project_old_data->time - $old_task->time;
                } else {
                    $project_new_time = 0;
                }
                if (!empty($project_old_data->total_payment) && $project_old_data->total_payment != null) {
                    $project_new_payment = $project_old_data->total_payment - $old_task->total_payment;
                } else {
                    $project_new_payment = 0;
                }
                Projects::where('id', $old_task->project_id)->update(['time'=>$project_new_time, 'total_payment'=>$project_new_payment, 'currency_id'=>$cur_id]);
            }

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
