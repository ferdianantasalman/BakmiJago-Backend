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

    public function cekInvoice($id)
    {
        $grouped_orders = OrderedItem::select('invoice_id')->groupBy('invoice_id')->where('invoice_id', $id)->get();

        foreach ($grouped_orders as $grouped_order) {
            $orders = OrderedItem::where('invoice_id', $grouped_order->invoice_id)->get();
            $order_details = [];
            $total_price = 0;

            foreach ($orders as $order) {
                $product = [
                    'product_id' => $order->product_id,
                    'nama_product' => $order->product->name,
                    'qty' => $order->qty,
                    'price' => $order->product->price,
                    'total_price' => $order->price,
                    'status' => $order->status,
                    'image' => $order->product->image,
                ];

                $order_details[] = $product;
                $total_price += $order->total_price;
            }

            $order_data = [
                'invoice_id' => $grouped_order->invoice_id,
                'invoice' => $grouped_order->invoice->invoice,
                'price' => $grouped_order->invoice->total_price,
                'products' => $order_details,
            ];
        }

        return response()->json($order_data);
    }
}
