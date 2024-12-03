<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

class ServeCommand extends BaseServeCommand
{
    /**
     * Get the date from the given PHP server output.
     *
     * @param  string  $line
     * @return \Illuminate\Support\Carbon
     */
    protected function getDateFromLine($line)
    {
        try {
            $regex = ! windows_os() && env('PHP_CLI_SERVER_WORKERS', 1) > 1
                ? '/^\[\d+]\s\[([^\]]+)\]/'
                : '/\[([^\]]+)\]/';

            $line = str_replace('  ', ' ', $line);

            if (preg_match($regex, $line, $matches)) {
                // Jika format sesuai dengan yang diharapkan
                if (isset($matches[1]) && strtotime($matches[1])) {
                    return Carbon::createFromFormat('D M d H:i:s Y', $matches[1]);
                }
            }

            // Jika format tidak sesuai, gunakan waktu saat ini
            return Carbon::now();

        } catch (\Exception $e) {
            // Jika terjadi error, gunakan waktu saat ini
            return Carbon::now();
        }
    }

    /**
     * Get the request port from the given PHP server output.
     *
     * @param  string  $line
     * @return int
     */
    protected function getRequestPortFromLine($line)
    {
        try {
            if (preg_match('/:(\d+)\s(?:(?:\w+$)|(?:\[.*))/', $line, $matches)) {
                return (int) $matches[1];
            }
            
            // Jika tidak menemukan port, gunakan port default
            return $this->port();
            
        } catch (\Exception $e) {
            return $this->port();
        }
    }

    /**
     * Returns a "callable" to handle the process output.
     *
     * @return callable(string, string): void
     */
    protected function handleProcessOutput()
    {
        return function ($type, $buffer) {
            try {
                $this->outputBuffer .= $buffer;
                $this->flushOutputBuffer();
            } catch (\Exception $e) {
                // Log error tapi jangan hentikan server
                \Log::error('Server output error: ' . $e->getMessage());
            }
        };
    }

    /**
     * Flush the output buffer.
     *
     * @return void
     */
    protected function flushOutputBuffer()
    {
        try {
            parent::flushOutputBuffer();
        } catch (\Exception $e) {
            // Log error tapi jangan hentikan server
            \Log::error('Buffer flush error: ' . $e->getMessage());
            $this->outputBuffer = '';
        }
    }
} 