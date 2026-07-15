<?php

declare(strict_types=1);

namespace App\Core;

use App\Helpers\Flash;
use ErrorException;
use RuntimeException;
use Throwable;

final class ErrorHandler
{
    private static array $config = [];
    private static bool $handling = false;

    public static function register(array $config): void
    {
        self::$config = $config;
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');

        set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        set_exception_handler([self::class, 'handle']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handle(Throwable $exception): void
    {
        if (self::$handling) {
            http_response_code(500);
            echo 'Ocurrio un error interno.';
            return;
        }
        self::$handling = true;
        self::log($exception);

        $isPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
        $status = $exception instanceof HttpException
            ? $exception->status()
            : ($isPost && $exception instanceof RuntimeException ? 400 : 500);

        if (
            $isPost
            && $exception instanceof RuntimeException
            && $status < 500
            && !headers_sent()
        ) {
            Flash::set('error', $exception->getMessage());
            header('Location: ' . self::safeReturnUrl());
            self::$handling = false;
            exit;
        }

        http_response_code($status);
        $messages = [
            400 => 'La solicitud contiene datos invalidos.',
            403 => 'No tiene permiso para realizar esta accion.',
            404 => 'La pagina solicitada no existe.',
            419 => 'La sesion del formulario expiro. Recargue la pagina e intentelo nuevamente.',
            500 => 'Ocurrio un error interno. Intente nuevamente mas tarde.',
        ];
        $message = $messages[$status] ?? $messages[500];

        if ((bool)(self::$config['debug'] ?? false)) {
            $message .= ' ' . $exception->getMessage();
        }

        self::render($status, $message);
        self::$handling = false;
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error === null || !in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
            return;
        }
        self::handle(new ErrorException(
            $error['message'],
            0,
            $error['type'],
            $error['file'],
            $error['line']
        ));
    }

    private static function log(Throwable $exception): void
    {
        $logFile = (string)(self::$config['log_file'] ?? dirname(__DIR__, 2) . '/storage/logs/app.log');
        $directory = dirname($logFile);
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }
        $requestId = substr(hash('sha256', uniqid('', true)), 0, 12);
        $line = sprintf(
            "[%s] [%s] %s %s: %s in %s:%d\n%s\n",
            date('c'),
            $requestId,
            $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            $_SERVER['REQUEST_URI'] ?? '-',
            $exception::class . ' - ' . $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        @error_log($line, 3, $logFile);
    }

    private static function safeReturnUrl(): string
    {
        $base = rtrim((string)(self::$config['base_url'] ?? ''), '/');
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $path = is_string($referer) ? parse_url($referer, PHP_URL_PATH) : null;
        if (is_string($path) && ($path === $base || str_starts_with($path, $base . '/'))) {
            return $path;
        }
        return $base . '/';
    }

    private static function render(int $status, string $message): void
    {
        $title = $status >= 500 ? 'Error interno' : 'No se pudo completar la solicitud';
        $base = htmlspecialchars((string)(self::$config['base_url'] ?? '/'), ENT_QUOTES, 'UTF-8');
        echo '<!doctype html><html lang="es"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>' . $status . ' - ' . $title . '</title></head><body>'
            . '<main style="max-width:680px;margin:10vh auto;font-family:system-ui;padding:24px">'
            . '<h1>' . $status . ' - ' . $title . '</h1><p>'
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
            . '</p><p><a href="' . $base . '/">Volver al inicio</a></p></main></body></html>';
    }
}
