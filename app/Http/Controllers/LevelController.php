<?php

namespace App\Http\Controllers;

use App\Currencies;
use App\UserLevels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LevelController extends Controller
{
    public function get_user_levels() {
        $levels = UserLevels::leftJoin('users as u', 'user_levels.created_by', '=', 'u.id')->leftJoin('currencies as c', 'user_levels.currency_id', '=', 'c.id')->where(['user_levels.deleted'=>0])->select('user_levels.id', 'user_levels.level', 'user_levels.description', 'user_levels.percentage', 'user_levels.hourly_rate', 'user_levels.currency_id', 'c.currency', 'user_levels.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);
        $currencies = Currencies::where(['deleted'=>0])->select('id', 'currency')->get();

        return view('backend.user_levels')->with(['levels'=>$levels, 'currencies'=>$currencies]);
    }

    public function post_user_levels(Request $request) {
        if ($request->type == 'add') {
            return $this->add_user_level($request);
        }
        else if ($request->type == 'update') {
            return $this->update_user_level($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_user_level($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add user_level
    private function add_user_level(Request $request) {
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|max:50',
            'percentage' => 'required|integer',
            'hourly_rate' => 'required|numeric',
            'currency_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            UserLevels::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update user_level
    private function update_user_level(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'level' => 'required|string|max:50',
            'percentage' => 'required|integer',
            'hourly_rate' => 'required|numeric',
            'currency_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            UserLevels::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete user_level
    private function delete_user_level(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            UserLevels::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
