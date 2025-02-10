<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            // Get action
            $action = request()->route()->getActionName();

            $handler->setFormatter(new LineFormatter(
                "[%datetime%] " . $action . ".%level_name%: %message% [" . request()->header('X-Request-ID') . "] %context%\n"
            ));
        }
    }
}
