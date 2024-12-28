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

    // Search by employee name
    if ($request->has('employee_name') && !empty($request->employee_name)) {
      $query->where('name', 'like', "%{$request->employee_name}%");
    }

    // Search by department
    if ($request->has('department') && !empty($request->department)) {
      $query->where('department', $request->department);
    }

    // Search by employee status
    if ($request->has('status') && !empty($request->status)) {
      $query->where('employee_status', $request->status);
    }

    $users = $query->latest()->paginate(10);
    $employees = User::select('name')->distinct()->get();
    $departments = User::select('department')->distinct()->whereNotNull('department')->get();

    return view('users.index', compact('users', 'employees', 'departments'));
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
