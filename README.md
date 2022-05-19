# nish2-sample
A sample project for Nish2

# See Nish2

[See Nish2 Framework.](https://github.com/Nish-Framework/nish2)

# Notes

After editing your settings via Configs/config_dev.php you may run this piece of code to create the sample DB.

```
php migrations/create_mysql_db.php
```

If you create users to check login form, passwords must be MD5 hashed. Of course, you may change this behaviour editing Sample\Sessions\RequestUser::login() method.