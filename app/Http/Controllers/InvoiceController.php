<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderedItem;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function invoices()
    {
        $invoices = Invoice::orderBy('id', 'DESC')->get();


        // if (Auth::user()->role_id == 2) {
        //     $query->where('author_id', Auth::user()->id);
        // }

        $response = [
            'status' => 'Success',
            'message' => 'List data invoice',
            'invoices' => $invoices
        ];

        if ($invoices) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }

    public function invoicesByTime($time)
    {
        $query = Invoice::query();

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

        $invoices = $query->get();


        $response = [
            'status' => 'Success',
            'message' => 'List Data Invoice',
            'invoices' => $invoices
        ];

        if ($invoices) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }
}
