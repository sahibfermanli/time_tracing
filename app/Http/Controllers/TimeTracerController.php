<?php

namespace App\Http\Controllers;

use App\Fields;
use App\NonBillableCodes;
use App\Tasks;
use App\Works;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TimeTracerController extends HomeController
{
    public function get_time_tracer() {
        $today = Carbon::today();
        $fields = Fields::where(['deleted'=>0])->select('id', 'start_time', 'end_time')->orderBy('start_time')->limit(48)->get();
        $tasks = Tasks::where(['user_id'=>Auth::id(), 'deleted'=>0])->select('id', 'task')->get();
        $non_billable_codes = NonBillableCodes::where(['deleted'=>0])->select('id', 'title')->get();
        $full_fields = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')->where(['works.user_id'=>Auth::id(), 'works.deleted'=>0])->whereDate('works.created_at', $today)->select('works.field_id', 'works.work', 'works.color', 't.task')->get();
//        $start_times = Fields::where(['']);

        return view('backend.time_tracer')->with(['fields'=>$fields, 'tasks'=>$tasks, 'non_billable_codes'=>$non_billable_codes, 'full_fields'=>$full_fields]);
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
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
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

            $request->merge(['user_id'=>Auth::id()]);

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
            foreach ($fields as $field) {
                if (Works::where(['user_id'=>Auth::id(), 'field_id'=>$field->id])->whereDate('created_at', $today)->count() == 0) {
                    $arr['field_id'] = $field->id;
                    Works::create($arr);
                    $field_count++;
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
}
