<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {   
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!a!', null, 401);
        }

        $data = Product::with('user:id,name')
                        ->get();

        return response()->json([
           'isSuccess'   => true,
           'data'        => [
               'products'       => $data
             ],
           'message'     => ''
        ]);
    }

    public function show($id)
    {   
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!!', null, 401);
        }

        $product = $this->user()->products()->find($id);
        
        if (!$product) {
            return $this->errorJsonResponse('Sorry, product with id ' . $id . ' cannot be found!', null, 404);
        }

        return $this->successJsonResponse('', $product, 200);

    }

    public function store(Request $request)
    {   
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!!', null, 401);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
           return $this->errorJsonResponse('Given data was invalid!!', null, 422);
        }else{
            try {
                if ($request->expectsJson()) {
                    $product = new Product();
                    $product->name = $request->name;
                    $product->price = $request->price;
                    $product->quantity = $request->quantity;

                    if($this->user()->products()->save($product)){
                        return $this->successJsonResponse('Product Created Successfully!', $product, 200);
                    }

                    return $this->errorJsonResponse('Something went wrong!', null, 422);
                }
                return $this->errorJsonResponse('Requested data is not valid!!', null, 422);
              } catch (Throwable $e) {
                Log::info($e);
                return $this->errorJsonResponse('Something went wrong!', null, 422);
             }
        }
    }

    public function update(Request $request, $id)
    {   
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!!', null, 401);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|integer',
            'quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
           return $this->errorJsonResponse('Given data was invalid!!', null, 422);
        }else{
            try {
                if ($request->expectsJson()) {

                    $product = $this->user()->products()->find($id);

                    $updated = $product->fill($request->all())
                    ->save();

                    if($updated){
                        return $this->successJsonResponse('Product Updated Successfully!', $product, 200);
                    }

                    return $this->errorJsonResponse('Sorry, product could not be updated!', null, 422);
                }
                return $this->errorJsonResponse('Requested data is not valid!!', null, 422);
              } catch (Throwable $e) {
                Log::info($e);
                return $this->errorJsonResponse('Sorry, product with id ' . $id . ' cannot be found!', null, 404);
             }
        }
    }

    public function destroy($id)
    {   
        if( ! auth()->guard('api')->check() ){
            return $this->errorJsonResponse('You are not authorized!!', null, 401);
        }

        $product = $this->user()->products()->find($id);
    
        if (!$product) {
            return $this->errorJsonResponse('Sorry, product with id ' . $id . ' cannot be found!!', null, 404);
        }
        
        try {
            if ($product->delete()) {
                return $this->successJsonResponse('Product deleted Successfully!', '', 200);
            } else {
                return $this->errorJsonResponse('Sorry, product could not be updated!!', null, 422);
            }
        } catch (Throwable $th) {
            Log::info($th);
            return $this->errorJsonResponse('Sorry! something went wrong!!', null, 404);
        }
        
    }
}
