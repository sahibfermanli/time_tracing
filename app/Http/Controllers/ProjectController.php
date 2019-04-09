<?php

namespace App\Http\Controllers;

use App\Categories;
use App\ClientRoles;
use App\Clients;
use App\Countries;
use App\FormOfBusiness;
use App\ProjectList;
use App\Projects;
use App\Tasks;
use App\Team;
use App\ThirdParties;
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
        $projects = Projects::leftJoin('users as created', 'projects.created_by', '=', 'created.id')->leftJoin('clients as c', 'projects.client_id', '=', 'c.id')->leftJoin('client_roles as cr', 'projects.client_role_id', '=', 'cr.id')->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')->leftJoin('users as pm', 'projects.project_manager_id', '=', 'pm.id')->where(['projects.deleted'=>0])->orderBy('projects.id', 'DESC')->select('projects.id', 'projects.project', 'projects.description', 'projects.created_at', 'projects.client_id', 'projects.client_role_id', 'c.name as client_name', 'fob.title as client_fob', 'c.director as client_director', 'cr.role as client_role', 'created.name as created_name', 'created.surname as created_surname', 'projects.project_manager_id', 'pm.name as pm_name', 'pm.surname as pm_surname')->paginate(30);
        $project_managers = User::where(['deleted'=>0])->whereIn('role_id', [4, 1])->orderBy('name')->select('id', 'name', 'surname')->get();
        $users = User::where(['deleted'=>0, 'role_id'=>2])->orderBy('name')->select('id', 'name', 'surname')->get();
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
        $project_list = ProjectList::where(['deleted'=>0])->orderBy('project')->select('project')->get();
        $client_roles = ClientRoles::where(['deleted'=>0])->select('id', 'role')->get();
        //for add new third party modal
        $industries = Categories::where('up_category', '=', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $form_of_businesses = FormOfBusiness::where(['deleted'=>0])->select('id', 'title')->get();
        $countries = Countries::where(['deleted'=>0])->select('id', 'country')->get();

        return view('backend.projects')->with(['projects'=>$projects, 'project_managers'=>$project_managers, 'clients'=>$clients, 'project_list'=>$project_list, 'users'=>$users, 'client_roles'=>$client_roles, 'form_of_businesses'=>$form_of_businesses, 'countries'=>$countries, 'industries'=>$industries]);
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
        else if ($request->type == 'show_team') {
            return $this->show_team($request);
        }
        else if ($request->type == 'show_team_for_update_project') {
            return $this->show_team_for_update_project($request);
        }
        else if ($request->type == 'show_categories') {
            return $this->show_categories($request);
        }
        else if ($request->type == 'add_new_third_party') {
            return $this->add_new_third_party($request);
        }
        else if ($request->type == 'show_third_parties') {
            return $this->show_third_parties($request);
        }
        else if ($request->type == 'delete_third_party') {
            return $this->delete_third_party($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    private function add_new_third_party(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'form_of_business_id' => ['required', 'integer'],
            'country_id' => ['required', 'integer'],
            'city' => ['required', 'string', 'max:100'],
            'director' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer'],
            'email' => ['required', 'string', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'web_site' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:20'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            if (isset($request->form_of_business_text) && !empty($request->form_of_business_text)) {
                unset($request['form_of_business_id']);

                $form_of_business = FormOfBusiness::create(['title'=>$request->form_of_business_text, 'created_by'=>Auth::id()]);
                $request['form_business_id'] = $form_of_business->id;
            } else {
                unset($request['form_of_business_text']);
            }

            $request->merge(['created_by'=>Auth::id()]);

            $client = Clients::create($request->all());

            return response(['case' => 'success', 'new_id'=>$client->id, 'new_company'=>$request->name]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show team
    private function show_team(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $team = Team::leftJoin('users as u', 'team.user_id', '=', 'u.id')->where(['team.project_id'=>$request->project_id, 'team.deleted'=>0, 'u.deleted'=>0])->select('team.project_id', 'team.user_id', 'u.name', 'u.surname')->get();

            return response(['case' => 'success', 'team'=>$team]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show third parties
    private function show_third_parties(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $third_parties = ThirdParties::leftJoin('clients as c', 'third_parties.client_id', '=', 'c.id')->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')->leftJoin('client_roles as cr', 'third_parties.role_id', '=', 'cr.id')->where(['third_parties.project_id'=>$request->project_id, 'third_parties.deleted'=>0])->select('third_parties.id', 'c.name', 'fob.title as fob', 'cr.role')->get();

            return response(['case' => 'success', 'third_parties'=>$third_parties]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete third party
    private function delete_third_party(Request $request) {
        $validator = Validator::make($request->all(), [
            'tp_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            ThirdParties::where(['id'=>$request->tp_id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->tp_id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //show team for update project
    private function show_team_for_update_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $team = Team::where(['project_id'=>$request->project_id, 'deleted'=>0])->select('user_id')->get();

            return response(['case' => 'success', 'team'=>$team]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //add project
    private function add_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'client_id' => ['required', 'integer'],
            'client_role_id' => ['required', 'integer'],
//            'third_party_id' => ['required', 'integer'],
//            'third_party_role_id' => ['required', 'integer'],
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $staff = $request->staff;
            $third_parties = $request->third_party_id;
            $roles = $request->third_party_role_id;
            unset($request['staff'], $request['third_party_id'], $request['third_party_role_id']);

            $project = '';
            if (isset($request->project_text) && !empty($request->project_text)) {
                $project = $request->project_text;

                ProjectList::create(['project'=>$request->project_text, 'created_by'=>Auth::id()]);
            } else {
                $project = $request->project_list;
            }

            $request->merge(['created_by'=>Auth::id(), 'project'=>$project]);
            unset($request['project_text'], $request['project_list']);

            if (empty($project)) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
            }

            $add = Projects::create($request->all());

            if ($add) {
                for ($i=0; $i<count($staff); $i++) {
                    $project_id = $add->id;
                    Team::create(['project_id'=>$project_id, 'user_id'=>$staff[$i]]);
                }

                $arr['project_id'] = $add->id;
                $arr['created_by'] = Auth::id();
                for ($j=1; $j<=count($third_parties); $j++) {
                    if ($third_parties[$j] == null || $third_parties[$j] == 0 || empty($third_parties[$j])) {
                        continue;
                    }
                    $arr['client_id'] = $third_parties[$j];
                    $arr['role_id'] = $roles[$j];

                    ThirdParties::create($arr);
                }

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
            'client_id' => ['required', 'integer'],
            'client_role_id' => ['required', 'integer'],
//            'third_party_id' => ['required', 'integer'],
//            'third_party_role_id' => ['required', 'integer'],
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            $staff = $request->staff;
            $third_parties = $request->third_party_id;
            $roles = $request->third_party_role_id;
            unset($request['staff'], $request['third_party_id'], $request['third_party_role_id']);

            $project = '';
            if (isset($request->project_text) && !empty($request->project_text)) {
                $project = $request->project_text;

                ProjectList::create(['project'=>$request->project_text, 'created_by'=>Auth::id()]);
            } else {
                $project = $request->project_list;
            }

            $request->merge(['project'=>$project]);
            unset($request['project_text'], $request['project_list']);

            if (empty($project)) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
            }

            $update = Projects::where(['id'=>$request->id])->update($request->all());

            if ($update) {
                $arr['project_id'] = $request->id;
                $arr['created_by'] = Auth::id();
                for ($j=1; $j<=count($third_parties); $j++) {
                    if ($third_parties[$j] == null || $third_parties[$j] == 0 || empty($third_parties[$j])) {
                        continue;
                    }
                    $arr['client_id'] = $third_parties[$j];
                    $arr['role_id'] = $roles[$j];

                    ThirdParties::create($arr);
                }

                Team::where(['project_id'=>$request->id, 'deleted'=>0])->update(['deleted'=>1, 'deleted_at'=>Carbon::now(), 'deleted_by'=>Auth::id()]);

                for ($i=0; $i<count($staff); $i++) {
                    $project_id = $request->id;
                    Team::create(['project_id'=>$project_id, 'user_id'=>$staff[$i]]);
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
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Industry not found!']);
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
