<?php

namespace App\Http\Controllers;

use App\Categories;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CategoryController extends HomeController
{
    public function get_categories() {
        $categories = Categories::leftJoin('users as u', 'categories.created_by', '=', 'u.id')->leftJoin('categories as up', 'categories.up_category', '=', 'up.id')->where(['categories.deleted'=>0])->select('categories.id', 'categories.category', 'up.category as up_category', 'categories.up_category as up_id', 'categories.created_at', 'u.name as created_name', 'u.surname as created_surname')->paginate(30);
        $up_categories = Categories::where(['up_category'=>0, 'deleted'=>0])->select('id', 'category')->get();

        return view('backend.categories')->with(['categories'=>$categories, 'up_categories'=>$up_categories]);
    }

    public function post_categories(Request $request) {
        if ($request->type == 'add') {
            return $this->add_category($request);
        }
        if ($request->type == 'update') {
            return $this->update_category($request);
        }
        if ($request->type == 'delete') {
            return $this->delete_category($request);
        }
        else {
            return response(['case' => 'error', 'title' => 'Oops!', 'content' => 'Operation not found!']);
        }
    }

    //add category
    private function add_category(Request $request) {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|max:100',
            'up_category' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['id']);

            $request->merge(['created_by'=>Auth::id()]);

            Categories::create($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //update category
    private function update_category(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'category' => 'required|string|max:100',
            'up_category' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Fill in all required fields!']);
        }
        try {
            unset($request['_token']);
            unset($request['type']);

            if ($request->id == $request->up_category) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You cannot add itself as up category to the category!']);
            }

            if (Categories::where(['up_category'=>$request->id, 'deleted'=>0])->count() > 0 && $request->up_category != 0) {
                return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'You can\'t add up category to this category because there are subcategories of this category!']);
            }

            Categories::where(['id'=>$request->id])->update($request->all());

            Session::flash('message', 'Success!');
            Session::flash('class', 'success');
            Session::flash('display', 'block');
            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!']);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }

    //delete category
    private function delete_category(Request $request) {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response(['case' => 'warning', 'title' => 'Warning!', 'content' => 'Id not found!']);
        }
        try {
            $current_date = Carbon::now();

            $delete = Categories::where(['id'=>$request->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);

            if ($delete) {
                $sub_categories = Categories::where(['up_category'=>$request->id, 'deleted'=>0])->select('id')->get();

                foreach ($sub_categories as $category) {
                    Categories::where(['id'=>$category->id])->update(['deleted'=>1, 'deleted_at'=>$current_date, 'deleted_by'=>Auth::id()]);
                }
            }

            return response(['case' => 'success', 'title' => 'Success!', 'content' => 'Successful!', 'id'=>$request->id]);
        } catch (\Exception $e) {
            return response(['case' => 'error', 'title' => 'Error!', 'content' => 'An error occurred!']);
        }
    }
}
