<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        // $image = "http://127.0.0.1:8000/public/product/" . $product->file('image');

        $order = Order::orderBy('id', 'DESC')->get();

        // $makanan = Order::where('category', '=', "makanan")->get();
        // $minuman = Order::where('category', '=', "minuman")->get();

        foreach ($order as $key => $val) {

            // $url = Storage::disk('public')->get($val->image);
            // $path = public_path($url);

            $order[$key]->image_url = asset('storage/' . $val->image);

            // $product[$key]->image_url = Storage::disk('public')->get($val->image);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'List data produk',
            'datas' => "$order",
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'code'     => 'required',
            'name'     => 'required',
            'price'     => 'required|integer',
            'qty'     => 'required|integer',
            'category'     => 'required',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'code'      => $request->code,
            'name'      => $request->name,
            'price'     => $request->price,
            'qty'     => $request->qty,
            'author_id'     => auth()->user()->id,
            'category'     => $request->category,
        ];

        // upload image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produk');
        }

        //create product
        $product = Order::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Produk berhasil ditambahkan',
                'data' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Produk gagal ditambahkan",
            ], 400);
        }
    }

    public function show($id)
    {
        // Show Data
        $product = Order::find($id);

        return response()->json([
            'status' => 'Success',
            'message' => 'Data produK ditemukan',
            'data' => $product,
        ]);
        // return new ProductResource(true, 'Data product Ditemukan!', $product);
    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'code'     => 'required',
            'name'     => 'required',
            'price'     => 'required|integer',
            'qty'     => 'required|integer',
            'category'     => 'required',
        ], [
            'code.required' => 'code harus diisi ngab',
            'name.required' => 'name harus diisi ngab',
            'price.required' => 'price harus diisi ngab',
            'qty.required' => 'qty harus diisi ngab',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $product = Order::find($id);

        $data = [
            'code'      => $request->code,
            'name'      => $request->name,
            'price'     => $request->price,
            'qty'     => $request->qty,
            'author_id'     => $product->author_id,
            'category'     => $request->category,
        ];

        if ($request->hasFile('image')) {
            if ($product->image == null) {
                $data['image'] = $request->file('image')->store('produk');
            } else {
                Storage::delete(Order::find($id)->image);
                $data['image'] = $request->file('image')->store('produk');
            }
        }

        $product->update($data);

        if ($product) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Produk berhasil diupdate',
                'product' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'product gagal diupdate',
            ], 400);
        }
    }

    public function destroy($id)
    {
        if (Order::find($id)->image != NULL) {
            Storage::delete(Order::find($id)->image);
        }

        $product = Order::find($id)->delete();

        if ($product) {
            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus',
                'product' => Order::find($id),
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Barang gagal dihapus',
            ], 400);
        }
    }
}
