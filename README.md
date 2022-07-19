# Prerequisite Installation
1. Composer
2. Laravel
3. XAMPP (for development only)
4. Redis
5. Python 3

### update project
1. git pull
2. composer install
3. npm install
4. python3 lazy-people-auto.py (If DB table change)

### execute server
5. redis-server
6. laravel-echo-server start
7. php artisan queue:listen --tries=1
8. php artisan serve
