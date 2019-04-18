<?php

namespace App\Http\Controllers;

use App\Currencies;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CurrencyController extends Controller
{
    public function get_currencies() {
        $currencies = Currencies::leftJoin('users as u', 'currencies.created_by', '=', 'u.id')->where(['currencies.deleted'=>0])->select('currencies.id', 'currencies.currency', 'currencies.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);

        return view('backend.currencies')->with(['currencies'=>$currencies]);
    }

    public function post_currencies(Request $request) {
        if ($request->type == 'add') {
            return $this->add_currency($request);
        }
        else if ($request->type == 'update') {
            return $this->update_currency($request);
        }
        else if ($request->type == 'delete') {
            return $this->delete_currency($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add currency
    private function add_currency(Request $request) {
        $validator = Validator::make($request->all(), [
            'currency' => 'required|string|max:10',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            Currencies::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update currency
    private function update_currency(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'currency' => 'required|string|max:10',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            Currencies::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete currency
    private function delete_currency(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            Currencies::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
