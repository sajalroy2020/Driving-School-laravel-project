<?php

namespace App\Http\Services;

use App\Models\Award;
use App\Models\AwardAssign;
use App\Models\Category;
use App\Models\CertificateAssign;
use App\Models\CertificateConfiger;
use App\Models\Enrolment;
use App\Models\Exam;
use App\Models\IncomeExpense;
use App\Models\Instructor;
use App\Models\Notice;
use App\Models\Package;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserContact;
use App\Models\UserDocument;
use App\Traits\JsonResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportService
{
    use JsonResponseTrait;

    public function list()
    {
        $data = Notice::query()
            ->where('tenant_id', auth()->user()->tenant_id);
        return datatables($data)
            ->addIndexColumn()
            ->editColumn('notice_title', function ($data) {
                return "<p>$data->notice_title</p>";
            })
            ->editColumn('created_at', function ($data) {
                return $data->created_at->diffForHumans();
            })
            ->editColumn('notice_for', function ($data) {
                $html = '';
                if ($data->notice_for == NOTICE_FOR_STUDENT) {
                    $html = "<p>" . __('Student') . "</p>";
                } elseif ($data->notice_for == NOTICE_FOR_INSTRUCTOR) {
                    $html = "<p>" . __('Instructor') . "</p>";
                } else {
                    $html = "<p>" . __('All') . "</p>";
                }
                return $html;
            })
            ->editColumn('status', function ($data) {
                return getStatusHtml($data->status);
            })
            ->addColumn('action', function ($data) {
                return '<div class="dropdown dropdown-one">
                                <button class="dropdown-toggle p-0 bg-transparent w-22 h-22 ms-auto bd-one bd-c-stroke rounded-circle fs-13 text-main-color d-flex justify-content-center align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa-solid fa-ellipsis"></i></button>
                                <ul class="dropdown-menu dropdownItem-one">
                                    <li>
                                        <button onclick="editCommonModal(\'' . route('admin.notice.details', encrypt($data->id)) . '\'' . ', \'#details-modal\')" class="border-0 bg-transparent p-0 d-flex align-items-center cg-8">
                                            <div class="d-flex">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.37405 12.3634L2.66794 11.9589L2.37405 12.3634ZM1.63661 11.626L2.04112 11.3321L1.63661 11.626ZM12.3634 11.626L11.9589 11.3321L12.3634 11.626ZM11.626 12.3634L11.3321 11.9589L11.626 12.3634ZM11.626 1.63661L11.3321 2.04112L11.626 1.63661ZM12.3634 2.37405L11.9589 2.66794L12.3634 2.37405ZM2.37405 1.63661L2.66794 2.04112L2.37405 1.63661ZM1.63661 2.37405L2.04112 2.66794L1.63661 2.37405ZM5 6.5C4.72386 6.5 4.5 6.72386 4.5 7C4.5 7.27614 4.72386 7.5 5 7.5V6.5ZM9 7.5C9.27614 7.5 9.5 7.27614 9.5 7C9.5 6.72386 9.27614 6.5 9 6.5V7.5ZM6.5 9C6.5 9.27614 6.72386 9.5 7 9.5C7.27614 9.5 7.5 9.27614 7.5 9H6.5ZM7.5 5C7.5 4.72386 7.27614 4.5 7 4.5C6.72386 4.5 6.5 4.72386 6.5 5H7.5ZM7 12.5C5.73895 12.5 4.83333 12.4993 4.13203 12.4233C3.44009 12.3484 3.00661 12.2049 2.66794 11.9589L2.08016 12.7679C2.61771 13.1585 3.24729 13.3333 4.02432 13.4175C4.79198 13.5007 5.76123 13.5 7 13.5V12.5ZM0.5 7C0.5 8.23877 0.499314 9.20802 0.582485 9.97568C0.666671 10.7527 0.841549 11.3823 1.2321 11.9198L2.04112 11.3321C1.79506 10.9934 1.65163 10.5599 1.57667 9.86797C1.50069 9.16667 1.5 8.26105 1.5 7H0.5ZM2.66794 11.9589C2.42741 11.7841 2.21588 11.5726 2.04112 11.3321L1.2321 11.9198C1.46854 12.2453 1.75473 12.5315 2.08016 12.7679L2.66794 11.9589ZM12.5 7C12.5 8.26105 12.4993 9.16667 12.4233 9.86797C12.3484 10.5599 12.2049 10.9934 11.9589 11.3321L12.7679 11.9198C13.1585 11.3823 13.3333 10.7527 13.4175 9.97568C13.5007 9.20802 13.5 8.23877 13.5 7H12.5ZM7 13.5C8.23877 13.5 9.20802 13.5007 9.97568 13.4175C10.7527 13.3333 11.3823 13.1585 11.9198 12.7679L11.3321 11.9589C10.9934 12.2049 10.5599 12.3484 9.86797 12.4233C9.16667 12.4993 8.26105 12.5 7 12.5V13.5ZM11.9589 11.3321C11.7841 11.5726 11.5726 11.7841 11.3321 11.9589L11.9198 12.7679C12.2453 12.5315 12.5315 12.2453 12.7679 11.9198L11.9589 11.3321ZM7 1.5C8.26105 1.5 9.16667 1.50069 9.86797 1.57667C10.5599 1.65163 10.9934 1.79506 11.3321 2.04112L11.9198 1.2321C11.3823 0.841549 10.7527 0.666671 9.97568 0.582485C9.20802 0.499314 8.23877 0.5 7 0.5V1.5ZM13.5 7C13.5 5.76123 13.5007 4.79198 13.4175 4.02432C13.3333 3.24729 13.1585 2.61771 12.7679 2.08016L11.9589 2.66794C12.2049 3.00661 12.3484 3.44009 12.4233 4.13203C12.4993 4.83333 12.5 5.73895 12.5 7H13.5ZM11.3321 2.04112C11.5726 2.21588 11.7841 2.42741 11.9589 2.66794L12.7679 2.08016C12.5315 1.75473 12.2453 1.46854 11.9198 1.2321L11.3321 2.04112ZM7 0.5C5.76123 0.5 4.79198 0.499314 4.02432 0.582485C3.24729 0.666671 2.61771 0.841549 2.08016 1.2321L2.66794 2.04112C3.00661 1.79506 3.44009 1.65163 4.13203 1.57667C4.83333 1.50069 5.73895 1.5 7 1.5V0.5ZM1.5 7C1.5 5.73895 1.50069 4.83333 1.57667 4.13203C1.65163 3.44009 1.79506 3.00661 2.04112 2.66794L1.2321 2.08016C0.841549 2.61771 0.666671 3.24729 0.582485 4.02432C0.499314 4.79198 0.5 5.76123 0.5 7H1.5ZM2.08016 1.2321C1.75473 1.46854 1.46854 1.75473 1.2321 2.08016L2.04112 2.66794C2.21588 2.42741 2.42741 2.21588 2.66794 2.04112L2.08016 1.2321ZM5 7.5H9V6.5H5V7.5ZM7.5 9V5H6.5V9H7.5Z" fill="#4C40F7"/></svg>
                                            </div>
                                            <p class="fs-14 fw-500 lh-19 text-main-color">' . __("Details") . '</p>
                                        </button>
                                    </li>
                                    <li>
                                        <button onclick="editCommonModal(\'' . route('admin.notice.edit', encrypt($data->id)) . '\'' . ', \'#edit-modal\')" class="border-0 bg-transparent p-0 d-flex align-items-center cg-8" >
                                            <div class="d-flex">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M2.37405 12.3634L2.66794 11.9589L2.37405 12.3634ZM1.63661 11.626L2.04112 11.3321L1.63661 11.626ZM12.3634 11.626L11.9589 11.3321L12.3634 11.626ZM11.626 12.3634L11.3321 11.9589L11.626 12.3634ZM11.626 1.63661L11.3321 2.04112L11.626 1.63661ZM12.3634 2.37405L11.9589 2.66794L12.3634 2.37405ZM2.37405 1.63661L2.66794 2.04112L2.37405 1.63661ZM1.63661 2.37405L2.04112 2.66794L1.63661 2.37405ZM5 6.5C4.72386 6.5 4.5 6.72386 4.5 7C4.5 7.27614 4.72386 7.5 5 7.5V6.5ZM9 7.5C9.27614 7.5 9.5 7.27614 9.5 7C9.5 6.72386 9.27614 6.5 9 6.5V7.5ZM6.5 9C6.5 9.27614 6.72386 9.5 7 9.5C7.27614 9.5 7.5 9.27614 7.5 9H6.5ZM7.5 5C7.5 4.72386 7.27614 4.5 7 4.5C6.72386 4.5 6.5 4.72386 6.5 5H7.5ZM7 12.5C5.73895 12.5 4.83333 12.4993 4.13203 12.4233C3.44009 12.3484 3.00661 12.2049 2.66794 11.9589L2.08016 12.7679C2.61771 13.1585 3.24729 13.3333 4.02432 13.4175C4.79198 13.5007 5.76123 13.5 7 13.5V12.5ZM0.5 7C0.5 8.23877 0.499314 9.20802 0.582485 9.97568C0.666671 10.7527 0.841549 11.3823 1.2321 11.9198L2.04112 11.3321C1.79506 10.9934 1.65163 10.5599 1.57667 9.86797C1.50069 9.16667 1.5 8.26105 1.5 7H0.5ZM2.66794 11.9589C2.42741 11.7841 2.21588 11.5726 2.04112 11.3321L1.2321 11.9198C1.46854 12.2453 1.75473 12.5315 2.08016 12.7679L2.66794 11.9589ZM12.5 7C12.5 8.26105 12.4993 9.16667 12.4233 9.86797C12.3484 10.5599 12.2049 10.9934 11.9589 11.3321L12.7679 11.9198C13.1585 11.3823 13.3333 10.7527 13.4175 9.97568C13.5007 9.20802 13.5 8.23877 13.5 7H12.5ZM7 13.5C8.23877 13.5 9.20802 13.5007 9.97568 13.4175C10.7527 13.3333 11.3823 13.1585 11.9198 12.7679L11.3321 11.9589C10.9934 12.2049 10.5599 12.3484 9.86797 12.4233C9.16667 12.4993 8.26105 12.5 7 12.5V13.5ZM11.9589 11.3321C11.7841 11.5726 11.5726 11.7841 11.3321 11.9589L11.9198 12.7679C12.2453 12.5315 12.5315 12.2453 12.7679 11.9198L11.9589 11.3321ZM7 1.5C8.26105 1.5 9.16667 1.50069 9.86797 1.57667C10.5599 1.65163 10.9934 1.79506 11.3321 2.04112L11.9198 1.2321C11.3823 0.841549 10.7527 0.666671 9.97568 0.582485C9.20802 0.499314 8.23877 0.5 7 0.5V1.5ZM13.5 7C13.5 5.76123 13.5007 4.79198 13.4175 4.02432C13.3333 3.24729 13.1585 2.61771 12.7679 2.08016L11.9589 2.66794C12.2049 3.00661 12.3484 3.44009 12.4233 4.13203C12.4993 4.83333 12.5 5.73895 12.5 7H13.5ZM11.3321 2.04112C11.5726 2.21588 11.7841 2.42741 11.9589 2.66794L12.7679 2.08016C12.5315 1.75473 12.2453 1.46854 11.9198 1.2321L11.3321 2.04112ZM7 0.5C5.76123 0.5 4.79198 0.499314 4.02432 0.582485C3.24729 0.666671 2.61771 0.841549 2.08016 1.2321L2.66794 2.04112C3.00661 1.79506 3.44009 1.65163 4.13203 1.57667C4.83333 1.50069 5.73895 1.5 7 1.5V0.5ZM1.5 7C1.5 5.73895 1.50069 4.83333 1.57667 4.13203C1.65163 3.44009 1.79506 3.00661 2.04112 2.66794L1.2321 2.08016C0.841549 2.61771 0.666671 3.24729 0.582485 4.02432C0.499314 4.79198 0.5 5.76123 0.5 7H1.5ZM2.08016 1.2321C1.75473 1.46854 1.46854 1.75473 1.2321 2.08016L2.04112 2.66794C2.21588 2.42741 2.42741 2.21588 2.66794 2.04112L2.08016 1.2321ZM5 7.5H9V6.5H5V7.5ZM7.5 9V5H6.5V9H7.5Z" fill="#4C40F7"/></svg>
                                            </div>
                                            <p class="fs-14 fw-500 lh-19 text-main-color">' . __("Edit") . '</p>
                                        </button>
                                    </li>
                                    <li>
                                        <button onclick="deleteCommonMethod(\'' . route('admin.notice.delete', encrypt($data->id)) . '\', \'noticeDataTable\')" class="border-0 bg-transparent p-0 d-flex align-items-center cg-8">
                                            <div class="d-flex">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.91107 7V9.66667M8.57774 7V9.66667" stroke="#F73E04" stroke-linecap="round"/><path d="M1.24442 3.1884C0.968273 3.1884 0.744415 3.41225 0.744415 3.6884C0.744415 3.96454 0.968273 4.1884 1.24442 4.1884V3.1884ZM13.2444 4.1884C13.5206 4.1884 13.7444 3.96454 13.7444 3.6884C13.7444 3.41225 13.5206 3.1884 13.2444 3.1884V4.1884ZM2.57775 3.6884V3.1884H2.07775V3.6884H2.57775ZM11.9111 3.6884H12.4111V3.1884H11.9111V3.6884ZM11.4723 10.2202L11.9527 10.3589L11.4723 10.2202ZM8.39372 12.9036L8.47703 13.3966L8.47703 13.3966L8.39372 12.9036ZM6.09509 12.9036L6.1784 12.4106L6.1784 12.4106L6.09509 12.9036ZM5.98993 12.8858L5.90661 13.3788L5.90662 13.3788L5.98993 12.8858ZM3.01651 10.2202L2.53613 10.3589L3.01651 10.2202ZM8.4989 12.8858L8.41558 12.3928L8.41558 12.3928L8.4989 12.8858ZM4.47278 2.65959L4.92664 2.86938L4.92664 2.86938L4.47278 2.65959ZM5.1231 1.78741L4.78941 1.41505L4.78941 1.41505L5.1231 1.78741ZM6.09637 1.20464L6.27036 1.67339L6.27036 1.67339L6.09637 1.20464ZM8.39247 1.20464L8.56646 0.735893L8.56646 0.735893L8.39247 1.20464ZM10.0161 2.65959L10.4699 2.44981L10.4699 2.44981L10.0161 2.65959ZM1.24442 4.1884H13.2444V3.1884H1.24442V4.1884ZM8.41558 12.3928L8.31041 12.4106L8.47703 13.3966L8.58221 13.3788L8.41558 12.3928ZM6.1784 12.4106L6.07324 12.3928L5.90662 13.3788L6.01178 13.3966L6.1784 12.4106ZM11.4111 3.6884V7.11722H12.4111V3.6884H11.4111ZM3.07775 7.11724V3.6884H2.07775V7.11724H3.07775ZM11.4111 7.11722C11.4111 8.12037 11.2699 9.11842 10.9919 10.0815L11.9527 10.3589C12.2568 9.30556 12.4111 8.21414 12.4111 7.11722H11.4111ZM8.31041 12.4106C7.60465 12.5298 6.88415 12.5298 6.1784 12.4106L6.01177 13.3966C6.82783 13.5345 7.66098 13.5345 8.47703 13.3966L8.31041 12.4106ZM6.07324 12.3928C4.8479 12.1857 3.84546 11.289 3.4969 10.0815L2.53613 10.3589C2.99027 11.932 4.29886 13.1071 5.90661 13.3788L6.07324 12.3928ZM3.4969 10.0815C3.21888 9.11841 3.07775 8.12038 3.07775 7.11724H2.07775C2.07775 8.21415 2.23207 9.30555 2.53613 10.3589L3.4969 10.0815ZM8.58221 13.3788C10.19 13.1071 11.4985 11.9321 11.9527 10.3589L10.9919 10.0815C10.6433 11.289 9.64091 12.1857 8.41558 12.3928L8.58221 13.3788ZM4.74442 3.6884C4.74442 3.40911 4.80571 3.13099 4.92664 2.86938L4.01892 2.44981C3.83831 2.84053 3.74442 3.26159 3.74442 3.6884H4.74442ZM4.92664 2.86938C5.04766 2.60755 5.22679 2.36588 5.45678 2.15978L4.78941 1.41505C4.46225 1.70823 4.19942 2.05929 4.01892 2.44981L4.92664 2.86938ZM5.45678 2.15978C5.68688 1.95358 5.96292 1.78751 6.27036 1.67339L5.92237 0.735893C5.50186 0.891983 5.11647 1.12196 4.78941 1.41505L5.45678 2.15978ZM6.27036 1.67339C6.57782 1.55926 6.90895 1.5 7.24442 1.5V0.5C6.79195 0.5 6.34286 0.579811 5.92237 0.735893L6.27036 1.67339ZM7.24442 1.5C7.57988 1.5 7.91101 1.55926 8.21847 1.67339L8.56646 0.735893C8.14597 0.579811 7.69688 0.5 7.24442 0.5V1.5ZM8.21847 1.67339C8.52591 1.78751 8.80195 1.95358 9.03205 2.15978L9.69942 1.41505C9.37237 1.12197 8.98698 0.891984 8.56646 0.735893L8.21847 1.67339ZM9.03205 2.15978C9.26204 2.36588 9.44117 2.60755 9.56219 2.86938L10.4699 2.44981C10.2894 2.05929 10.0266 1.70823 9.69942 1.41505L9.03205 2.15978ZM9.56219 2.86938C9.68312 3.131 9.74442 3.40911 9.74442 3.6884H10.7444C10.7444 3.26159 10.6505 2.84053 10.4699 2.44981L9.56219 2.86938ZM2.57775 4.1884H11.9111V3.1884H2.57775V4.1884Z" fill="#F73E04"/>
                                                </svg>
                                            </div>
                                            <p class="fs-14 fw-500 lh-19 text-red text-nowrap">' . __("Delete") . '</p>
                                        </button>
                                    </li>
                                </ul>
                            </div>';
            })
            ->rawColumns(['notice_title', 'created_at', 'action', 'status', 'notice_for'])
            ->make(true);
    }

    public function generation($request)
    {
        try {
            $reportData = '';
            if ($request->module_name == 'student') {
                $data['student_data'] = User::query()
                    ->where(['tenant_id' => auth()->user()->tenant_id, 'role' => USER_ROLE_STUDENT])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['stdPackages','enrolments'])
                    ->get();
                $reportData = view('admin.report.partial.student-report-render', $data)->render();
            } elseif ($request->module_name == 'instructor') {
                $data['instructor_data'] = User::where(['tenant_id' => auth()->user()->tenant_id, 'role' => USER_ROLE_INSTRUCTOR])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['instructor', 'packages'])
                    ->get();
                $reportData = view('admin.report.partial.instructor-report-render', $data)->render();
            } elseif ($request->module_name == 'staff') {
                $data['staff_data'] = User::where(['tenant_id' => auth()->user()->tenant_id, 'role' => USER_ROLE_STAFF])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['permissions'])
                    ->get();
                $reportData = view('admin.report.partial.staff-report-render', $data)->render();
            } elseif ($request->module_name == 'package') {
                $data['package_data'] = Package::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['category','instructors','stdPackages'])
                    ->get();
                $reportData = view('admin.report.partial.package-report-render', $data)->render();
            } elseif ($request->module_name == 'enrolment') {
                $data['enrolment_data'] = Enrolment::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['student','package','timeSchedule','slot'])
                    ->get();
                $reportData = view('admin.report.partial.enrolment-report-render', $data)->render();
            } elseif ($request->module_name == 'payment') {
                $data['payment_data'] = Payment::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['paymentGateway','student','enrolment','package'])
                    ->get();
                $reportData = view('admin.report.partial.payment-report-render', $data)->render();
            } elseif ($request->module_name == 'certificate') {
                $data['certificate_data'] = CertificateAssign::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['certificateStudent'])
                    ->get();
                $reportData = view('admin.report.partial.certificate-report-render', $data)->render();
            } elseif ($request->module_name == 'exam') {
                $data['exam_data'] = Exam::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->get();
//                dd($data['exam_data']);
                $reportData = view('admin.report.partial.exam-report-render', $data)->render();
            }elseif ($request->module_name == 'award') {
                $data['award_data'] = AwardAssign::where(['tenant_id' => auth()->user()->tenant_id])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->with(['awards','student'])
                    ->get();
                $reportData = view('admin.report.partial.award-report-render', $data)->render();
            }elseif ($request->module_name == 'income') {
                $data['income_data'] = IncomeExpense::where(['tenant_id' => auth()->user()->tenant_id, 'type' => INCOME_EXPENSE_TYPE_INCOME])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->get();
                $reportData = view('admin.report.partial.income-report-render', $data)->render();
            }elseif ($request->module_name == 'expense') {
                $data['expense_data'] = IncomeExpense::where(['tenant_id' => auth()->user()->tenant_id, 'type' => INCOME_EXPENSE_TYPE_EXPENSE])
                    ->where(function ($query) use ($request) {
                        if ($request->duration == 2) {
                            $query->whereMonth('created_at', Carbon::now()->month);
                        } elseif ($request->duration == 3) {
                            $query->whereYear('created_at', Carbon::now()->year);
                        } elseif ($request->duration == 4) {
                            $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
                            $endData = Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d');
                            $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endData]);
                        }
                    })
                    ->get();
                $reportData = view('admin.report.partial.expense-report-render', $data)->render();
            }

            return $this->successResponse($reportData, __(MSG_DATA_FETCH_SUCCESSFULLY));
        }catch (Exception $exception){
            Log::info($exception->getMessage());
            dd($exception->getMessage());
            return $this->errorResponse([], __(MSG_SOMETHING_WENT_WRONG));
        }

    }
}
