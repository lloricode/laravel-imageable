<?php
/**
 *
 * Created by PhpStorm.
 * User: Lloric Mayuga Garcia <lloricode@gmail.com>
 * Date: 10/31/18
 * Time: 2:22 PM
 */

namespace Lloricode\LaravelImageable\Exceptions;

/**
 * Class FileNotUniqueException
 *
 * @package Lloricode\LaravelImageable\Exceptions
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class FileNotUniqueException extends \Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response();
    }
}