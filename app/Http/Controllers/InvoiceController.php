<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\OrderedItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class InvoiceController extends Controller
{
    public function invoices(Request $request)
    {
        $response = [
            'status' => 'Success',
            'data' => [],
        ];

        $grouped_orders = OrderedItem::select('invoice_id')->groupBy('invoice_id')->orderBy('invoice_id', 'DESC')->get();

        foreach ($grouped_orders as $grouped_order) {
            $orders = OrderedItem::where('invoice_id', $grouped_order->invoice_id)->get();
            $order_details = [];
            $total_price = 0;

            foreach ($orders as $order) {
                $product = [
                    'product_id' => $order->product_id,
                    'name_product' => $order->product->name,
                    'qty' => $order->qty,
                    'price' => $order->product->price,
                    'total_price' => $order->price,
                    'status' => $order->status,
                    'image' => $order->product->image,
                ];

                $order_details[] = $product;
                $total_price += $order->total_price;
            }

            $alamat_detail = [
                'id' => $order->alamat->id,
                'name' => $order->alamat->name,
                'no_telpon' => $order->alamat->no_telpon,
                'alamat' => $order->alamat->alamat,
                'keterangan' => $order->alamat->keterangan,
                'catatan' => $order->alamat->catatan,
            ];

            $order_data = [
                'invoice_id' => $grouped_order->invoice_id,
                'invoice' => $grouped_order->invoice->invoice,
                'price' => $grouped_order->invoice->total_price,
                'statusPembayaran' => $grouped_order->invoice->status_pembayaran,
                'alamat' => $alamat_detail,
                'products' => $order_details,
                'author' => $grouped_order->invoice->author,
            ];

            $response['data'][] = $order_data;
        }

        return response()->json($response);
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

            $alamat_detail = [
                'id' => $order->alamat->id,
                'name' => $order->alamat->name,
                'no_telpon' => $order->alamat->no_telpon,
                'alamat' => $order->alamat->alamat,
                'keterangan' => $order->alamat->keterangan,
                'catatan' => $order->alamat->catatan,
            ];

            $order_data = [
                'invoice_id' => $grouped_order->invoice_id,
                'invoice' => $grouped_order->invoice->invoice,
                'price' => $grouped_order->invoice->total_price,
                'statusPembayaran' => $grouped_order->invoice->status_pembayaran,
                'alamat' => $alamat_detail,
                'products' => $order_details,
                'author' => $grouped_order->invoice->author,
            ];
        }

        return response()->json($order_data);
    }


    public function orderedItems(Request $request)
    {
        $orderedItem = OrderedItem::orderBy('id', 'DESC')->get();

        // if (Auth::user()->role_id == 2) {
        //     $query->where('author_id', Auth::user()->id);
        // }

        $response = [
            'status' => 'Success',
            'message' => 'List Data Order',
            'orders' => $orderedItem
        ];

        if ($orderedItem) {
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }

    public function cekOrder($id)
    {
        $order = OrderedItem::find($id);

        if ($order) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'Data Order',
                    'order' => $order
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }

    public function createOrder(Request $request)
    {
        function calculateTotalAmount($orderedItems)
        {
            $totalAmount = 0;

            foreach ($orderedItems as $orderedItem) {
                // Category::findOrFail($id)
                // $product = Product::findOrFail($orderedItem['product_id']);

                $product = Product::where('id', "=", $orderedItem['product_id'])->first();

                $totalAmount += $product->price * $orderedItem['qty'];
            }

            return $totalAmount;
        }

        $rules = [
            'product_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];

        $messages = [
            'required' => 'Data harus diisi.',
            'integer' => 'Data harus bernilai angka.',
            'numeric' => 'Data harus bernilai angka.',
            'min' => 'The :attribute field must be at least :min.',
            'string' => 'Data harus bernilai huruf.'
        ];

        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastInvoiceCode = $lastInvoice ? $lastInvoice->invoice : null;
        $lastInvoiceNumber = $lastInvoiceCode ? intval(substr($lastInvoiceCode, 4)) : 0;
        $nextInvoiceNumber = $lastInvoiceNumber + 1;
        $newInvoice = $nextInvoiceNumber;

        // $authorId =  Auth::user()->id;

        $validator = Validator::make($rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // $totalPrice = 0;


        $invoice = Invoice::create([
            'invoice' => 'INV-' . $newInvoice,
            // 'total_price' => 50000,
            'total_price' => calculateTotalAmount($request->input('ordered_items')),
        ]);

        // Store ordered items associated with the invoice

        foreach ($request->input('ordered_items') as $orderedItemData) {
            // $product = Product::findOrFail($orderedItemData['product_id']);

            $product = Product::where('id', $orderedItemData['product_id'])->first();
            OrderedItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $orderedItemData['product_id'],
                'qty' => $orderedItemData['qty'],
                'price' => $product->price * $orderedItemData['qty'],
            ]);
        }

        // Return the newly created invoice
        if ($invoice) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Berhasil Membuat Pesanan',
                'invoice' => $invoice,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Pesanan gagal ditambahkan",
            ], 400);
        }
    }
}
