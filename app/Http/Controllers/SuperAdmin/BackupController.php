<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:superadmin');
    }

    public function index()
    {
        $backups = $this->getExistingBackups();

        return view('superadmin.backup.index', compact('backups'));
    }

    public function create()
    {
        $date = date('Y-m-d_H-i-s');
        $backupDir = storage_path('app/backups/backup-' . $date);

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $databases = $this->getDatabases();
        $dumped = [];

        foreach ($databases as $dbName) {
            $file = $backupDir . '/' . $dbName . '.sql';
            $this->dumpDatabase($dbName, $file);

            if (File::exists($file) && File::size($file) > 0) {
                $dumped[] = $dbName;
            }
        }

        if (empty($dumped)) {
            return redirect()->back()->with('error', 'No databases were backed up. Check mysqldump availability.');
        }

        // Create zip
        $zipFile = storage_path('app/backups/database-' . $date . '.zip');
        $zip = new ZipArchive();

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $zip->addFile($file->getPathname(), $file->getFilename());
            }
            $zip->close();
        }

        // Cleanup sql files directory
        File::deleteDirectory($backupDir);

        if (!File::exists($zipFile)) {
            return redirect()->back()->with('error', 'Failed to create backup zip file.');
        }

        $size = $this->formatSize(File::size($zipFile));

        return redirect()->back()->with('message', "Backup created successfully! File: database-{$date}.zip ({$size})");
    }

    public function download($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function email(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'filename' => 'required|string',
        ]);

        $path = storage_path('app/backups/' . $request->filename);

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found.');
        }

        $to = $request->email;
        $date = date('d M Y, h:i A');
        $size = $this->formatSize(File::size($path));
        $filename = $request->filename;

        try {
            \Illuminate\Support\Facades\Mail::raw("Database backup attached.", function ($message) use ($to, $path, $filename, $date, $size) {
                $message->to($to)
                    ->subject("EduPilot - School Management System — Database Backup ({$date})")
                    ->attach($path, [
                        'as' => $filename,
                        'mime' => 'application/zip',
                    ]);
            });

            return redirect()->back()->with('message', "Backup emailed successfully to {$to}");
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function delete($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
        }

        return redirect()->back()->with('message', 'Backup deleted.');
    }

    private function getDatabases(): array
    {
        $databases = [config('database.connections.mysql.database')];

        foreach (Tenant::all() as $tenant) {
            $databases[] = $tenant->id;
        }

        return $databases;
    }

    private function dumpDatabase(string $database, string $filePath): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');

        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            \Illuminate\Support\Facades\Log::warning("mysqldump failed for {$database}", [
                'output' => $output,
                'return_code' => $returnCode,
            ]);
        }
    }

    private function getExistingBackups(): array
    {
        $backupDir = storage_path('app/backups');

        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
            return [];
        }

        $files = collect(File::files($backupDir))
            ->filter(fn($f) => $f->getExtension() === 'zip')
            ->sortByDesc('filename')
            ->map(fn($f) => [
                'name' => $f->getFilename(),
                'size' => $this->formatSize($f->getSize()),
                'created' => date('d M Y, h:i A', $f->getMTime()),
            ])
            ->values()
            ->toArray();

        return $files;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }
}
