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
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TimeTracerController extends HomeController
{
    //for user
    public function get_time_tracer() {
        $today = Carbon::today();
        $fields = Fields::where(['deleted'=>0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['task_user.user_id'=>Auth::id(), 't.deleted'=>0])->orderBy('t.task')->select('t.id', 't.task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted'=>0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $today)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $today)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work', 'works.completed')->get();
        $projects = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['task_user.user_id'=>Auth::id(), 't.deleted'=>0, 'p.deleted'=>0])->distinct('t.project_id')->orderBy('p.project')->select('p.id', 'p.project')->get();

        return view('backend.time_tracer')->with(['fields'=>$fields, 'tasks'=>$tasks, 'non_billable_codes'=>$non_billable_codes, 'full_fields'=>$full_fields, 'works'=>$works, 'projects'=>$projects]);
    }

    public function post_time_tracer(Request $request) {
        if ($request->type == 'add_work_with_field') {
            return $this->add_work_with_field($request);
        }
        else if ($request->type == 'add_work_with_time') {
            return $this->add_work_with_time($request);
        }
        else if ($request->type == 'select_start_time') {
            return $this->select_start_time($request);
        }
        else if ($request->type == 'get_works_where') {
            return $this->get_works_where($request);
        }
        else if ($request->type == 'select_project_for_tasks') {
            return $this->select_project_for_tasks($request);
        }
        else if ($request->type == 'complete_works') {
            return $this->complete_works();
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //for chief
    public function get_tracer_for_chief() {
        $today = Carbon::today();
//        $tasks = Tasks::where(['deleted'=>0])->orderBy('task')->select('id', 'task')->get();
//        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted'=>0])->whereDate('works.created_at', $today)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 't.description as task_desc', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
//        $projects = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted'=>0, 'p.deleted'=>0])->distinct('tasks.project_id')->orderBy('p.project')->select('p.id', 'p.project')->get();
//        $users = User::where(['role_id'=>2, 'deleted'=>0])->select('id', 'name', 'surname')->get();
        $projects = Projects::where(['deleted'=>0])->select('id', 'project', 'description')->get();

        $i = 0;
        foreach ($projects as $project) {
            $billable = 0;
            $non_billabel = 0;
            $tasks = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.deleted'=>0, 't.deleted'=>0, 't.project_id'=>$project->id])->select('color')->get();
            foreach ($tasks as $task) {
                if ($task->color == 'red') {
                    $billable++;
                } else {
                    $non_billabel++;
                }
            }
            $projects[$i]['billable'] = $billable;
            $projects[$i]['non_billable'] = $non_billabel;

            $i++;
        }

        return view('backend.tracer_for_chief')->with(['projects'=>$projects]);
    }

    public function post_tracer_for_chief(Request $request) {
        if ($request->type == 'show_tasks') {
            return $this->show_tasks($request);
        }
        else if ($request->type == 'show_works') {
            return $this->show_works($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //for project manager
    public function get_tracer_for_project_manager() {
        $today = Carbon::today();
        $projects = Projects::where(['deleted'=>0, 'project_manager_id'=>Auth::id()])->select('id', 'project', 'description')->get();

        $i = 0;
        foreach ($projects as $project) {
            $billable = 0;
            $non_billabel = 0;
            $tasks = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.deleted'=>0, 't.deleted'=>0, 't.project_id'=>$project->id])->select('color')->get();
            foreach ($tasks as $task) {
                if ($task->color == 'red') {
                    $billable++;
                } else {
                    $non_billabel++;
                }
            }
            $projects[$i]['billable'] = $billable;
            $projects[$i]['non_billable'] = $non_billabel;

            $i++;
        }

        return view('backend.tracer_for_chief')->with(['projects'=>$projects]);
    }

    //for project manager
    public function get_time_tracer_for_project_manager() {
        $today = Carbon::today();
        $fields = Fields::where(['deleted'=>0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')->where(['tasks.deleted'=>0, 'p.deleted'=>0, 'p.project_manager_id'=>Auth::id()])->orderBy('tasks.task')->select('tasks.id', 'tasks.task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted'=>0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $today)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
        $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $today)->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work', 'works.completed')->get();
        $projects = Projects::where(['deleted'=>0, 'project_manager_id'=>Auth::id()])->orderBy('project')->select('id', 'project')->get();

        return view('backend.time_tracer')->with(['fields'=>$fields, 'tasks'=>$tasks, 'non_billable_codes'=>$non_billable_codes, 'full_fields'=>$full_fields, 'works'=>$works, 'projects'=>$projects]);
    }

    //complete works
    private function complete_works() {
        try {
            $today = Carbon::today();
            $now = Carbon::now();

            if (Works::where(['user_id'=>Auth::id(), 'deleted'=>0, 'completed'=>0])->whereDate('created_at', $today)->count() < 48) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You cannot do this without filling the whole day!']);
            }

            $complete = Works::where(['user_id'=>Auth::id(), 'deleted'=>0, 'completed'=>0])->whereDate('created_at', $today)->update(['completed'=>1, 'completed_at'=>$now]);

            if ($complete) {
                $managers = User::where(['role_id'=>1])->select('send_mail', 'email', 'name', 'surname')->get();
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

            return response(['case' => 'success', 'title'=>'Success!', 'content'=>'You completed the day!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show tasks for chief
    private function show_tasks(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            $tasks = TaskUser::leftJoin('users as u', 'task_user.user_id', '=', 'u.id')->leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['t.project_id'=>$request->project_id, 't.deleted'=>0])->select('t.id', 't.task', 'u.name', 'u.surname')->get();

            $i = 0;
            foreach ($tasks  as $task) {
                $billable = 0;
                $non_billable = 0;
                $works = Works::where(['task_id'=>$task->id, 'deleted'=>0])->select('color')->get();

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

            return response(['case' => 'success', 'tasks'=>$tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show works for chief
    private function show_works(Request $request) {
        $validator = Validator::make($request->all(), [
            'task_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Task not found!']);
        }
        try {
            $works = Works::leftJoin('users as u', 'works.user_id', '=', 'u.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->where(['works.deleted'=>0, 'works.task_id'=>$request->task_id])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('works.id', 'works.work', 'works.color', 'works.created_at', 'f.start_time', 'f.end_time', 'u.name', 'u.surname', 'same_work')->get();

            return response(['case' => 'success', 'works'=>$works]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add work with field
    private function add_work_with_field(Request $request) {
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
            $today = Carbon::today();
            if (Works::where(['user_id'=>Auth::id(), 'field_id'=>$request->field_id])->whereDate('created_at', $today)->count() > 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You have already added this field!']);
            }

            $same_work = str_random(25) . time() . str_random(3) . microtime();

            $request->merge(['user_id'=>Auth::id(), 'same_work'=>md5($same_work)]);

            Works::create($request->all());

            Session::flash('message', 'Added!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add work with time
    private function add_work_with_time(Request $request) {
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
            $start = $request->start_time;
            $end_time = strtotime($request->end_time) - 10*60;
            $end = date("H:i", $end_time);

            $field_count = 0;
            $arr['user_id'] = Auth::id();
            $arr['task_id'] = $request->task_id;
            $arr['work'] = $request->work;
            $arr['color'] = $request->color;
            $today = Carbon::today();
            $fields = Fields::where(['deleted'=>0])->whereTime('start_time', '>=', $start)->whereTime('start_time', '<=', $end)->select('id')->get();

            $same_work = str_random(25) . time() . str_random(3) . microtime();
            $arr['same_work'] = md5($same_work);

            foreach ($fields as $field) {
                if (Works::where(['user_id'=>Auth::id(), 'field_id'=>$field->id])->whereDate('created_at', $today)->count() == 0) {
                    $arr['field_id'] = $field->id;
                    Works::create($arr);
                    $field_count++;
                }
                else {
                    $same_work = str_random(25) . time() . str_random(3) . microtime();
                    $arr['same_work'] = md5($same_work);
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
    private function select_start_time(Request $request) {
        $validator = Validator::make($request->all(), [
            'start_time' => ['required', 'date_format:H:i'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Start time not found!']);
        }
        try {
            $end_times = Fields::where(['deleted'=>0, ])->whereTime('end_time', '>', $request->start_time)->orderBy('end_time')->select('end_time')->get();

            return response(['case' => 'success', 'end_times'=>$end_times]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function get_works_where(Request $request) {
        $validator = Validator::make($request->all(), [
            'column' => ['required', 'string'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            if ($request->column == 'date') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $request->value)->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work')->get();
            }
            else if ($request->column == 'task') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0, 'works.task_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work')->get();
            }
            else if ($request->column == 'project') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0, 't.project_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'works.same_work')->get();
            }
            else {
                return response(['case' => 'error', 'title' => 'Error!', 'content' => 'Column not found!']);
            }

            return response(['case' => 'success', 'works'=>$works]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function get_works_where_for_chief(Request $request) {
        $validator = Validator::make($request->all(), [
            'column' => ['required', 'string'],
            'value' => ['required'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            if ($request->column == 'date') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted'=>0])->whereDate('works.created_at', $request->value)->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            }
            else if ($request->column == 'task') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted'=>0, 'works.task_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            }
            else if ($request->column == 'project') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted'=>0, 't.project_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            }
            else if ($request->column == 'user') {
                $works = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->leftJoin('fields as f', 'works.field_id', '=', 'f.id')->leftJoin('projects as p', 't.project_id', '=', 'p.id')->leftJoin('users as u', 't.user_id', '=', 'u.id')->where(['works.deleted'=>0, 'works.user_id' => $request->value])->orderByRaw('DATE(works.created_at)')->orderBy('f.start_time')->select('f.start_time', 'f.end_time', 'p.project', 'p.description as project_desc', 't.task', 'works.color', 'works.work', 'works.created_at', 'u.name', 'u.surname')->get();
            }
            else {
                return response(['case' => 'error', 'title' => 'Error!', 'content' => 'Column not found!']);
            }

            return response(['case' => 'success', 'works'=>$works]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //select project for tasks
    private function select_project_for_tasks(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            if (Auth::user()->role() == 4) {
                //project manager
                $tasks = Tasks::where(['project_id'=>$request->project_id, 'deleted'=>0])->orderBy('task')->select('id', 'task')->get();
            } else {
                //user
                $tasks = TaskUser::leftJoin('tasks as t', 'task_user.task_id', '=', 't.id')->where(['t.project_id'=>$request->project_id, 'task_user.user_id'=>Auth::id(), 't.deleted'=>0])->orderBy('t.task')->select('t.id', 't.task')->get();
            }

            return response(['case' => 'success', 'tasks'=>$tasks]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    private function calculate_time($field) {
        $minute = $field * 10;

        $hour = floor($minute / 60);
        $minute = $minute - ($hour * 60);

        $time = $hour . " hour(s), " . $minute . " minute(s)";

        return $time;
    }
}
