<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDO;

class BackupController extends Controller
{
    public function index()
    {
        $lastBackup = null;
        $metaPath = 'backup_meta.json';

        if (Storage::exists($metaPath)) {
            try {
                $lastBackup = json_decode(Storage::get($metaPath), true);
            } catch (\Throwable $e) {
                $lastBackup = null;
            }
        }

        return view('backup.index', compact('lastBackup'));
    }

    public function download(Request $request)
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver !== 'mysql') {
            return back()->with('error', 'Database backup is only supported for MySQL connections.');
        }

        $host = config("database.connections.{$connection}.host", '127.0.0.1');
        $port = config("database.connections.{$connection}.port", 3306);
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = (string) config("database.connections.{$connection}.password");

        $filename = 'laras-flowershop-backup-' . now()->format('Y-m-d_His') . '.sql';

        try {
            $sql = $this->generateDump($host, $port, $database, $username, $password);

            Storage::put('backup_meta.json', json_encode([
                'last_backup_at' => now()->toDateTimeString(),
                'last_backup_by' => auth()->user()->full_name ?? 'Unknown',
                'filename' => $filename,
                'size_bytes' => strlen($sql),
            ], JSON_PRETTY_PRINT));

            return response()->streamDownload(function () use ($sql) {
                echo $sql;
            }, $filename, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate a full SQL dump of the database using PDO only.
     * No process spawning, no mysqldump binary required.
     */
    private function generateDump(string $host, string $port, string $database, string $username, string $password): string
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        $out = [];
        $out[] = "-- Lara's Flowershop — Database Backup";
        $out[] = "-- Generated: " . now()->toDateTimeString();
        $out[] = "-- Database: {$database}";
        $out[] = "-- Generator: PHP PDO (no mysqldump)";
        $out[] = "";
        $out[] = "SET FOREIGN_KEY_CHECKS=0;";
        $out[] = "SET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';";
        $out[] = "SET NAMES utf8mb4;";
        $out[] = "";

        // Get all tables
        $tables = $pdo->query('SHOW FULL TABLES')->fetchAll(PDO::FETCH_NUM);

        foreach ($tables as $row) {
            $tableName = $row[0];
            $tableType = $row[1]; // 'BASE TABLE' or 'VIEW'

            if ($tableType !== 'BASE TABLE') {
                continue; // skip views
            }

            $out[] = "--";
            $out[] = "-- Table structure for `{$tableName}`";
            $out[] = "--";
            $out[] = "DROP TABLE IF EXISTS `{$tableName}`;";

            $createResult = $pdo->query("SHOW CREATE TABLE `{$tableName}`")->fetch(PDO::FETCH_NUM);
            $out[] = $createResult[1] . ";";
            $out[] = "";

            $countResult = $pdo->query("SELECT COUNT(*) FROM `{$tableName}`")->fetch(PDO::FETCH_NUM);
            $rowCount = (int) $countResult[0];

            if ($rowCount > 0) {
                $out[] = "--";
                $out[] = "-- Data for `{$tableName}` ({$rowCount} rows)";
                $out[] = "--";

                $stmt = $pdo->query("SELECT * FROM `{$tableName}`");

                $batch = [];
                $batchSize = 100;
                $columns = null;

                while ($dataRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    // Capture the column list once, from the first actual row
                    if ($columns === null) {
                        $columns = '`' . implode('`, `', array_keys($dataRow)) . '`';
                    }

                    $values = array_map(function ($value) use ($pdo) {
                        if ($value === null) return 'NULL';
                        if (is_int($value) || is_float($value)) return $value;
                        return $pdo->quote((string) $value);
                    }, $dataRow);

                    $batch[] = '(' . implode(', ', $values) . ')';

                    if (count($batch) >= $batchSize) {
                        $out[] = "INSERT INTO `{$tableName}` ({$columns}) VALUES";
                        $out[] = implode(",\n", $batch) . ";";
                        $batch = [];
                    }
                }

                // Flush remaining rows
                if (!empty($batch) && $columns !== null) {
                    $out[] = "INSERT INTO `{$tableName}` ({$columns}) VALUES";
                    $out[] = implode(",\n", $batch) . ";";
                }

                $out[] = "";
            }
        }

        $out[] = "SET FOREIGN_KEY_CHECKS=1;";
        $out[] = "";
        $out[] = "-- End of backup";

        return implode("\n", $out);
    }
}
