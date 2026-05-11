<?php

declare(strict_types=1);

/**
 * One-off: bootstrap app and GET /login. Run: php scripts/probe_login_request.php
 * Delete after debugging if desired.
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Http\Kernel $kernel */
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$request = \Illuminate\Http\Request::create('/login', 'GET');
$response = new \Illuminate\Http\Response();

try {
    $response = $kernel->handle($request);
    fwrite(STDOUT, 'Status: '.$response->getStatusCode().PHP_EOL);
    if ($response->getStatusCode() >= 500) {
        fwrite(STDOUT, (string) $response->getContent().PHP_EOL);
    }
} catch (\Throwable $e) {
    fwrite(STDERR, get_class($e).': '.$e->getMessage().PHP_EOL.$e->getTraceAsString().PHP_EOL);
    exit(1);
} finally {
    $kernel->terminate($request, $response);
}
