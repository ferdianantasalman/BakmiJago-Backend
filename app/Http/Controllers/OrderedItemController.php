<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\OrderedItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrderedItemController extends Controller
{
    public function orderedItems(Request $request)
    {
        $orderedItem = OrderedItem::orderBy('id', 'DESC')->get();

        // if (Auth::user()->role_id == 2) {
        //     $query->where('author_id', Auth::user()->id);
        // }


        if ($orderedItem) {
            $response = [
                'status' => 'Success',
                'message' => 'List Data Order',
                'orders' => $orderedItem
            ];
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
        // return response()->json(['halo']);
    }

    public function orderedItemsByInvoice($id)
    {
        $orderedItem = OrderedItem::where("invoice_id", "=", $id)->orderBy('id', 'DESC')->get();

        // if (Auth::user()->role_id == 2) {
        //     $query->where('author_id', Auth::user()->id);
        // }


        if ($orderedItem) {
            $response = [
                'status' => 'Success',
                'message' => 'List Data Order',
                'orders' => $orderedItem
            ];
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

                // var_dump($product);
                $totalAmount += $product->price * $orderedItem['qty'];
            }

            return $totalAmount;
        }

        $rules = [
            'product_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
            // 'price' => 'required|numeric|min:0',
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
