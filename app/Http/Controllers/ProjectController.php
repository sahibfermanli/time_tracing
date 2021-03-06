<?php

namespace App\Http\Controllers;

use App\ActionLogs;
use App\Categories;
use App\ClientRoles;
use App\Clients;
use App\Countries;
use App\Currencies;
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
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProjectController extends HomeController
{
    //for manager
    public function get_projects() {
        $query = Projects::leftJoin('users as created', 'projects.created_by', '=', 'created.id')
            ->leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
            ->leftJoin('client_roles as cr', 'projects.client_role_id', '=', 'cr.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->leftJoin('users as pm', 'projects.project_manager_id', '=', 'pm.id')
            ->leftJoin('currencies as cur', 'projects.currency_id', '=', 'cur.id')
            ->where(['projects.deleted'=>0]);

        $search_arr = array(
            'description' => '',
            'client' => '',
            'role' => '',
            'project' => '',
            'manager' => '',
            'start_date' => '',
            'end_date' => ''
        );

        if (!empty(Input::get('description')) && Input::get('description') != ''  && Input::get('description') != null) {
            $where_description = Input::get('description');
            $query->where('projects.description', 'LIKE', '%'.$where_description.'%');
            $search_arr['description'] = $where_description;
        }

        if (!empty(Input::get('client')) && Input::get('client') != ''  && Input::get('client') != null) {
            $where_client = Input::get('client');
            $query->where('projects.client_id', $where_client);
            $search_arr['client'] = $where_client;
        }

        if (!empty(Input::get('role')) && Input::get('role') != ''  && Input::get('role') != null) {
            $where_role = Input::get('role');
            $query->where('projects.client_role_id', $where_role);
            $search_arr['role'] = $where_role;
        }

        if (!empty(Input::get('project')) && Input::get('project') != ''  && Input::get('project') != null) {
            $where_project = Input::get('project');
            $query->where('projects.project', $where_project);
            $search_arr['project'] = $where_project;
        }

        if (!empty(Input::get('manager')) && Input::get('manager') != ''  && Input::get('manager') != null) {
            $where_manager = Input::get('manager');
            $query->where('projects.project_manager_id', $where_manager);
            $search_arr['manager'] = $where_manager;
        }

        //short by start
        $short_by = 'projects.id';
        $shortType = 'DESC';
        if (!empty(Input::get('shortBy')) && Input::get('shortBy') != ''  && Input::get('shortBy') != null) {
            $short_by = Input::get('shortBy');
        }
        if (!empty(Input::get('shortType')) && Input::get('shortType') != ''  && Input::get('shortType') != null) {
            $short_type = Input::get('shortType');
            if ($short_type == 2) {
                $shortType = 'DESC';
            } else {
                $shortType = 'ASC';
            }
        }
        //short by finish

        $projects = $query->orderBy($short_by, $shortType)
            ->select('projects.id', 'projects.project', 'projects.description', 'projects.time', 'projects.total_payment', 'projects.currency_id', 'cur.currency', 'projects.created_at', 'projects.client_id', 'projects.client_role_id', 'c.name as client_name', 'fob.title as client_fob', 'c.director as client_director', 'cr.role as client_role', 'created.name as created_name', 'created.surname as created_surname', 'projects.project_manager_id', 'pm.name as pm_name', 'pm.surname as pm_surname')
            ->paginate(30);

        $project_managers = User::where(['deleted'=>0])->whereIn('role_id', [4, 1])->orderBy('name')->select('id', 'name', 'surname')->get();
        $users = User::where(['deleted'=>0, 'role_id'=>2])->orderBy('name')->select('id', 'name', 'surname')->get();
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
        $project_list = ProjectList::where(['deleted'=>0])->orderBy('project')->select('project')->get();
        $client_roles = ClientRoles::where(['deleted'=>0])->select('id', 'role')->get();
        //for add new third party modal
        $industries = Categories::where('up_category', '=', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $form_of_businesses = FormOfBusiness::where(['deleted'=>0])->select('id', 'title')->get();
        $countries = Countries::where(['deleted'=>0])->select('id', 'country')->get();

        return view('backend.projects')->with(['projects'=>$projects, 'project_managers'=>$project_managers, 'clients'=>$clients, 'project_list'=>$project_list, 'users'=>$users, 'client_roles'=>$client_roles, 'form_of_businesses'=>$form_of_businesses, 'countries'=>$countries, 'industries'=>$industries, 'search_arr'=>$search_arr]);
    }

    //for project manager
    public function get_projects_for_project_manager() {
        $query = Projects::leftJoin('users as created', 'projects.created_by', '=', 'created.id')
            ->leftJoin('clients as c', 'projects.client_id', '=', 'c.id')
            ->leftJoin('client_roles as cr', 'projects.client_role_id', '=', 'cr.id')
            ->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')
            ->leftJoin('users as pm', 'projects.project_manager_id', '=', 'pm.id')
            ->leftJoin('currencies as cur', 'projects.currency_id', '=', 'cur.id')
            ->where(['projects.deleted'=>0, 'project_manager_id'=>Auth::id()]);

        $search_arr = array(
            'description' => '',
            'client' => '',
            'role' => '',
            'project' => '',
            'manager' => '',
            'start_date' => '',
            'end_date' => ''
        );

        if (!empty(Input::get('description')) && Input::get('description') != ''  && Input::get('description') != null) {
            $where_description = Input::get('description');
            $query->where('projects.description', 'LIKE', '%'.$where_description.'%');
            $search_arr['description'] = $where_description;
        }

        if (!empty(Input::get('client')) && Input::get('client') != ''  && Input::get('client') != null) {
            $where_client = Input::get('client');
            $query->where('projects.client_id', $where_client);
            $search_arr['client'] = $where_client;
        }

        if (!empty(Input::get('role')) && Input::get('role') != ''  && Input::get('role') != null) {
            $where_role = Input::get('role');
            $query->where('projects.client_role_id', $where_role);
            $search_arr['role'] = $where_role;
        }

        if (!empty(Input::get('project')) && Input::get('project') != ''  && Input::get('project') != null) {
            $where_project = Input::get('project');
            $query->where('projects.project', $where_project);
            $search_arr['project'] = $where_project;
        }

        if (!empty(Input::get('manager')) && Input::get('manager') != ''  && Input::get('manager') != null) {
            $where_manager = Input::get('manager');
            $query->where('projects.project_manager_id', $where_manager);
            $search_arr['manager'] = $where_manager;
        }

        //short by start
        $short_by = 'projects.id';
        $shortType = 'DESC';
        if (!empty(Input::get('shortBy')) && Input::get('shortBy') != ''  && Input::get('shortBy') != null) {
            $short_by = Input::get('shortBy');
        }
        if (!empty(Input::get('shortType')) && Input::get('shortType') != ''  && Input::get('shortType') != null) {
            $short_type = Input::get('shortType');
            if ($short_type == 2) {
                $shortType = 'DESC';
            } else {
                $shortType = 'ASC';
            }
        }
        //short by finish

        $projects = $query->orderBy($short_by, $shortType)
            ->select('projects.id', 'projects.project', 'projects.description', 'projects.time', 'projects.total_payment', 'projects.currency_id', 'cur.currency', 'projects.created_at', 'projects.client_id', 'projects.client_role_id', 'c.name as client_name', 'fob.title as client_fob', 'c.director as client_director', 'cr.role as client_role', 'created.name as created_name', 'created.surname as created_surname', 'projects.project_manager_id', 'pm.name as pm_name', 'pm.surname as pm_surname')
            ->paginate(30);

        $project_managers = User::where(['deleted'=>0])->whereIn('role_id', [4, 1])->orderBy('name')->select('id', 'name', 'surname')->get();
        $users = User::where(['deleted'=>0, 'role_id'=>2])->orderBy('name')->select('id', 'name', 'surname')->get();
        $clients = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.deleted'=>0])->orderBy('clients.name')->select('clients.id', 'clients.name', 'fob.title as fob')->get();
        $project_list = ProjectList::where(['deleted'=>0])->orderBy('project')->select('project')->get();
        $client_roles = ClientRoles::where(['deleted'=>0])->select('id', 'role')->get();
        //for add new third party modal
        $industries = Categories::where('up_category', '=', 0)->where(['deleted'=>0])->select('id', 'category')->get();
        $form_of_businesses = FormOfBusiness::where(['deleted'=>0])->select('id', 'title')->get();
        $countries = Countries::where(['deleted'=>0])->select('id', 'country')->get();

        return view('backend.projects_for_project_manager')->with(['projects'=>$projects, 'project_managers'=>$project_managers, 'clients'=>$clients, 'project_list'=>$project_list, 'users'=>$users, 'client_roles'=>$client_roles, 'form_of_businesses'=>$form_of_businesses, 'countries'=>$countries, 'industries'=>$industries, 'search_arr'=>$search_arr]);
    }

    //for manager
    public function post_projects(Request $request) {
        if ($request->type == 'add' && Auth::user()->role() == 1) {
            return $this->add_project($request);
        }
        else if ($request->type == 'update') {
            return $this->update_project($request);
        }
        else if ($request->type == 'delete' && Auth::user()->role() == 1) {
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
        else if ($request->type == 'delete_staff') {
            return $this->delete_staff($request);
        }
        else if ($request->type == 'same_client_control') {
            return $this->same_client_control($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //same client control
    private function same_client_control(Request $request) {
        $validator = Validator::make($request->all(), [
            'val' => 'required',
            'col' => 'required',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Value not found!']);
        }
        try {
            $value = $request->val;
            $column = '';
            switch ($request->col) {
                case 'mail':
                    $column = 'email';
                    break;
                case 'tel':
                    $column = 'phone';
                    break;
                case 'web':
                    $column = 'web_site';
                    break;
                case 'address':
                    $column = 'address';
                    break;
                default:
                    return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Column not found!']);
            }

            $client_id = 0;
            $same = false;
            if (Clients::where([$column=>$value, 'deleted'=>0])->count() > 0) {
                $client = Clients::where([$column=>$value, 'deleted'=>0])->select('id')->first();
                $client_id = $client->id;
                $same = true;
            } else {
                $same = false;
            }

            return response(['case' => 'success', 'same'=>$same, 'client_id'=>$client_id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
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
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Project not found!']);
        }
        try {
            $team = Team::leftJoin('users as u', 'team.user_id', '=', 'u.id')->leftJoin('currencies as c', 'team.currency_id', '=', 'c.id')->where(['team.project_id'=>$request->project_id, 'team.deleted'=>0, 'u.deleted'=>0])->select('team.id', 'team.project_id', 'team.user_id', 'u.name', 'u.surname', 'team.percentage', 'team.hourly_rate', 'c.currency')->get();

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

    //delete staff
    private function delete_staff(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Staff not found!']);
        }
        try {
            $current_date = Carbon::now();

            Team::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
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
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id'], $request['deleted_staffs']);

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

            $staff_mail_arr = array();
            $user_arr = array();
            if ($add) {
                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        //same staff control
                        if (in_array($staff[$i]['user_id'], $user_arr)) {
                            continue;
                        } else {
                            array_push($user_arr, $staff[$i]['user_id']);
                        }

                        $staff[$i]['project_id'] = $add->id;
                        Team::create($staff[$i]);
                        array_push($staff_mail_arr, $staff[$i]['user_id']);
                    }
                }

                $added_tp_arr = array();
                $arr['project_id'] = $add->id;
                $arr['created_by'] = Auth::id();
                for ($j=1; $j<=count($third_parties); $j++) {
                    if ($third_parties[$j] == null || $third_parties[$j] == 0 || empty($third_parties[$j])) {
                        continue;
                    }
                    $arr['client_id'] = $third_parties[$j];
                    $arr['role_id'] = $roles[$j];

                    $add_tp = ThirdParties::create($arr);
                    array_push($added_tp_arr, $add_tp->id);
                }

                //send email
                $email_arr = array();
                $to_arr = array();

                $project_manager = User::where(['id'=>$request->project_manager_id])->select('send_mail', 'email', 'name', 'surname')->first();

                if ($project_manager->send_mail == 1) {
                    array_push($email_arr, $project_manager['email']);
                    array_push($to_arr, $project_manager['name'] . ' ' . $project_manager['surname']);
                }

                $staffs = User::whereIn('id', $staff_mail_arr)->where('deleted', 0)->select('send_mail', 'email', 'name', 'surname', 'deleted')->get();

                foreach ($staffs as $staff) {
                    if ($staff->send_mail == 1) {
                        array_push($email_arr, $staff['email']);
                        array_push($to_arr, $staff['name'] . ' ' . $staff['surname']);
                    }
                }

                if (count($email_arr) > 0) {
                    $client = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.id'=>$request->client_id])->select('clients.name', 'fob.title as fob')->first();
                    $client_role = ClientRoles::where(['id'=>$request->client_role_id])->select('role')->first();

                    $third_parties_email_string = '';
                    if (count($added_tp_arr) > 0) {
                        $added_third_parties = ThirdParties::leftJoin('clients as c', 'third_parties.client_id', '=', 'c.id')->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')->leftJoin('client_roles as cr', 'third_parties.role_id', '=', 'cr.id')->whereIn('third_parties.id', $added_tp_arr)->select('c.name', 'fob.title as fob', 'cr.role')->get();
                        foreach ($added_third_parties as $added_third_party) {
                            $third_parties_email_string .= '<br>Third party: <b>' . $added_third_party->name . ' ' . $added_third_party->fob . ' (' . $added_third_party->role . ')' . '</b>';
                        }
                    }

                    //send email
                    $message = "You have a new project:";
                    $message .= "<br>Project type: <b>" . $request->project . "</b>";
                    $message .= "<br>Description: <b>" . $request->description . "</b>";
                    $message .= "<br>Client: <b>" . $client->name . ' ' . $client->fob . "</b>";
                    $message .= "<br>Client role: <b>" . $client_role->role . "</b>";
                    $message .= $third_parties_email_string;
                    $message .= "<br>Project manager: <b>" . $project_manager['name'] . ' ' . $project_manager['surname'] . "</b>";
                    $title = 'New project';

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

    //update project
    private function update_project(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'integer'],
            'client_id' => ['required', 'integer'],
            'client_role_id' => ['required', 'integer'],
            'project_manager_id' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            $deleted_staffs = $request->deleted_staffs;
            unset($request['type'], $request['deleted_staffs']);

            $old_data = Projects::where(['id'=>$request->id])->select('project_manager_id')->first();

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

            $update = Projects::where(['id'=>$request->id])->update($request->all());

            $staff_mail_arr = array();
            $user_arr = array();
            if ($update) {
                $deleted_staffs_arr = array();
                $deleted_staffs_arr = explode(',', $deleted_staffs);
                if (count($deleted_staffs_arr) > 0) {
                    Team::whereIn('id', $deleted_staffs_arr)->update(['deleted'=>1, 'deleted_at'=>Carbon::now(), 'deleted_by'=>Auth::id()]);
                }

                for ($i=1; $i<=count($staff); $i++) {
                    if (!empty($staff[$i]['user_id']) && $staff[$i]['user_id'] != 0) {
                        //same staff control
                        if (in_array($staff[$i]['user_id'], $user_arr)) {
                            continue;
                        } else {
                            if (Team::where(['project_id'=>$request->id, 'user_id'=>$staff[$i]['user_id'], 'deleted'=>0])->count() > 0) {
                                continue;
                            }
                            array_push($user_arr, $staff[$i]['user_id']);
                        }

                        $staff[$i]['project_id'] = $request->id;
                        Team::create($staff[$i]);
                        array_push($staff_mail_arr, $staff[$i]['user_id']);
                    }
                }

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

                //send email
                $email_arr = array();
                $to_arr = array();

                $project_manager = User::where(['id'=>$request->project_manager_id])->select('send_mail', 'email', 'name', 'surname')->first();
                if ($old_data->project_manager_id != $request->project_manager_id) {
                    if ($project_manager->send_mail == 1) {
                        array_push($email_arr, $project_manager['email']);
                        array_push($to_arr, $project_manager['name'] . ' ' . $project_manager['surname']);
                    }
                }

                $staffs = User::whereIn('id', $staff_mail_arr)->where('deleted', 0)->select('send_mail', 'email', 'name', 'surname', 'deleted')->get();

                foreach ($staffs as $staff) {
                    if ($staff->send_mail == 1) {
                        array_push($email_arr, $staff['email']);
                        array_push($to_arr, $staff['name'] . ' ' . $staff['surname']);
                    }
                }

                if (count($email_arr) > 0) {
                    $client = Clients::leftJoin('form_of_business as fob', 'clients.form_of_business_id', '=', 'fob.id')->where(['clients.id'=>$request->client_id])->orderBy('clients.name')->select('clients.name', 'fob.title as fob')->first();
                    $client_role = ClientRoles::where(['id'=>$request->client_role_id])->select('role')->first();

                    $third_parties_email_string = '';
                    $added_third_parties = ThirdParties::leftJoin('clients as c', 'third_parties.client_id', '=', 'c.id')->leftJoin('form_of_business as fob', 'c.form_of_business_id', '=', 'fob.id')->leftJoin('client_roles as cr', 'third_parties.role_id', '=', 'cr.id')->where(['third_parties.project_id'=>$request->id, 'third_parties.deleted'=>0])->select('c.name', 'fob.title as fob', 'cr.role')->get();
                    foreach ($added_third_parties as $added_third_party) {
                        $third_parties_email_string .= '<br>Third party: <b>' . $added_third_party->name . ' ' . $added_third_party->fob . ' (' . $added_third_party->role . ')' . '</b>';
                    }

                    //send email
                    $message = "You have a new project:";
                    $message .= "<br>Project type: <b>" . $request->project . "</b>";
                    $message .= "<br>Description: <b>" . $request->description . "</b>";
                    $message .= "<br>Client: <b>" . $client->name . ' ' . $client->fob . "</b>";
                    $message .= "<br>Client role: <b>" . $client_role->role . "</b>";
                    $message .= $third_parties_email_string;
                    $message .= "<br>Project manager: <b>" . $project_manager['name'] . ' ' . $project_manager['surname'] . "</b>";
                    $title = 'New project';

                    app('App\Http\Controllers\MailController')->get_send($email_arr, $to_arr, $title, $message);
                }
            }

            ActionLogs::create(['user_id'=>Auth::id(), 'action'=>'update', 'table'=>'projects', 'row_id'=>$request->id]);

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
