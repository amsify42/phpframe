## Action
Action can be created manually in `app/Actions` directory or go to base path of sage repository and run this command from console
```
php pcli create action Sample 
```
This will create the action at `app/Actions/Sample.php` with code.
```php
<?php

namespace App\Actions;

use PHPattern\Action;

class Sample extends Action
{
        
}
```
You can keep adding methods to the action based on requirement or route pattern.