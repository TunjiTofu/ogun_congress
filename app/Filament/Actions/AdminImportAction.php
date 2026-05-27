<?php

namespace App\Filament\Actions;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Church;
use App\Models\District;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;

class AdminImportAction extends Action
{
    public static function make(?string $name = 'import_admins'): static
    {
        return parent::make($name)
            ->label('Import from Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->modalHeading('Bulk Import Admin Users')
            ->modalDescription('Upload an Excel file (.xlsx) with admin user data. Existing emails are skipped.')
            ->modalSubmitActionLabel('Import Users')
            ->form([
                FileUpload::make('file')
                    ->label('Excel File (.xlsx)')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->required()
                    ->helperText('Download the sample template from the Users list page.'),
            ])
            ->action(function (array $data): void {
                $uploaded = $data['file'];

                // Resolve file path (Filament stores on public disk)
                $path = null;
                if (is_string($uploaded)) {
                    foreach ([
                                 Storage::disk('public')->path($uploaded),
                                 Storage::disk('local')->path($uploaded),
                             ] as $candidate) {
                        if (file_exists($candidate)) { $path = $candidate; break; }
                    }
                } elseif (is_object($uploaded) && method_exists($uploaded, 'getRealPath')) {
                    $path = $uploaded->getRealPath();
                }

                if (! $path || ! file_exists($path)) {
                    Notification::make()->title('File not found. Please try again.')->danger()->send();
                    return;
                }

                $validRoles = ['super_admin','accountant','secretariat','security',
                    'church_coordinator','camp_director','district_coordinator'];

                $spreadsheet = IOFactory::load($path);
                $sheet       = $spreadsheet->getActiveSheet();
                $rows        = $sheet->toArray();

                $imported = 0;
                $skipped  = [];
                $errors   = [];

                // Row 1 = headers, Row 2 = notes → start from row index 2 (3rd row)
                foreach (array_slice($rows, 2) as $lineIndex => $row) {
                    $lineNo = $lineIndex + 3;

                    $name        = trim($row[0] ?? '');
                    $email       = strtolower(trim($row[1] ?? ''));
                    $role        = strtolower(trim($row[2] ?? ''));
                    $churchName  = trim($row[3] ?? '');
                    $districtName= trim($row[4] ?? '');

                    // Look up IDs by name (case-insensitive)
                    $churchId   = $churchName
                        ? Church::whereRaw('LOWER(name) = ?', [strtolower($churchName)])->value('id')
                        : null;
                    $districtId = $districtName
                        ? District::whereRaw('LOWER(name) = ?', [strtolower($districtName)])->value('id')
                        : null;

                    if (! $name || ! $email || ! $role) continue;

                    if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Row {$lineNo}: Invalid email '{$email}'";
                        continue;
                    }

                    if (! in_array($role, $validRoles)) {
                        $errors[] = "Row {$lineNo}: Invalid role '{$role}'";
                        continue;
                    }

                    if (User::where('email', $email)->exists()) {
                        $skipped[] = $email;
                        continue;
                    }

                    $user = User::create([
                        'name'        => $name,
                        'email'       => $email,
                        'password'    => Hash::make('P@ssword.123'),
                        'church_id'   => $churchId,
                        'district_id' => $districtId,
                    ]);

                    $roleModel = Role::findByName($role, 'web');
                    $user->assignRole($roleModel);
                    $imported++;
                }

                $message = "✅ {$imported} admin(s) imported.";
                if ($skipped)  $message .= " ⏭ " . count($skipped) . " skipped (email exists).";
                if ($errors)   $message .= " ❌ " . count($errors) . " error(s): " . implode('; ', array_slice($errors, 0, 3));

                Notification::make()
                    ->title($message)
                    ->color($errors ? 'warning' : 'success')
                    ->send();
            });
    }
}
