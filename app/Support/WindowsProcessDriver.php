<?php

namespace App\Support;

use Closure;
use Illuminate\Concurrency\ProcessDriver;
use Illuminate\Support\Arr;
use Illuminate\Support\Defer\DeferredCallback;
use Illuminate\Support\Facades\Context;
use Laravel\SerializableClosure\SerializableClosure;
use Symfony\Component\Process\PhpExecutableFinder;

use function Illuminate\Support\defer;

class WindowsProcessDriver extends ProcessDriver
{
    public function defer(Closure|array $tasks): DeferredCallback
    {
        return defer(function () use ($tasks) {
            foreach (Arr::wrap($tasks) as $task) {
                $quote = fn (string $value) => "'".str_replace("'", "''", $value)."'";
                $arguments = '"'.base_path('artisan').'" invoke-serialized-closure';
                $script = '$ErrorActionPreference = \'Stop\'; Start-Process -WindowStyle Hidden -FilePath '
                    .$quote((new PhpExecutableFinder)->find(false) ?: PHP_BINARY).' -WorkingDirectory '.$quote(base_path())
                    .' -ArgumentList '.$quote($arguments);

                // Symfony filters the HTTP SAPI environment. PowerShell also
                // needs the Windows profile/system paths to initialize .NET.
                $environment = [];
                foreach (['SystemRoot', 'WINDIR', 'USERPROFILE', 'APPDATA', 'LOCALAPPDATA', 'ProgramData', 'TEMP', 'TMP', 'PATH', 'COMSPEC', 'PSModulePath'] as $name) {
                    if (($value = getenv($name)) !== false) {
                        $environment[$name] = $value;
                    }
                }

                $result = $this->processFactory->path(base_path())->env($environment + [
                    '__LARAVEL_CONTEXT' => json_encode(Context::dehydrate()),
                    'LARAVEL_INVOKABLE_CLOSURE' => base64_encode(serialize(new SerializableClosure($task))),
                ])->run(['powershell.exe', '-NoProfile', '-NonInteractive', '-EncodedCommand',
                    base64_encode(mb_convert_encoding($script, 'UTF-16LE', 'UTF-8')),
                ]);

                $result->throw();
            }
        });
    }
}
