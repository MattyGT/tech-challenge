<?php
namespace App\Http\Services;

/**
 * Base class for returning API responses
 * @author Matthew Tyler
*/

class ApiResponse {
    
    private $request;

    public function __construct(ApiRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Convert response into array
    */
    public static function format(string $response):array
    {
        return explode("\r\n\r\n", $response, 2);
    }

}