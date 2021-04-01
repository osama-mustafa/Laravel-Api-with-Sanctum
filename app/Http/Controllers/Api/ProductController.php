<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::orderBy('created_at', 'DESC')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|min:3|max:255',
            'slug'          => 'required|string|min:3|max:255',
            'price'         => 'numeric',
            'description'   => 'required|min:5',
        ]);

        $product             = Product::create([
            'name'           => $validatedData['name'],
            'slug'           => $validatedData['slug'],
            'price'          => $validatedData['price'],
            'description'    => $validatedData['description']
        ]);

        return response()->json([
            'message' => 'success',
            'product' => $product
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (isset($product)) {
                return response()->json([
                    'status' => 'success',
                    'product' => $product
                ], 200); 
        
            } else {

                return response()->json([
                    'status' => 'Failed',
                    'message' => 'Product is not found!'
                ], 404);
            }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([

            'name'          => 'required|string|min:3|max:255',
            'slug'          => 'required|string|min:3|max:255',
            'price'         => 'numeric',
            'description'   => 'required|min:5',
        ]);

        $product = Product::find($id);
        if (!isset($product)) {

            return response()->json([
                'status' => 'Failed',
                'message' => 'Product is not found'
            ], 404);

        } 

        $product->name = $validatedData['name'];
        $product->slug = $validatedData['slug'];
        $product->price = $validatedData['price'];
        $product->description = $validatedData['description'];

        $product->save();
        return response()->json([
            'message' => 'success',
            'data'   => $product
        ], 200);
   
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $product = Product::find($id);

        if (!isset($product)) {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Product is not found'
            ], 404);
        }

        $product->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Product has been deleted'
        ], 200);
    }
}
