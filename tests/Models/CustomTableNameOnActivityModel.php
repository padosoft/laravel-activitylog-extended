<?php

namespace Padosoft\Laravel\ActivitylogExtended\Test\Models;

use Padosoft\Laravel\ActivitylogExtended\Models\Activity;

class CustomTableNameOnActivityModel extends Activity
{
    protected $table = 'custom_table_name';
}
