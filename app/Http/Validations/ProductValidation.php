<?php
namespace App\Http\Validations;

use Validator;

class ProductValidation 
{
  /**
   * Product Validation
   */
  public static function validate($request, $id = 0)
  {
    $validator = Validator::make($request->all(), [
        'name'  => 'required|string|max:255',
        'code'  => 'required|integer',
        'price' => 'required|integer'
    ]);

    if ($validator->fails()) {
        return([
            'success' => false,
            'errors'  => $validator->errors()
        ]);
    }

    return ['success'=>true];
  }
}