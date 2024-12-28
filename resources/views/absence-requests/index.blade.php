@extends('layouts.app')

@section('content')
<link href="{{ asset('css/absence-management.css') }}" rel="stylesheet">
<div class="container">
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if(Auth::user()->role === 'manager' || Auth::user()->role === 'leader')
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('absence-requests.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="employee_name" class="form-label">بحث عن موظف</label>
                    <select class="form-select" id="employee_name" name="employee_name">
                        <option value="">كل الموظفين</option>
                        @foreach($users as $user)
                        <option value="{{ $user->name }}"
                            {{ request('employee_name') == $user->name ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->department }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label">تصفية حسب الحالة</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>مقبول</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">تطبيق الفلتر</button>
                    <a href="{{ route('absence-requests.index') }}" class="btn btn-secondary ms-2">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </div>
    @endif



    @if(isset($absenceDays))
    <div class="alert alert-info">
        <strong>Your Absence Days:</strong> {{ $absenceDays }}
    </div>
    @endif




    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i> طلبات الغياب
                    </h5>
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#createAbsenceModal">
                        <i class="fas fa-plus me-1"></i> طلب جديد
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>القسم</th>
                                <th>عدد أيام الغياب</th>
                                <th>التاريخ</th>
                                <th>السبب</th>
                                <th>الحالة</th>
                                <th>سبب الرفض</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr class="request-row">
                                <td>{{ $request->user->name ?? 'Unknown User' }}</td>
                                <td>{{ $request->user->department ?? 'N/A' }}</td>
                                <td>
                                    {{ $request->total_absence_days ?? 0 }}
                                </td>



                                <td>{{ $request->absence_date }}</td>
                                <td>{{ $request->reason }}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="badge bg-{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                        <small class="mt-1">
                                            Manager:
                                            <span class="badge bg-{{ $request->manager_approval === 'approved' ? 'success' : ($request->manager_approval === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($request->manager_approval) }}
                                            </span>
                                        </small>
                                        <small class="mt-1">
                                            Leader:
                                            <span class="badge bg-{{ $request->leader_approval === 'approved' ? 'success' : ($request->leader_approval === 'rejected' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($request->leader_approval) }}
                                            </span>
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @if($request->manager_rejection_reason)
                                    <strong>Manager:</strong> {{ $request->manager_rejection_reason }}<br>
                                    @endif
                                    @if($request->leader_rejection_reason)
                                    <strong>Leader:</strong> {{ $request->leader_rejection_reason }}
                                    @endif
                                </td>

                                <td>
                                    @if($request->status === 'pending')
                                    @if(Auth::user()->id === $request->user_id)
                                    <button class="btn btn-sm btn-primary edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAbsenceModal"
                                        data-request="{{ json_encode($request) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('absence-requests.destroy', $request) }}"
                                        method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endif

                                    @if(in_array(Auth::user()->role, ['manager', 'leader']))
                                    @if($request->status === 'pending')
                                    @if((Auth::user()->role === 'manager' && $request->manager_approval === 'pending') ||
                                    (Auth::user()->role === 'leader' && $request->leader_approval === 'pending'))
                                    <button class="btn btn-sm btn-info respond-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#respondModal"
                                        data-request-id="{{ $request->id }}">
                                        <i class="fas fa-reply"></i> Respond
                                    </button>
                                    @endif
                                    @endif

                                    @if(Auth::user()->role === 'manager' && $request->manager_approval !== 'pending')
                                    <form action="{{ route('absence-requests.reset-status', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Are you sure you want to reset this request to pending?')">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </form>
                                    @endif

                                    @if(Auth::user()->role === 'leader' && $request->leader_approval !== 'pending')
                                    <form action="{{ route('absence-requests.reset-status', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-secondary" onclick="return confirm('Are you sure you want to reset this request to pending?')">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                    </form>
                                    @endif
                                    @endif

                                    @if(($request->manager_approval === 'approved' || $request->manager_approval === 'rejected') && Auth::user()->role === 'manager')
                                    <button class="btn btn-sm btn-warning modify-response-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modifyResponseModal"
                                        data-request-id="{{ $request->id }}"
                                        data-status="{{ $request->manager_approval }}"
                                        data-reason="{{ $request->manager_rejection_reason }}">
                                        <i class="fas fa-edit"></i> Modify
                                    </button>
                                    @endif
                                    @if(($request->leader_approval === 'approved' || $request->leader_approval === 'rejected') && Auth::user()->role === 'leader')
                                    <button class="btn btn-sm btn-warning modify-response-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modifyResponseModal"
                                        data-request-id="{{ $request->id }}"
                                        data-status="{{ $request->leader_approval }}"
                                        data-reason="{{ $request->leader_rejection_reason }}">
                                        <i class="fas fa-edit"></i> Modify
                                    </button>
                                    @endif
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>لا توجد طلبات غياب</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>


        <!-- Create Modal -->
        <div class="modal fade" id="createAbsenceModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('absence-requests.store') }}" method="POST" id="createAbsenceForm">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">New Absence Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            @if(Auth::user()->role === 'manager' || Auth::user()->role === 'leader')
                            <div class="mb-4">
                                <label for="registration_type" class="form-label fw-bold">نوع التسجيل:</label>
                                <div>
                                    <input type="radio" id="self_registration" name="registration_type" value="self" checked onclick="toggleEmployeeSelect(true)">
                                    <label for="self_registration" class="form-label">تسجيل لنفسي</label>
                                </div>
                                <div>
                                    <input type="radio" id="other_registration" name="registration_type" value="other" onclick="toggleEmployeeSelect(false)">
                                    <label for="other_registration" class="form-label">تسجيل لموظف آخر</label>
                                </div>
                            </div>

                            <div class="mb-4" id="employee_select_container">
                                <label for="user_id" class="form-label fw-bold">اختر الموظف:</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="" disabled selected>اختر موظف</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ auth()->user()->id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                        @if(Auth::user()->role === 'leader')
                                        ({{ $user->department }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <script>
                                function toggleEmployeeSelect(isSelf) {
                                    const userSelect = document.getElementById('user_id');
                                    if (isSelf) {
                                        userSelect.value = "{{ auth()->user()->id }}";
                                        userSelect.disabled = true;
                                    } else {
                                        userSelect.value = "";
                                        userSelect.disabled = false;
                                    }
                                }

                                document.addEventListener('DOMContentLoaded', function() {
                                    toggleEmployeeSelect(document.getElementById('self_registration').checked);
                                });
                            </script>
                            @endif
                            <div class="mb-3">
                                <label for="absence_date" class="form-label">تاريخ الغياب</label>
                                <input type="date"
                                    class="form-control"
                                    id="absence_date"
                                    name="absence_date"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="reason" class="form-label">سبب الغياب</label>
                                <textarea class="form-control"
                                    id="reason"
                                    name="reason"
                                    required
                                    maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editAbsenceModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editAbsenceForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Absence Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_absence_date" class="form-label">Absence Date</label>
                                <input type="date"
                                    class="form-control"
                                    id="edit_absence_date"
                                    name="absence_date"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_reason" class="form-label">Reason</label>
                                <textarea class="form-control"
                                    id="edit_reason"
                                    name="reason"
                                    required
                                    maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Respond Modal -->
        <div class="modal fade" id="respondModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="respondForm" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Respond to Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="response_status" class="form-label">Response Status</label>
                                <select class="form-select" id="response_status" name="status" required>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                </select>
                            </div>

                            <div class="mb-3" id="response_reason_container" style="display: none;">
                                <label for="response_reason" class="form-label">Reason </label>
                                <textarea class="form-control" id="response_reason" name="rejection_reason" maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Response</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modify Response Modal -->
        <div class="modal fade" id="modifyResponseModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="modifyResponseForm" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">Modify Response</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="modify_status" class="form-label">Status</label>
                                <select class="form-select" id="modify_status" name="status" required>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="mb-3" id="modify_reason_container" style="display: none;">
                                <label for="modify_reason" class="form-label">Reason </label>
                                <textarea class="form-control" id="response_reason" name="rejection_reason" maxlength="255"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


    @endsection




    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation for new rows
            gsap.from(".request-row", {
                duration: 0.5,
                opacity: 0,
                y: 20,
                stagger: 0.1
            });

            // Edit request handling
            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const request = JSON.parse(this.dataset.request);
                    const form = document.getElementById('editAbsenceForm');
                    form.action = `/absence-requests/${request.id}`;
                    document.getElementById('edit_absence_date').value = request.absence_date;
                    document.getElementById('edit_reason').value = request.reason;
                });
            });

            // Response handling
            document.querySelectorAll('.respond-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const requestId = this.dataset.requestId;
                    const form = document.getElementById('respondForm');
                    form.action = `/absence-requests/${requestId}/status`;
                });
            });

            // Show/hide rejection reason field
            document.querySelectorAll('input[name="status"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const rejectionContainer = document.getElementById('rejection_reason_container');
                    const rejectionTextarea = document.getElementById('rejection_reason');

                    if (this.value === 'rejected') {
                        rejectionContainer.style.display = 'block';
                        rejectionTextarea.required = true;
                    } else {
                        rejectionContainer.style.display = 'none';
                        rejectionTextarea.required = false;
                    }
                });
            });

            // Form validation
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                        toastr.error('Please fill in all required fields.');
                    }
                    form.classList.add('was-validated');
                });
            });
        });

        document.querySelectorAll('.modify-response-btn').forEach(button => {
            button.addEventListener('click', function() {
                const requestId = this.dataset.requestId;
                const form = document.getElementById('modifyResponseForm');
                form.action = `/absence-requests/${requestId}/modify`;


                const requestStatus = this.dataset.status;
                const requestReason = this.dataset.reason;

                document.getElementById('modify_status').value = requestStatus;
                document.getElementById('modify_reason').value = requestReason || '';
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            document.getElementById('response_status').addEventListener('change', function() {
                const rejectionContainer = document.getElementById('response_reason_container');
                const rejectionTextarea = document.getElementById('response_reason');

                if (this.value === 'rejected') {
                    rejectionContainer.style.display = 'block';
                    rejectionTextarea.required = true;
                } else {
                    rejectionContainer.style.display = 'none';
                    rejectionTextarea.required = false;
                }
            });


            document.getElementById('modify_status').addEventListener('change', function() {
                const rejectionContainer = document.getElementById('modify_reason_container');
                const rejectionTextarea = document.getElementById('modify_reason');

                if (this.value === 'rejected') {
                    rejectionContainer.style.display = 'block';
                    rejectionTextarea.required = true;
                } else {
                    rejectionContainer.style.display = 'none';
                    rejectionTextarea.required = false;
                }
            });


            if (document.getElementById('response_status').value === 'rejected') {
                document.getElementById('response_reason_container').style.display = 'block';
            }

            if (document.getElementById('modify_status').value === 'rejected') {
                document.getElementById('modify_reason_container').style.display = 'block';
            }
        });
    </script>


    @endpush