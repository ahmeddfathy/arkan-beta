<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRequest;
use App\Services\AbsenceRequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class AbsenceRequestController extends Controller
{
    protected $absenceRequestService;

    public function __construct(AbsenceRequestService $absenceRequestService)
    {
        $this->absenceRequestService = $absenceRequestService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $users = collect();
        $requests = collect();

        if ($user->role === 'manager') {
            $requests = $this->absenceRequestService->getFilteredRequests(
                $request->input('employee_name'),
                $request->input('status')
            );
            $users = User::where('id', '!=', $user->id)->get();
        } elseif ($user->role === 'leader') {
            $requests = $this->absenceRequestService->getDepartmentRequests(
                $user->department,
                $request->input('employee_name'),
                $request->input('status')
            );
            $users = User::where('department', $user->department)
                ->where('id', '!=', $user->id)
                ->get();
        } else {
            $requests = $this->absenceRequestService->getUserRequests();
        }

        // حساب أيام الغياب لكل طلب
        foreach ($requests as $request) {
            $request->total_absence_days = $this->absenceRequestService->getApprovedAbsenceDays($request->user_id);
        }

        return view('absence-requests.index', compact('requests', 'users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'absence_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'registration_type' => 'required_if:role,manager,leader|in:self,other'
        ]);

        if ($request->filled('user_id') && $request->user_id != $user->id) {
            $targetUser = User::findOrFail($request->user_id);

            // التحقق من أن التيم ليدر يضيف طلب لموظف في نفس القسم
            if ($user->role === 'leader' && $targetUser->department !== $user->department) {
                return redirect()->back()->with('error', 'لا يمكنك إضافة طلب لموظف خارج قسمك');
            }

            if ($user->role === 'employee') {
                return redirect()->back()->with('error', 'لا يمكنك إضافة طلب لموظف آخر');
            }

            // التحقق من عدم وجود طلب في نفس اليوم
            $existingRequest = AbsenceRequest::where('user_id', $request->user_id)
                ->where('absence_date', $validated['absence_date'])
                ->first();

            if ($existingRequest) {
                return redirect()->back()->with('error', 'يوجد طلب غياب مسجل لهذا الموظف في نفس اليوم');
            }
        }

        if ($request->registration_type === 'self' || !$request->filled('user_id')) {
            $this->absenceRequestService->createRequest($validated);
        } else {
            $this->absenceRequestService->createRequestForUser($request->user_id, $validated);
        }

        return redirect()->route('absence-requests.index')
            ->with('success', 'تم إنشاء طلب الغياب بنجاح');
    }

    public function update(Request $request, AbsenceRequest $absenceRequest)
    {
        $user = Auth::user();

        if (
            $user->role === 'manager' ||
            ($user->role === 'leader' && $absenceRequest->user->department === $user->department) ||
            $user->id === $absenceRequest->user_id
        ) {
            $validated = $request->validate([
                'absence_date' => 'required|date|after:today',
                'reason' => 'required|string|max:255'
            ]);

            $this->absenceRequestService->updateRequest($absenceRequest, $validated);

            return redirect()->route('absence-requests.index')
                ->with('success', 'Absence request updated successfully.');
        }

        return redirect()->route('welcome')->with('error', 'Unauthorized action.');
    }

    public function destroy(AbsenceRequest $absenceRequest)
    {
        $user = Auth::user();

        if (
            $user->role === 'manager' ||
            ($user->role === 'leader' && $absenceRequest->user->department === $user->department) ||
            $user->id === $absenceRequest->user_id
        ) {
            $this->absenceRequestService->deleteRequest($absenceRequest);

            return redirect()->route('absence-requests.index')
                ->with('success', 'Absence request deleted successfully.');
        }

        return redirect()->route('welcome')->with('error', 'Unauthorized action.');
    }

    public function updateStatus(Request $request, AbsenceRequest $absenceRequest)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['manager', 'leader'])) {
            return redirect()->route('welcome')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
        ]);

        $this->absenceRequestService->updateStatus($absenceRequest, $validated);

        return redirect()->route('absence-requests.index')
            ->with('success', 'Request status updated successfully.');
    }

    public function modifyResponse(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'leader'])) {
            return redirect()->route('welcome')->with('error', 'Unauthorized action.');
        }

        $absenceRequest = AbsenceRequest::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:255'
        ]);

        $this->absenceRequestService->modifyResponse($absenceRequest, $validated);

        return redirect()->route('absence-requests.index')
            ->with('success', 'Response updated successfully');
    }

    public function resetStatus(AbsenceRequest $absenceRequest)
    {
        $user = Auth::user();

        if (!in_array($user->role, ['manager', 'leader'])) {
            return redirect()->route('welcome')->with('error', 'Unauthorized action.');
        }

        $this->absenceRequestService->resetStatus($absenceRequest);

        return redirect()->route('absence-requests.index')
            ->with('success', 'Request status reset to pending successfully.');
    }
}
