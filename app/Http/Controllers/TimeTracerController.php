<?php

namespace App\Http\Controllers;

use App\Fields;
use App\NonBillableCodes;
use App\Projects;
use App\Tasks;
use App\TaskUser;
use App\User;
use App\Works;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TimeTracerController extends HomeController
{
    //for user
    public function get_time_tracer()
    {
        if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
            $date = Input::get('date');

            if ($date > Carbon::today()) {
                Session::flash('message', 'You cannot select the date after this day!');
                Session::flash('class', 'warning');
                Session::flash('display', 'block');
                $date = Carbon::today();
            }
        } else {
            $date = Carbon::today();
        }

        $fields = Fields::where(['deleted' => 0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['task_user.user_id' => Auth::id(), 't.deleted' => 0])->orderBy('t.task')->select('t.id', 't.task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted' => 0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work', 'works.completed')->get();
        $projects = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')
            ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
            ->leftJoin('clients as c', 'p.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->where(['task_user.user_id' => Auth::id(), 't.deleted' => 0, 'p.deleted' => 0])
            ->distinct('t.project_id')
            ->orderBy('p.project')
            ->select('p.id', 'p.project', 'c.name as client', 'fob.title as fob')
            ->get();

        return view('backend.time_tracer')->with(['fields' => $fields, 'tasks' => $tasks, 'non_billable_codes' => $non_billable_codes, 'full_fields' => $full_fields, 'works' => $works, 'projects' => $projects, 'date'=>$date]);
    }

    //for project manager
    public function get_time_tracer_for_project_manager()
    {
        if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
            $date = Input::get('date');

            if ($date > Carbon::today()) {
                Session::flash('message', 'You cannot select the date after this day!');
                Session::flash('class', 'warning');
                Session::flash('display', 'block');
                $date = Carbon::today();
            }
        } else {
            $date = Carbon::today();
        }

        $fields = Fields::where(['deleted' => 0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted' => 0, 'p.deleted' => 0, 'p.project_manager_id' => Auth::id()])->orderBy('tasks.task')->select('tasks.id', 'tasks.task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted' => 0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work', 'works.completed')->get();
        $projects = Projects::leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->where(['projects.deleted' => 0, 'projects.project_manager_id' => Auth::id()])
            ->orderBy('projects.project')
            ->select('projects.id', 'projects.project', 'c.name as client', 'fob.title as fob')
            ->get();

        return view('backend.time_tracer')->with(['fields' => $fields, 'tasks' => $tasks, 'non_billable_codes' => $non_billable_codes, 'full_fields' => $full_fields, 'works' => $works, 'projects' => $projects, 'date'=>$date]);
    }

    //for manager
    public function get_time_tracer_for_manager()
    {
        if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
            $date = Input::get('date');

            if ($date > Carbon::today()) {
                Session::flash('message', 'You cannot select the date after this day!');
                Session::flash('class', 'warning');
                Session::flash('display', 'block');
                $date = Carbon::today();
            }
        } else {
            $date = Carbon::today();
        }

        $fields = Fields::where(['deleted' => 0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted' => 0, 'p.deleted' => 0])->orderBy('tasks.task')->select('tasks.id', 'tasks.task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted' => 0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0])->whereDate('works.date', $date)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work', 'works.completed')->get();
        $projects = Projects::leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->where(['projects.deleted' => 0])
            ->orderBy('projects.project')
            ->select('projects.id', 'projects.project', 'c.name as client', 'fob.title as fob')
            ->get();

        return view('backend.time_tracer')->with(['fields' => $fields, 'tasks' => $tasks, 'non_billable_codes' => $non_billable_codes, 'full_fields' => $full_fields, 'works' => $works, 'projects' => $projects, 'date'=>$date]);
    }

    //post time tracer
    public function post_time_tracer(Request $request)
    {
        if ($request->type == 'add_work_with_field') {
            return $this->add_work_with_field($request);
        } else if ($request->type == 'add_work_with_time') {
            return $this->add_work_with_time($request);
        } else if ($request->type == 'select_start_time') {
            return $this->select_start_time($request);
        } else if ($request->type == 'get_works_where') {
            return $this->get_works_where($request);
        } else if ($request->type == 'select_project_for_tasks') {
            return $this->select_project_for_tasks($request);
        } else if ($request->type == 'complete_works') {
            return $this->complete_works();
        } else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //for chief
    public function get_tracer_for_chief()
    {
        $projects = Projects::leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->where(['projects.deleted' => 0])
            ->select('projects.id', 'projects.project', 'projects.description', 'c.name as client', 'fob.title as fob')
            ->get();

        $i = 0;
        $billable_sum = 0;
        $non_billable_sum = 0;
        foreach ($projects as $project) {
            $billable = 0;
            $non_billable = 0;
            $tasks = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.deleted' => 0, 't.deleted' => 0, 't.project_id' => $project->id])->select('color')->get();
            foreach ($tasks as $task) {
                if ($task->color == 'red') {
                    $billable++;
                } else {
                    $non_billable++;
                }
            }
            $projects[$i]['billable'] = $billable;
            $billable_sum += $billable;
            $projects[$i]['non_billable'] = $non_billable;
            $non_billable_sum += $non_billable;

            $i++;
        }

        return view('backend.tracer_for_chief', compact('projects', 'billable_sum', 'non_billable_sum'));
    }

    //for project manager
    public function get_tracer_for_project_manager()
    {
        $projects = Projects::where(['deleted' => 0, 'project_manager_id' => Auth::id()])->select('id', 'project', 'description')->get();

        $i = 0;
        $billable_sum = 0;
        $non_billable_sum = 0;
        foreach ($projects as $project) {
            $billable = 0;
            $non_billable = 0;
            $tasks = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.deleted' => 0, 't.deleted' => 0, 't.project_id' => $project->id])->select('color')->get();
            foreach ($tasks as $task) {
                if ($task->color == 'red') {
                    $billable++;
                } else {
                    $non_billable++;
                }
            }
            $projects[$i]['billable'] = $billable;
            $projects[$i]['non_billable'] = $non_billable;
            $billable_sum += $billable;
            $non_billable_sum += $non_billable;

            $i++;
        }

        return view('backend.tracer_for_chief', compact('projects', 'billable_sum', 'non_billable_sum'));
    }

    public function post_tracer_for_chief(Request $request)
    {
        if ($request->type == 'show_tasks') {
            return $this->show_tasks($request);
        } else if ($request->type == 'show_works') {
            return $this->show_works($request);
        } else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //complete works
    private function complete_works()
    {
        try {
            $now = Carbon::now();

            if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
                $date = Input::get('date');

                if ($date > Carbon::today()) {
                    return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'You cannot select the date after this day!']);
                }
            } else {
                $date = Carbon::today();
            }

            if (Works::where(['user_id' => Auth::id(), 'deleted' => 0, 'completed' => 0])->whereDate('date', $date)->count() < 48) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You cannot do this without filling the whole day!']);
            }

            $complete = Works::where(['user_id' => Auth::id(), 'deleted' => 0, 'completed' => 0])->whereDate('date', $date)->update(['completed' => 1, 'completed_at' => $now]);

            if ($complete) {
                $managers = User::where(['role_id' => 1])->select('send_mail', 'email', 'name', 'surname')->get();
                $email = array();
                $to = array();
                $send_mail = false;
                foreach ($managers as $manager) {
                    if ($manager->send_mail == 1) {
                        $send_mail = true;
                        array_push($email, $manager->email);
                        array_push($to, $manager->name . ' ' . $manager->surname);
                    }
                }

                if ($send_mail) {
                    $message = Auth::user()->name . " " . Auth::user()->surname . " completed the day.";
                    $title = 'Completed day';

                    app('App\Http\Controllers\MailController')->get_send($email, $to, $title, $message);
                }

            }

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'You completed the day!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show tasks for chief
    private function show_tasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            $tasks = TaskUser::leftJoin('users as u', 'task_user.user_id', '=', 'u.id')->leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['t.project_id' => $request->project_id, 't.deleted' => 0])->select('t.id', 't.task', 'u.name', 'u.surname')->get();

            $i = 0;
            foreach ($tasks as $task) {
                $billable = 0;
                $non_billable = 0;
                $works = Works::where(['task_id' => $task->id, 'deleted' => 0])->select('color')->get();

                foreach ($works as $work) {
                    if ($work->color == 'red') {
                        $billable++;
                    } else {
                        $non_billable++;
                    }
                }

                $tasks[$i]['billable'] = $billable;
                $tasks[$i]['non_billable'] = $non_billable;

                $i++;
            }

            return response(['case' => 'success', 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show works for chief
    private function show_works(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'task_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Task not found!']);
        }
        try {
            $works = Works::leftJoin('users as u', 'works.user_id', '=', 'u.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->where(['works.deleted' => 0, 'works.task_id' => $request->task_id])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('works.id', 'works.work', 'works.color', 'works.created_at', 'f.start_time', 'f.end_time', 'u.name', 'u.surname', 'same_work')->get();

            return response(['case' => 'success', 'works' => $works]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add work with field
    private function add_work_with_field(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'field_id' => ['required', 'integer'],
            'task_id' => ['required', 'integer'],
            'work' => ['required', 'string', 'max:4000'],
            'color' => ['required', 'string', 'max:30'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
                $date = Input::get('date');

                if ($date > Carbon::today()) {
                    return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'You cannot select the date after this day!']);
                }
            } else {
                $date = Carbon::today();
            }

            if (Works::where(['user_id' => Auth::id(), 'field_id' => $request->field_id])->whereDate('date', $date)->count() > 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You have already added this field!']);
            }

            $task = Tasks::where('id', $request->task_id)->select('time', 'act_time', 'task')->first();
            $new_act_time = $task->act_time + 10;
            if ($task->time * 60 < $new_act_time) {
                return response(['case' => 'warning', 'title' => 'Stop!', 'content' => 'Time limit exceeded!']);
            }

            $same_work = str_random(25) . time() . str_random(3) . microtime();

            $request->merge(['user_id' => Auth::id(), 'same_work' => md5($same_work), 'date'=>$date]);

            $work = Works::create($request->all());

            if ($work) {
                Tasks::where('id', $request->task_id)->update(['act_time'=>$new_act_time]);

                if ($task->time * 60 == $new_act_time) {
                    $users_in_task = TaskUser::leftJoin('users as u', 'task_user.user_id', '=', 'u.id')
                        ->leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')
                        ->where('task_user.task_id', $request->task_id)
                        ->select('u.send_mail', 'u.email', 'u.name', 'u.surname')
                        ->get();

                    $email_arr = array();
                    $to_arr = array();
                    foreach ($users_in_task as $user_in_task) {
                        if ($user_in_task->send_mail == 1) {
                            array_push($email_arr, $user_in_task['email']);
                            array_push($to_arr, $user_in_task['name'] . ' ' . $user_in_task['surname']);
                        }
                    }

                    $managers = User::where(['role_id' => 1])->select('send_mail', 'email', 'name', 'surname')->get();
                    foreach ($managers as $manager) {
                        if ($manager->send_mail == 1) {
                            array_push($email_arr, $manager->email);
                            array_push($to_arr, $manager->name . ' ' . $manager->surname);
                        }
                    }

                    if (count($email_arr) > 0) {
                        //send email
                        $message = "The time for this task is over:";
                        $message .= "<br>";
                        $message .= "<b>" . $task->task . "</b>";
                        $message .= "<br>";
                        $message .= "SCT: " . $task->time . " hour(s)";
                        $message .= "<br>";
                        $message .= "ACT: " . $new_act_time / 60 . " hour(s)";
                        $title = 'Time is over';

                        app('App\Http\Controllers\MailController')->get_send($email_arr, $to_arr, $title, $message);
                    }
                }
            }

            Session::flash('message', 'Added!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add work with time
    private function add_work_with_time(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'task_id' => ['required', 'integer'],
            'work' => ['required', 'string', 'max:4000'],
            'color' => ['required', 'string', 'max:30'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            if (!empty(Input::get('date')) && Input::get('date') != ''  && Input::get('date') != null) {
                $date = Input::get('date');

                if ($date > Carbon::today()) {
                    return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'You cannot select the date after this day!']);
                }
            } else {
                $date = Carbon::today();
            }

            $start = $request->start_time;
            $end_time = strtotime($request->end_time) - 10 * 60;
            $end = date("H:i", $end_time);

            $field_count = 0;
            $arr['user_id'] = Auth::id();
            $arr['task_id'] = $request->task_id;
            $arr['work'] = $request->work;
            $arr['color'] = $request->color;
            $fields = Fields::where(['deleted' => 0])->whereTime('start_time', '>=', $start)->whereTime('start_time', '<=', $end)->select('id')->get();

            $same_work = str_random(25) . time() . str_random(3) . microtime();
            $arr['same_work'] = md5($same_work);

            $task = Tasks::where('id', $request->task_id)->select('time', 'act_time', 'task')->first();
            $new_act_time = $task->act_time;

            foreach ($fields as $field) {
                if (Works::where(['user_id' => Auth::id(), 'field_id' => $field->id])->whereDate('date', $date)->count() == 0) {
                    $arr['field_id'] = $field->id;
                    $arr['date'] = $date;
                    if ($task->time * 60 < $new_act_time + 10) {
                        break;
                    }
                    Works::create($arr);
                    $new_act_time += 10;
                    $field_count++;
                } else {
                    $same_work = str_random(25) . time() . str_random(3) . microtime();
                    $arr['same_work'] = md5($same_work);
                }
            }

            if ($field_count > 0) {
                Tasks::where('id', $request->task_id)->update(['act_time'=>$new_act_time]);

                if ($task->time * 60 == $new_act_time) {
                    $users_in_task = TaskUser::leftJoin('users as u', 'task_user.user_id', '=', 'u.id')
                        ->leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')
                        ->where('task_user.task_id', $request->task_id)
                        ->select('u.send_mail', 'u.email', 'u.name', 'u.surname')
                        ->get();

                    $email_arr = array();
                    $to_arr = array();
                    foreach ($users_in_task as $user_in_task) {
                        if ($user_in_task->send_mail == 1) {
                            array_push($email_arr, $user_in_task['email']);
                            array_push($to_arr, $user_in_task['name'] . ' ' . $user_in_task['surname']);
                        }
                    }

                    $managers = User::where(['role_id' => 1])->select('send_mail', 'email', 'name', 'surname')->get();
                    foreach ($managers as $manager) {
                        if ($manager->send_mail == 1) {
                            array_push($email_arr, $manager->email);
                            array_push($to_arr, $manager->name . ' ' . $manager->surname);
                        }
                    }

                    if (count($email_arr) > 0) {
                        //send email
                        $message = "The time for this task is over:";
                        $message .= "<br>";
                        $message .= "<b>" . $task->task . "</b>";
                        $message .= "<br>";
                        $message .= "SCT: " . $task->time . " hour(s)";
                        $message .= "<br>";
                        $message .= "ACT: " . $new_act_time / 60 . " hour(s)";
                        $title = 'Time is over';

                        app('App\Http\Controllers\MailController')->get_send($email_arr, $to_arr, $title, $message);
                    }
                }
            }

            Session::flash('message', 'Added to ' . $field_count . ' field(s)!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //select start time
    private function select_start_time(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_time' => ['required', 'date_format:H:i'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Start time not found!']);
        }
        try {
            $end_times = Fields::where(['deleted' => 0,])->whereTime('end_time', '>', $request->start_time)->orderBy('end_time')->select('end_time')->get();

            return response(['case' => 'success', 'end_times' => $end_times]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function get_works_where(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'column' => ['required', 'string'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            $tasks = array();

            if ($request->column == 'task') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0, 'works.task_id' => $request->value])->orderByRaw('DATE(works.date)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.date', 'works.same_work')->get();
            } else if ($request->column == 'project') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id' => Auth::id(), 'works.deleted' => 0, 't.project_id' => $request->value])->orderByRaw('DATE(works.date)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.date', 'works.same_work')->get();
                $tasks = Tasks::where(['deleted' => 0, 'project_id' => $request->value])->orderBy('task')->select('id', 'task')->get();
            } else {
                return response(['case' => 'error', 'title' => 'Error!', 'content' => 'Column not found!']);
            }

            return response(['case' => 'success', 'works' => $works, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function get_works_where_for_chief(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'column' => ['required', 'string'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            $tasks = array();

            if ($request->column == 'date') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted' => 0])->whereDate('works.created_at', $request->value)->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            } else if ($request->column == 'task') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted' => 0, 'works.task_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            } else if ($request->column == 'project') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted' => 0, 't.project_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
                $tasks = Tasks::where(['deleted' => 0, 'project_id' => $request->value])->orderBy('task')->select('id', 'task')->get();
            } else if ($request->column == 'user') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted' => 0, 'works.user_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            } else {
                return response(['case' => 'error', 'title' => 'Error!', 'content' => 'Column not found!']);
            }

            return response(['case' => 'success', 'works' => $works, 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //select project for tasks
    private function select_project_for_tasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            if (Auth::user()->role() == 4 || Auth::user()->role() == 1) {
                //project manager || manager
                $tasks = Tasks::where(['project_id' => $request->project_id, 'deleted' => 0])->orderBy('task')->select('id', 'task', 'time', 'act_time')->get();
            } else {
                //user
                $tasks = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['t.project_id' => $request->project_id, 'task_user.user_id' => Auth::id(), 't.deleted' => 0])->orderBy('t.task')->select('t.id', 't.task', 't.time', 't.act_time')->get();
            }

            return response(['case' => 'success', 'tasks' => $tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function calculate_time($field)
    {
        $minute = $field * 10;

        $hour = floor($minute / 60);
        $minute = $minute - ($hour * 60);

        $time = $hour . " hour(s), " . $minute . " minute(s)";

        return $time;
    }
}
