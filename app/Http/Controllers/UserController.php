<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use App\Imports\UsersImport2;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Pagination
        $users = $query->paginate(10);
        $employees = User::all();

        return view('users.index', compact('users' , 'employees'));
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function import(Request $request)
    {
        Excel::import(new UsersImport, $request->file('file'));
        return redirect()->route('users.index')
            ->with('success', 'Users imported successfully');
    }




}
