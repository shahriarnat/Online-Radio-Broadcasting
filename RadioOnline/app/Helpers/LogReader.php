<?php

namespace App\Helpers;


class LogReader
{

    public static function readLatestLogFromChannel(string $channel, int $line): array
    {
        // Define the log directory based on the channel
        $logDirectory = storage_path('logs/' . $channel);

        // Check if the log directory exists
        if (!is_dir($logDirectory)) {
            throw new \Exception("Log directory for channel '{$channel}' does not exist.");
        }

        // Get all files in the log directory, sorted by modification time (descending)
        $logFiles = array_diff(scandir($logDirectory, SCANDIR_SORT_DESCENDING), ['.', '..']);

        // Filter out non-log files (assuming log files have a .log extension)
        if (empty($logFiles)) {
            throw new \Exception("No log files found for channel '{$channel}'.");
        }

        // Get the most recent log file
        $latestLogFile = $logDirectory . '/' . $logFiles[0];

        // Check if the latest log file is readable
        if (!is_readable($latestLogFile)) {
            throw new \Exception("Cannot read the latest log file for channel '{$channel}'.");
        }

        // Initialize an array to hold the last 10 lines
        $lines = [];
        // Open the file and read the last 10 lines
        $file = new \SplFileObject($latestLogFile, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();

        // Calculate the starting line to read the last 10 lines
        $startLine = max(0, $lastLine - $line);

        // Read the lines from the file
        for ($i = $startLine; $i <= $lastLine; $i++) {
            $file->seek($i);
            $lines[] = $file->current();
        }

        return array_filter($lines);
    }
}
