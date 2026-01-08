<?php

namespace App\Utilities;

use App\Jobs\Auth\CreateUser;
use App\Jobs\Common\CreateCompany;
use App\Utilities\Console;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Installer
{
    public static function checkServerRequirements()
    {
        $requirements = [];

        if (ini_get('safe_mode')) {
            $requirements[] = trans('install.requirements.disabled', ['feature' => 'Safe Mode']);
        }

        if (ini_get('register_globals')) {
            $requirements[] = trans('install.requirements.disabled', ['feature' => 'Register Globals']);
        }

        if (ini_get('magic_quotes_gpc')) {
            $requirements[] = trans('install.requirements.disabled', ['feature' => 'Magic Quotes']);
        }

        if (!ini_get('file_uploads')) {
            $requirements[] = trans('install.requirements.enabled', ['feature' => 'File Uploads']);
        }

        if (!function_exists('proc_open')) {
            $requirements[] = trans('install.requirements.enabled', ['feature' => 'proc_open']);
        }

        if (!function_exists('proc_close')) {
            $requirements[] = trans('install.requirements.enabled', ['feature' => 'proc_close']);
        }

        if (!class_exists('PDO')) {
            $requirements[] = trans('install.requirements.extension', ['extension' => 'MySQL PDO']);
        }

        foreach ([
            'bcmath', 'ctype', 'curl', 'dom', 'fileinfo',
            'intl', 'gd', 'json', 'mbstring',
            'openssl', 'tokenizer', 'xml', 'zip'
        ] as $ext) {
            if (!extension_loaded($ext)) {
                $requirements[] = trans('install.requirements.extension', ['extension' => strtoupper($ext)]);
            }
        }

        foreach ([
            'storage/app',
            'storage/app/uploads',
            'storage/framework',
            'storage/logs',
        ] as $dir) {
            if (!is_writable(base_path($dir))) {
                $requirements[] = trans('install.requirements.directory', ['directory' => $dir]);
            }
        }

        // ✅ FIX FINAL
        if (Console::run('help') !== true) {
            $requirements[] = trans(
                'install.error.php_version',
                ['php_version' => PHP_VERSION]
            );
        }

        return $requirements;
    }

    public static function createDefaultEnvFile()
    {
        if (!is_file(base_path('.env')) && is_file(base_path('.env.example'))) {
            File::move(base_path('.env.example'), base_path('.env'));
        }

        static::updateEnv([
            'APP_KEY' => 'base64:' . base64_encode(random_bytes(32)),
        ]);
    }

    public static function createDbTables($host, $port, $database, $username, $password, $prefix = null)
    {
        if (!static::isDbValid($host, $port, $database, $username, $password)) {
            return false;
        }

        static::saveDbVariables($host, $port, $database, $username, $password, $prefix);

        set_time_limit(300);

        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--class' => 'Database\\Seeds\\Permissions', '--force' => true]);

        return true;
    }

    public static function isDbValid($host, $port, $database, $username, $password)
    {
        Config::set('database.connections.install_test', [
            'driver'   => config('database.default', 'mysql'),
            'host'     => $host,
            'port'     => $port,
            'database' => $database,
            'username' => $username,
            'password' => $password,
            'charset'  => 'utf8mb4',
        ]);

        try {
            DB::connection('install_test')->getPdo();
        } catch (\Exception $e) {
            return false;
        }
        DB::purge('install_test');
        return true;
    }
    public static function saveDbVariables($host, $port, $database, $username, $password, $prefix = null)
    {
        $prefix = $prefix ?: strtolower(Str::random(3) . '_');

        static::updateEnv([
            'DB_HOST'     => $host,
            'DB_PORT'     => $port,
            'DB_DATABASE' => $database,
            'DB_USERNAME' => $username,
            'DB_PASSWORD' => '"' . $password . '"',
            'DB_PREFIX'   => $prefix,
        ]);

        $con = config('database.default', 'mysql');
        $db = Config::get("database.connections.$con");

        $db['host'] = $host;
        $db['database'] = $database;
        $db['username'] = $username;
        $db['password'] = $password;
        $db['prefix'] = $prefix;

        Config::set("database.connections.$con", $db);

        DB::purge($con);
        DB::reconnect($con);
    }

    public static function createCompany($name, $email, $locale)
    {
        dispatch_sync(new CreateCompany([
            'name' => $name,
            'domain' => '',
            'email' => $email,
            'currency' => 'USD',
            'locale' => $locale,
            'enabled' => '1',
        ]));
    }

    public static function createUser($email, $password, $locale)
    {
        dispatch_sync(new CreateUser([
            'name' => '',
            'email' => $email,
            'password' => $password,
            'locale' => $locale,
            'companies' => ['1'],
            'roles' => ['1'],
            'enabled' => '1',
        ]));
    }
    public static function finalTouches()
    {
        static::updateEnv([
            'APP_LOCALE' => session('locale'),
            'APP_INSTALLED' => 'true',
            'APP_DEBUG' => 'false',
        ]);
    }
    public static function updateEnv($data)
    {
        if (!is_file(base_path('.env'))) {
            return false;
        }
        $env = file(base_path('.env'), FILE_IGNORE_NEW_LINES);

        foreach ($data as $key => $value) {
            $found = false;

            foreach ($env as &$line) {
                if (str_starts_with($line, "$key=")) {
                    $line = "$key=$value";
                    $found = true;
                }
            }

            if (!$found) {
                $env[] = "$key=$value";
            }
        }
        file_put_contents(base_path('.env'), implode("\n", $env));
        return true;
    }
}
