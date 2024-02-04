<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        // $image = "http://127.0.0.1:8000/public/product/" . $product->file('image');

        $product = Product::orderBy('id', 'DESC')->get();

        $makanan = Product::where('category', '=', "makanan")->get();
        $minuman = Product::where('category', '=', "minuman")->get();
        foreach ($product as $key => $val) {

            // $url = Storage::disk('public')->get($val->image);
            // $path = public_path($url);

            $product[$key]->image_url = asset('storage/' . $val->image);

            // $product[$key]->image_url = Storage::disk('public')->get($val->image);
        }

        return response()->json([
            'status' => 'Success',
            'message' => 'List data produk',
            'makanan' => $makanan,
            'minuman' => $minuman,
            'datas' => $product,
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
        if ($image = $request->file("image")) {
            $destinationPath = "products/";
            $profileImage = date("YmdHis") . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $data["image"] = "$profileImage";
        }

        // if ($request->hasFile('image')) {
        //     $data['image'] = $request->file('image')->store('produk');
        // }

        //create product
        $product = Product::create($data);

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
        $product = Product::find($id);

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

        $product = Product::find($id);

        $data = [
            'code'      => $request->code,
            'name'      => $request->name,
            'price'     => $request->price,
            'qty'     => $request->qty,
            'author_id'     => $product->author_id,
            'category'     => $request->category,
        ];

        // Update Image
        if ($image = $request->file("image")) {
            // remove old file
            $path = "products/";

            if ($product->image != ''  && $product->image != null) {
                $file_old = $path . $product->image;
                unlink($file_old);
            }

            // upload new file
            $destinationPath = "products/";
            $profileImage = date("YmdHis") . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $data["image"] = "$profileImage";
        } else {
            unset($data["image"]);
        }

        // if ($request->hasFile('image')) {
        //     if ($product->image == null) {
        //         $data['image'] = $request->file('image')->store('produk');
        //     } else {
        //         Storage::delete(Product::find($id)->image);
        //         $data['image'] = $request->file('image')->store('produk');
        //     }
        // }

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
                'message' => 'Produk gagal diupdate',
            ], 400);
        }
    }

    public function destroy($id)
    {
        if (Product::find($id)->image != NULL) {
            Storage::delete(Product::find($id)->image);
        }

        $product = Product::find($id)->delete();

        if ($product) {
            return response()->json([
                'status' => 'success',
                'message' => 'Barang berhasil dihapus',
                'product' => Product::find($id),
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'Produk gagal dihapus',
            ], 400);
        }
    }
}
