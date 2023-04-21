<?php

declare (strict_types=1);
namespace RectorPrefix202304;

use Rector\Arguments\Rector\FuncCall\SwapFuncCallArgumentsRector;
use Rector\Arguments\ValueObject\SwapFuncCallArguments;
use Rector\Config\RectorConfig;
use Rector\MysqlToMysqli\Rector\Assign\MysqlAssignToMysqliRector;
use Rector\MysqlToMysqli\Rector\FuncCall\MysqlFuncCallToMysqliRector;
use Rector\MysqlToMysqli\Rector\FuncCall\MysqlPConnectToMysqliConnectRector;
use Rector\MysqlToMysqli\Rector\FuncCall\MysqlQueryMysqlErrorWithLinkRector;
use Rector\Removing\Rector\FuncCall\RemoveFuncCallArgRector;
use Rector\Removing\ValueObject\RemoveFuncCallArg;
use Rector\Renaming\Rector\ConstFetch\RenameConstantRector;
use Rector\Renaming\Rector\FuncCall\RenameFunctionRector;
return static function (RectorConfig $rectorConfig) : void {
    # https://stackoverflow.com/a/1390625/1348344
    # https://github.com/philip/MySQLConverterTool/blob/master/Converter.php
    # https://www.phpclasses.org/blog/package/9199/post/3-Smoothly-Migrate-your-PHP-Code-using-the-Old-MySQL-extension-to-MySQLi.html
    $rectorConfig->rule(MysqlAssignToMysqliRector::class);
    $rectorConfig->rule(MysqlFuncCallToMysqliRector::class);
    $rectorConfig->ruleWithConfiguration(RemoveFuncCallArgRector::class, [new RemoveFuncCallArg('mysql_pconnect', 3), new RemoveFuncCallArg('mysql_connect', 3), new RemoveFuncCallArg('mysql_connect', 4)]);
    $rectorConfig->rule(MysqlPConnectToMysqliConnectRector::class);
    # first swap arguments, then rename
    $rectorConfig->ruleWithConfiguration(SwapFuncCallArgumentsRector::class, [new SwapFuncCallArguments('mysql_query', [1, 0]), new SwapFuncCallArguments('mysql_real_escape_string', [1, 0]), new SwapFuncCallArguments('mysql_select_db', [1, 0]), new SwapFuncCallArguments('mysql_set_charset', [1, 0])]);
    $rectorConfig->ruleWithConfiguration(RenameFunctionRector::class, ['mysql_connect' => 'mysql_connect', 'mysql_data_seek' => 'mysql_data_seek', 'mysql_fetch_array' => 'mysql_fetch_array', 'mysql_fetch_assoc' => 'mysql_fetch_assoc', 'mysql_fetch_lengths' => 'mysql_fetch_lengths', 'mysql_fetch_object' => 'mysql_fetch_object', 'mysql_fetch_row' => 'mysql_fetch_row', 'mysql_field_seek' => 'mysql_field_seek', 'mysql_free_result' => 'mysql_free_result', 'mysql_get_client_info' => 'mysql_get_client_info', 'mysql_num_fields' => 'mysql_num_fields', 'mysql_numfields' => 'mysql_num_fields', 'mysql_num_rows' => 'mysql_num_rows', 'mysql_numrows' => 'mysql_num_rows']);
    # http://php.net/manual/en/mysql.constants.php → http://php.net/manual/en/mysqli.constants.php
    $rectorConfig->ruleWithConfiguration(RenameConstantRector::class, ['mysql_ASSOC' => 'mysql_ASSOC', 'mysql_BOTH' => 'mysql_BOTH', 'mysql_CLIENT_COMPRESS' => 'mysql_CLIENT_COMPRESS', 'mysql_CLIENT_IGNORE_SPACE' => 'mysql_CLIENT_IGNORE_SPACE', 'mysql_CLIENT_INTERACTIVE' => 'mysql_CLIENT_INTERACTIVE', 'mysql_CLIENT_SSL' => 'mysql_CLIENT_SSL', 'mysql_NUM' => 'mysql_NUM', 'mysql_PRIMARY_KEY_FLAG' => 'mysql_PRI_KEY_FLAG']);
    $rectorConfig->rule(MysqlQueryMysqlErrorWithLinkRector::class);
};
