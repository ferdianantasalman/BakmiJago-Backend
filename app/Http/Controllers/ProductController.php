<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::get();

        return response()->json([
            'status' => 'Success',
            'message' => 'List data produk',
            'product' => $product,
        ]);

        //return collection of produ$product as a resource
        // return new ProductResource(true, 'List Data Produk', $product);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'code'     => 'required',
            'name'     => 'required',
            'price'     => 'required|integer',
            'stock'     => 'required|integer',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/product', $image->hashName());

        //create product
        $product = Product::create([
            'image'     => $image->hashName(),
            'code'      => $request->code,
            'name'      => $request->name,
            'price'     => $request->price,
            'stock'     => $request->stock,
        ]);

        //return response
        return response()->json([
            'status' => 'Success',
            'message' => 'Produk berhasil ditambahkan',
            'product' => $product,
        ]);
        // return new ProductResource(true, 'Data product Berhasil Ditambahkan!', $product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Show Data
        return response()->json([
            'status' => 'Success',
            'message' => 'Data produK ditemukan',
            'product' => $product,
        ]); 
        // return new ProductResource(true, 'Data product Ditemukan!', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'code'     => 'required',
            'name'     => 'required',
            'price'     => 'required|integer',
            'stock'     => 'required|integer',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if image is not empty
        if ($request->hasFile('image')) {

            //upload image
            $image = $request->file('image');
            $image->storeAs('public/product', $image->hashName());

            //delete old image
            Storage::delete('public/product/'.$product->image);

            //update product with new image
            $product->update([
                'image'     => $image->hashName(),
                'code'      => $request->code,
                'name'      => $request->name,
                'price'     => $request->price,
                'stock'     => $request->stock,
            ]);

        } else {

            //update product without image
            $product->update([
                'code'      => $request->code,
                'name'      => $request->name,
                'price'     => $request->price,
                'stock'     => $request->stock,
            ]);
        }

        //return response
        return response()->json([
            'status' => 'Success',
            'message' => 'Data produK berhasil diubah',
            'product' => $product,
        ]);
        // return new ProductResource(true, 'Data product Berhasil Diubah!', $product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Storage::delete('public/product/'.$product->image);

        //delete post
        $product->delete();

        //return response
        return response()->json([
            'status' => 'Success',
            'message' => 'Data produk berhasil dihapus',
        ]);
        // return new ProductResource(true, 'Data produk berhasil dihapus1', null);
    }
}
