<?php

namespace App\Http\Controllers;

use App\Clients;
use App\User;
use App\Projects;
use App\Tasks;
use App\Works;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ReportController extends HomeController
{
    public function get_report() {
        try {
            $users = User::where('deleted', 0)->where('role_id', '<>', 3)->select('id', 'name', 'surname')->get();
            $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
            $projects = Projects::leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
                ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
                ->where(['projects.deleted' => 0, 'c.deleted'=>0])
                ->orderBy('projects.project')
                ->select('projects.id', 'projects.project', 'c.name as client', 'fob.title as fob')
                ->get();
            $tasks = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')
                ->leftJoin('clients as c', 'p.client_id', '=', 'c.id')
                ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
                ->where(['tasks.deleted' => 0, 'p.deleted' => 0, 'c.deleted'=>0])
                ->orderBy('tasks.task')
                ->select('tasks.id', 'tasks.task', 'p.project', 'c.name as client', 'fob.title as fob')
                ->get();

            return view("backend.report", compact(
                'users',
                'clients',
                'projects',
                'tasks'
            ));
        } catch (\Exception $exception) {
            return view("backend.index");
        }
    }

    public function change_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Client not found!']);
        }
        try {
            $projects = Projects::where(['client_id'=>$request->client_id, 'deleted' => 0])
                ->orderBy('project')
                ->select('id', 'project')
                ->get();
            $tasks = Tasks::leftJoin('projects as p', 'tasks.project_id', '=', 'p.id')
                ->where(['p.client_id'=>$request->client_id, 'tasks.deleted' => 0, 'p.deleted' => 0])
                ->orderBy('tasks.task')
                ->select('tasks.id', 'tasks.task', 'p.project')
                ->get();

            return response(['case' => 'success', 'projects' => $projects, 'tasks' => $tasks]);
        } catch (\Exception $exception) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    public function change_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            $tasks = Tasks::where(['project_id'=>$request->project_id, 'deleted' => 0])
                ->orderBy('task')
                ->select('id', 'task')
                ->get();

            return response(['case' => 'success', 'tasks' => $tasks]);
        } catch (\Exception $exception) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    public function show_report(Request $request) {
        $validator = Validator::make($request->all(), [
            'user_id' => ['nullable', 'integer'],
            'client_id' => ['nullable', 'integer'],
            'project_id' => ['nullable', 'integer'],
            'task_id' => ['nullable', 'integer'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Validation error!']);
        }
        try {
            $query = Works::leftJoin('tasks as t', 'works.task_id', '=', 't.id')
                ->leftJoin('projects as p', 't.project_id', '=', 'p.id')
                ->where(['works.deleted' => 0, 't.deleted' => 0]);

            if (!empty($request->user_id) && $request->user_id != ''  && $request->user_id != null) {
                $where_user_id = $request->user_id;
                $query->where('works.user_id', $where_user_id);
            }

            if (!empty($request->client_id) && $request->client_id != ''  && $request->client_id != null) {
                $where_client_id = $request->client_id;
                $query->where('p.client_id', $where_client_id);
            }

            if (!empty($request->project_id) && $request->project_id != ''  && $request->project_id != null) {
                $where_project_id = $request->project_id;
                $query->where('t.project_id', $where_project_id);
            }

            if (!empty($request->task_id) && $request->task_id != ''  && $request->task_id != null) {
                $where_task_id = $request->task_id;
                $query->where('works.task_id', $where_task_id);
            }

            if (!empty($request->start_date) && $request->start_date != ''  && $request->start_date != null) {
                $where_start_date = $request->start_date;
                $query->where('works.date', '>=', $where_start_date);
            }

            if (!empty($request->end_date) && $request->end_date != ''  && $request->end_date != null) {
                $where_end_date = $request->end_date;
                $where_end_date = new DateTime($where_end_date);
                $where_end_date = $where_end_date->modify('+1 day');
                $query->where('works.date', '<=', $where_end_date);
            }

            $billable = 0;
            $non_billable = 0;
            $works = $query->select('color')->get();

            foreach ($works as $work) {
                if ($work->color == 'red') {
                    $billable++;
                } else {
                    $non_billable++;
                }
            }

            $total = count($works);

            return response(['case' => 'success', 'total'=>$total, 'billable'=>$billable, 'non_billable'=>$non_billable]);
        } catch (\Exception $exception) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
