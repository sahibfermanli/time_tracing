<?php

namespace App\Http\Controllers;

use App\ActionLogs;
use Illuminate\Http\Request;

class LogsController extends HomeController
{
    public function get_logs() {
        try {
            $logs = ActionLogs::leftJoin('users as u', 'action_logs.user_id', '=', 'u.id')
                ->whereNotNull('action_logs.error_str')
                ->orderBy('action_logs.id', 'desc')
                ->select('action_logs.id', 'action_logs.action', 'action_logs.table', 'action_logs.error_str', 'u.username', 'action_logs.created_at')
                ->paginate(30);

            return view('backend.logs')->with(['logs'=>$logs]);
        } catch (\Exception $exception) {
            return redirect("/");
        }
    }
}
