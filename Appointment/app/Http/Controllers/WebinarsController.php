<?php

namespace App\Http\Controllers;

use App\Http\Models\Webinars;
use Illuminate\Http\Request;
use Validator;

class WebinarsController extends BaseController
{
     /**
     * Lista de la tabla webinar.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       
         $webinar = Webinars::get();
         return $this->sendResponse($webinar->toArray(), 'Webinar exit');
    }
}
