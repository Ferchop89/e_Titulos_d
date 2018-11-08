<?php

namespace App\Rules;
use App\Http\Controllers\Admin\WSController;
// use App\Models\Web_Service;


use Illuminate\Contracts\Validation\Rule;

class curpValido implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $valid = new WSController();
        $respuesta = $valid->ws_RENAPO($value);
        if(isset($respuesta->return))
        {
            $var = (array)simplexml_load_string(utf8_encode($respuesta->return))->attributes()->statusOper;
            if($var[0] == 'EXITOSO')
            {
               return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
      return (session()->has('errorWS') ? session('errorWS') : 'CURP no valido.');
    }
}
