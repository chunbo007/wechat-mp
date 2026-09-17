<?php
namespace app\exception;

use Webman\Exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * Class JsonBusinessException
 * @package plugin\saiadmin\app\exception
 */
class JsonBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
    }
}
