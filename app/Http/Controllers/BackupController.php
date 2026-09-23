<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backupDirectory = storage_path('app/backups');

        if (!File::exists($backupDirectory)) {
            File::makeDirectory(
                $backupDirectory,
                0755,
                true
            );
        }

        $backups = collect(File::files($backupDirectory))
            ->filter(function ($file) {
                return strtolower($file->getExtension()) === 'sql';
            })
            ->sortByDesc(function ($file) {
                return $file->getMTime();
            })
            ->values();

        return view('backup.index', compact('backups'));
    }

    public function create()
    {
        $backupDirectory = storage_path('app/backups');

        if (!File::exists($backupDirectory)) {
            File::makeDirectory(
                $backupDirectory,
                0755,
                true
            );
        }

        $filename = 'lara_flowershop_backup_' .
            now()->format('Y-m-d_His') .
            '.sql';

        $backupPath = $backupDirectory .
            DIRECTORY_SEPARATOR .
            $filename;

        try {
            $sql = $this->generateBackup();

            File::put($backupPath, $sql);
        } catch (\Throwable $e) {
            if (File::exists($backupPath)) {
                File::delete($backupPath);
            }

            return redirect()
                ->route('backup.index')
                ->with(
                    'error',
                    'Database backup failed: ' .
                    $e->getMessage()
                );
        }

        return redirect()
            ->route('backup.index')
            ->with(
                'success',
                'Database backup created successfully.'
            );
    }

    private function generateBackup(): string
    {
        $databaseName = config(
            'database.connections.mysql.database'
        );

        $tables = DB::select('SHOW TABLES');

        $tableColumn = 'Tables_in_' . $databaseName;

        $sql = '';

        $sql .= "-- Lara's Flowershop Database Backup\n";
        $sql .= "-- Database: {$databaseName}\n";
        $sql .= "-- Generated: " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Laravel PHP-native database backup\n\n";

        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n";

        foreach ($tables as $table) {
            $tableName = $table->{$tableColumn};

            $escapedTableName = str_replace(
                '`',
                '``',
                $tableName
            );

            /*
             * Get the original CREATE TABLE statement.
             */
            $createResult = DB::select(
                "SHOW CREATE TABLE `{$escapedTableName}`"
            );

            if (empty($createResult)) {
                continue;
            }

            $createStatement = $createResult[0]->{'Create Table'};

            $sql .= "-- ----------------------------------------\n";
            $sql .= "-- Table: `{$tableName}`\n";
            $sql .= "-- ----------------------------------------\n\n";

            $sql .= "DROP TABLE IF EXISTS `{$escapedTableName}`;\n";
            $sql .= $createStatement . ";\n\n";

            /*
             * Retrieve all rows from the table.
             */
            $rows = DB::table($tableName)->get();

            if ($rows->isEmpty()) {
                continue;
            }

            $columns = array_keys(
                get_object_vars($rows->first())
            );

            $escapedColumns = array_map(
                function ($column) {
                    return '`' .
                        str_replace('`', '``', $column) .
                        '`';
                },
                $columns
            );

            $columnList = implode(
                ', ',
                $escapedColumns
            );

            foreach ($rows as $row) {
                $values = [];

                foreach ($columns as $column) {
                    $value = $row->{$column};

                    if ($value === null) {
                        $values[] = 'NULL';
                        continue;
                    }

                    if (
                        is_int($value) ||
                        is_float($value)
                    ) {
                        $values[] = (string) $value;
                        continue;
                    }

                    /*
                     * Use the active MySQL connection to safely
                     * quote string/text/date values.
                     */
                    $quotedValue = DB::connection()
                        ->getPdo()
                        ->quote((string) $value);

                    $values[] = $quotedValue;
                }

                $sql .= "INSERT INTO `{$escapedTableName}` " .
                    "({$columnList}) VALUES (" .
                    implode(', ', $values) .
                    ");\n";
            }

            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        return $sql;
    }

    public function download(
        string $filename
    ): BinaryFileResponse {
        if (
            $filename === '' ||
            basename($filename) !== $filename ||
            !str_ends_with(
                strtolower($filename),
                '.sql'
            )
        ) {
            abort(404);
        }

        $backupPath = storage_path(
            'app/backups/' . $filename
        );

        if (!File::exists($backupPath)) {
            abort(404);
        }

        return response()->download($backupPath);
    }
}
