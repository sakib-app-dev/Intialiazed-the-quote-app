<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('q');
        $page = $request->input('page', 1);
        $pageSize = $request->input('page_size', 2);

        if ($keyword) {
            $users = User::where('first_name', 'like', "%$keyword%")->orWhere('last_name', 'like', "%$keyword%")->orWhere('email', 'like', "%$keyword%")->get();
            $count = User::where('first_name', 'like', "%$keyword%")->orWhere('last_name', 'like', "%$keyword%")->orWhere('email', 'like', "%$keyword%")->count();
            $total_pages = ($count + $pageSize - 1) / $pageSize;

            foreach ($users as $user) {
                $userList[] = [
                    'uuid' => $user->uuid,
                    'name' => $user->first_name . ' ' . $user->last_name,
                ];
            }
        } else {
            $users = User::all();
            $total_pages = (int) ((User::count() + $pageSize - 1) / $pageSize);
            $userList = [];
            foreach ($users as $user) {
                $userList[] = [
                    'uuid' => $user->uuid,
                    'name' => $user->first_name . ' ' . $user->last_name
                ];
            }
        }
        $nextPage = min($page + 1, $total_pages);
        $metadata = [
            'total_pages' => $total_pages,
            'current_url' => "http://127.0.0.1:8000/api/v1?q=${keyword}&page=${page}",
            'next_url' => "http://127.0.0.1:8000/api/v1?q=${keyword}&page=${nextPage}",
            'page_size' => $pageSize,
        ];
        return response()->json(['items' => $userList, 'metadata' => $metadata]);
    }
}
