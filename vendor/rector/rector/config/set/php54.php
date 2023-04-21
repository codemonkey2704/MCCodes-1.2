<?php

declare (strict_types=1);
namespace RectorPrefix202304;

use Rector\Config\RectorConfig;
use Rector\Php54\Rector\Array_\LongArrayToShortArrayRector;
use Rector\Php54\Rector\Break_\RemoveZeroBreakContinueRector;
use Rector\Php54\Rector\FuncCall\RemoveReferenceFromCallRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
return static function (RectorConfig $rectorConfig) : void {
    $rectorConfig->rule(LongArrayToShortArrayRector::class);
    $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, ['mysql_param_count' => 'mysql_stmt_param_count']);
    $rectorConfig->rule(RemoveReferenceFromCallRector::class);
    $rectorConfig->rule(RemoveZeroBreakContinueRector::class);
};
