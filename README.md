# botify-module.admin
Simple Admin module for Botify Telegram library.

## Install
After creating the `$bot` object, include the module.
```php
include __DIR__ . '/modules/botify.admin/module.php';
```

## Commands
`!ban <user_id>` - ban user
`!unban <user_id>` - unban user
`!id <increment_id>` - get `user_id` by id in database
`!list` - show banned users
