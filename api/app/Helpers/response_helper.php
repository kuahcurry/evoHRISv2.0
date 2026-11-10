<?php
if (!function_exists('sendResponse')) {
    function sendResponse($code, $res = array(), $message = null)
    {
        $startTime = 0;
        $response = array(
            200 => "ok",
            202 => "accepted",
            203 => "setting not found",
            204 => "data not found",
            304 => "save failed",
            305 => "delete failed",
            308 => "data already exist",
            400 => "bad request",
            401 => "authentication required",
            403 => "authentication failed",
            404 => "endpoint not Exist",
            405 => "private fn call require (db and con) as params",
            406 => "not acceptable",
            422 => "validation failed",
            451 => "Unavailable module",
            500 => "Query error",
            501 => "Query error by rules",
            502 => "bad gateway",
            503 => "Database not available",
            505 => "https required"
        );
        $status = $response[$code] ?? 'unknown status';
        $endTime = microtime(true);
        $interval = round($endTime - $startTime, 2);
        $loadtime = "{$interval}s";
        if (is_array($res)) {
            $messages = count($res) > 0 ? array("code" => $code, "status" => $status, "loadtime" => $loadtime, "data" => $res, "rows" => count($res)) : array("code" => $code, "status" => $status, "loadtime" => $loadtime, "data" => [], "rows" => 0);
            if (isset($res['message'])) {
                $messages['message'] = $res['message'];
            }
        } else if (is_string($res)) {
            $messages = array("code" => $code, "status" => "{$status}, {$res}", "message" => $res, "loadtime" => $loadtime);
        } else {
            $messages = array("code" => $code, "status" => $status, "loadtime" => $loadtime, "data" => $res, "rows" => $res ? 1 : 0);
        }

        if (!empty($message)) {
            $messages['message'] = $message;
            if (empty($messages['status']) || $messages['status'] === $status) {
                $messages['status'] = "{$status}, {$message}";
            }
        } elseif (empty($messages['message'])) {
            $messages['message'] = $messages['status'] ?? $status;
        }

        $statusCode = (int)$code;
        if ($statusCode >= 100 && $statusCode <= 599) {
            if (function_exists('service')) {
                try {
                    $resp = service('response');
                    $resp->setStatusCode($statusCode);
                    $resp->setContentType('application/json');
                } catch (\Throwable $t) {}
            }
            if (!headers_sent()) {
                http_response_code($statusCode);
                header('Content-Type: application/json; charset=UTF-8');
            }
        }

        $isProduction = (defined('ENVIRONMENT') && ENVIRONMENT == 'production') ? true : false;
        if ($isProduction) {
            $is_array = is_array($res);
            if ($is_array) {
                return base64_encode(json_encode($messages));
            } else {
                return json_encode($messages);
            }
        } else {
            return json_encode($messages);
        }
    }
}
