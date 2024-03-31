<?php

namespace App\Logging;

use Illuminate\Support\Arr;
use Monolog\Handler\RotatingFileHandler;
use Monolog\LogRecord;
use Nwidart\Modules\Facades\Module;

class ModuleHandler extends RotatingFileHandler
{
    protected function write(LogRecord $record): void
    {
        $this->changeUrl($record);
        parent::write($record);
    }

    protected function changeUrl(LogRecord $record)
    {
        $classes = Arr::pluck($record['context']['exception']->getTrace(), 'class');

        foreach ($classes as $class) {
            $paths = explode('\\', $class);

            if (isset($paths[1]) && Module::has($paths[1])) {
                $module = Module::find($paths[1]);
                $this->url = str_replace(storage_path(), $module->getPath() . '/storage', $this->url);

                break;
            }
        }
    }
}
