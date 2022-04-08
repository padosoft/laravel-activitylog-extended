<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Padosoft\Laravel\ActivitylogExtended\Models\Activity;

class CustomDatabaseConnectionOnActivityModel extends Activity
{
    protected $connection = 'custom_connection_name';
}
