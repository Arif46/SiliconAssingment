<?php

namespace App\Http\Controllers;

use App\Http\Validations\ProductValidation;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = new Product();

        if ($request->name) {
            $query = $query->where('name', $request->name);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Product List',
            'data' =>$list
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validationResult = ProductValidation::validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $requestAll= $request->all();
            $model = Product::create($requestAll);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $model
        ]);
    }

    /**
     * product  Update Method
     */
    public function update(Request $request, $id)
    {
        $validationResult = ProductValidation:: validate($request, $id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $model = Product::find($id);

        if (!$model) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {   
            $data= $request->all();
            $model->update($data);


        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $model
        ]);
    }

    /**
     * Product status Update method
     */
    public function toggleStatus($id)
    {
        $model = Product::find($id);

        if (!$model) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $model->status = $model->status == 1 ? 2 : 1;
        $model->update();

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $model
        ]);
    }

    /**
     * Product Delete Method
     */
    public function destroy($id)
    {
        $model = Product::find($id);

        if (!$model) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $model->delete();

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
