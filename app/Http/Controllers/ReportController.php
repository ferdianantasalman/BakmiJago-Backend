<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {

        $report = Report::orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => 'Success',
            'message' => 'List Data Laporan',
            'reports' => $report,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required|integer',
            'keterangan'     => 'required',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'name'      => $request->name,
            'price'     => $request->price,
            'keterangan'     => $request->keterangan,
        ];

        //create product
        $report = Report::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Laporan berhasil ditambahkan',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Laporan gagal ditambahkan",
            ], 400);
        }
    }

    public function show($id)
    {
        // Show Data
        $report = Report::find($id);

        return response()->json([
            'status' => 'Success',
            'message' => 'Data Laporan ditemukan',
            'data' => $report,
        ]);
        // return new ProductResource(true, 'Data product Ditemukan!', $product);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name'     => 'required',
            'price'     => 'required|integer',
            'keterangan'     => 'required',
        ], [
            'name.required' => 'name harus diisi ngab',
            'price.required' => 'price harus diisi ngab',
            'keterangan.required' => 'keterangan harus diisi ngab',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $report = Report::find($id);

        $data = [
            'name'      => $request->name,
            'price'     => $request->price,
            'keterangan'     => $request->keterangan,
        ];

        $report->update($data);

        if ($report) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Laporan berhasil diupdate',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Laporan gagal diupdate',
            ], 400);
        }
    }

    public function destroy($id)
    {

        $report = Report::find($id)->delete();

        if ($report) {
            return response()->json([
                'status' => 'success',
                'message' => 'Laporan berhasil dihapus',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Laporan gagal dihapus',
            ], 400);
        }
    }

    public function reportsByTime($time)
    {
        $query = Report::query();

        switch ($time) {
            case 'today';
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $query->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $reports = $query->orderBy('id', 'DESC')->get();


        $response = [
            'status' => 'Success',
            'message' => 'List Data Laporan',
            'reports' => $reports
        ];

        if ($reports) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }


    // Laporan Pemasukan

    public function incomeReportByTime($time)
    {
        // $outcome = Invoice::orderBy('id', 'DESC')->sum('total_price');

        // $query = Invoice::selectRaw('SUM(total_price) as amount');

        // switch ($time) {
        //     case 'today';
        //         $query->groupByRaw('DATE(created_at)')->whereDate('created_at', Carbon::today());
        //         break;
        //     case 'this_week';
        //         $query->groupByRaw('WEEK(created_at)')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        //         break;
        //     case 'this_month';
        //         $query->groupByRaw('MONTH(created_at)')->whereMonth('created_at', Carbon::now()->month);
        //         break;
        //     case 'this_year';
        //         $query->whereYear('YEAR(created_at)', Carbon::now()->year);
        //         break;
        // }


        $queryIncome = Invoice::query();

        switch ($time) {
            case 'today';
                $queryIncome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryIncome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryIncome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryIncome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $income = $queryIncome->sum('total_price');

        $response = [
            'status' => 'Success',
            'message' => 'List Data Laporan Pengeluaran',
            'income' => $income
        ];

        if ($income) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
                'income' => '0'
            ], 400);
        }
    }


    public function outcomeReportByTime($time)
    {
        // $outcome = Report::orderBy('id', 'DESC')->sum('price');

        // $query = Report::selectRaw('SUM(price) as amount');

        // switch ($time) {
        //     case 'today';
        //         $query->groupByRaw('DATE(created_at)')->whereDate('created_at', Carbon::today());
        //         break;
        //     case 'this_week';
        //         $query->groupByRaw('WEEK(created_at)')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        //         break;
        //     case 'this_month';
        //         $query->groupByRaw('MONTH(created_at)')->whereMonth('created_at', Carbon::now()->month);
        //         break;
        //     case 'this_year';
        //         $query->whereYear('YEAR(created_at)', Carbon::now()->year);
        //         break;
        // }

        // $reports = $query->get();

        $queryOutcome = Report::query();

        switch ($time) {
            case 'today';
                $queryOutcome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryOutcome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $outcome = $queryOutcome->sum('price');


        $response = [
            'status' => 'Success',
            'message' => 'List Data Laporan Pengeluaran',
            'outcome' => $outcome
        ];

        if ($outcome) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
                'outcome' => '0'
            ], 400);
        }
    }

    public function revenueReportByTime($time)
    {
        $queryIncome = Invoice::query();

        switch ($time) {
            case 'today';
                $queryIncome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryIncome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryIncome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryIncome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryIncome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryIncome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $income = $queryIncome->sum('total_price');
        // $income = Invoice::orderBy('id', 'DESC')->sum('total_price');

        $queryOutcome = Report::query();

        switch ($time) {
            case 'today';
                $queryOutcome->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $queryOutcome->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $queryOutcome->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $queryOutcome->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $queryOutcome->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $outcome = $queryOutcome->sum('price');

        // $outcome = Report::orderBy('id', 'DESC')->sum('price');

        $revenue = $income - $outcome;


        $response = [
            'status' => 'Success',
            'message' => 'Laporan Keuntungan',
            'income' => $income,
            'outcome' => $outcome,
            'revenue' => "$revenue"
        ];

        if ($revenue) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }
}
