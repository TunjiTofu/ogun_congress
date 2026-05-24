<?php

namespace App\Filament\Actions;

use App\Models\Church;
use App\Models\District;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportDistrictsChurchesAction extends Action
{
    public static function make(?string $name = 'import_districts_churches'): static
    {
        return parent::make($name)
            ->label('Import from Excel')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('info')
            ->modalHeading('Import Districts & Churches')
            ->modalDescription(
                'Upload the Excel file. Sheet 1 = Districts (id, name, zone). ' .
                'Sheet 2 = Churches (id, district_id, name, address). ' .
                'Existing records are updated. New records are inserted. Nothing is deleted.'
            )
            ->modalSubmitActionLabel('Import')
            ->modalWidth('lg')
            ->form([
                FileUpload::make('file')
                    ->label('Excel File (.xlsx)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->required()
                    ->helperText('Use: districts_and_churches_import.xlsx'),
            ])
            ->action(function (array $data): void {
                // Resolve path
                $uploaded = $data['file'];
                $path     = null;
                if (is_string($uploaded)) {
                    foreach ([
                                 Storage::disk('public')->path($uploaded),
                                 Storage::disk('local')->path($uploaded),
                                 storage_path('app/public/' . $uploaded),
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

                $spreadsheet = IOFactory::load($path);
                $distNew = $distUp = $chNew = $chUp = 0;
                $errors  = [];

                DB::beginTransaction();
                try {
                    // ── Districts: id | name | zone ───────────────────────
                    $sheet = $spreadsheet->getSheetByName('Districts') ?? $spreadsheet->getSheet(0);
                    foreach ($sheet->toArray() as $i => $row) {
                        if ($i === 0) continue; // header
                        [$id, $name, $zone] = array_pad($row, 3, null);

                        $name = trim((string) $name);
                        if (! $name) continue;

                        $exists = District::find((int) $id);
                        District::updateOrCreate(
                            ['id'   => (int) $id],
                            ['name' => $name, 'zone' => trim((string) $zone)]
                        );
                        $exists ? $distUp++ : $distNew++;
                    }

                    // ── Churches: id | district_id | name | address ───────
                    $sheet = $spreadsheet->getSheetByName('Churches') ?? $spreadsheet->getSheet(1);
                    foreach ($sheet->toArray() as $i => $row) {
                        if ($i === 0) continue;
                        [$id, $districtId, $name, $address] = array_pad($row, 4, null);

                        $name = trim((string) $name);
                        if (! $name) continue;

                        $districtId = (int) $districtId;
                        if (! District::where('id', $districtId)->exists()) {
                            $errors[] = "Row " . ($i + 1) . ": District ID {$districtId} not found for '{$name}'";
                            continue;
                        }

                        $exists = Church::find((int) $id);
                        Church::updateOrCreate(
                            ['id'          => (int) $id],
                            [
                                'district_id' => $districtId,
                                'name'        => $name,
                                'address'     => trim((string) $address),
                            ]
                        );
                        $exists ? $chUp++ : $chNew++;
                    }

                    DB::commit();

                } catch (\Throwable $e) {
                    DB::rollBack();
                    Notification::make()->title('Import failed: ' . $e->getMessage())->danger()->send();
                    return;
                }

                $body = "Districts: {$distNew} new, {$distUp} updated.\nChurches: {$chNew} new, {$chUp} updated.";
                if ($errors) {
                    $body .= "\n⚠ " . count($errors) . " skipped: " . implode('; ', array_slice($errors, 0, 3));
                }

                Notification::make()
                    ->title('Import Complete')
                    ->body($body)
                    ->color($errors ? 'warning' : 'success')
                    ->persistent()
                    ->send();
            });
    }
}
