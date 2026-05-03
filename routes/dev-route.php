<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\StreamOutput;

/*
|--------------------------------------------------------------------------
| Dev Routes
|--------------------------------------------------------------------------
|
| Here is where you can register dev routes for your application.
| The routes will only be available on Local and Dev environments.
|
*/

Route::middleware('migration.key')->group(function () {
    Route::get('/queue', function () {
        $stream = fopen('php://output', 'w');

        Artisan::call('queue:work', [
            '--force' => true,
        ], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    Route::get('/migrate', function () {
        Log::info('Running migrations');
        $stream = fopen('php://output', 'w');
        Artisan::call('migrate', [
            '--force' => true,
        ], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    Route::get('/seed/{class}', function ($class) {
        // $class = EnrolledLessonTableSeeder
        $stream = fopen('php://output', 'w');
        Artisan::call('db:seed', [
            '--class' => $class,
            '--force' => true,
        ], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    Route::get('/schedule', function () {
        $stream = fopen('php://output', 'w');
        Artisan::call('schedule:run', [], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    Route::get('/migrate-fresh', function () {
        Log::info('Running migrations fresh');
        $stream = fopen('php://output', 'w');
        Artisan::call('migrate:fresh', [
            '--seed' => true,
            '--force' => true,
        ], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    Route::get('/storage', function () {
        $stream = fopen('php://output', 'w');
        Artisan::call('storage:link', [], new Symfony\Component\Console\Output\StreamOutput($stream));

        return Artisan::output();
    });

    // =========================================================================
    // DROP TABLE
    // =========================================================================

    Route::get('/drop-table/{table}', function ($table) {
        try {
            // Protected tables that should never be dropped
            $protectedTables = [
                'migrations',
                'users',
                'password_resets',
                'password_reset_tokens',
                'personal_access_tokens',
            ];

            // Check if table is protected
            if (in_array($table, $protectedTables)) {
                Log::warning("Attempt to drop protected table: {$table}");
                return response()->json([
                    'success' => false,
                    'message' => "Cannot drop protected table: {$table}",
                    'protected_tables' => $protectedTables,
                ], 403);
            }

            // Check if table exists
            if (!Schema::hasTable($table)) {
                Log::warning("Attempt to drop non-existent table: {$table}");
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Drop the table
            Schema::dropIfExists($table);

            Log::info("Successfully dropped table: {$table}");

            return response()->json([
                'success' => true,
                'message' => "Table '{$table}' dropped successfully",
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            Log::error("Error dropping table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to drop table: {$table}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // =========================================================================
    // TRUNCATE TABLE
    // =========================================================================

    Route::get('/truncate-table/{table}', function ($table) {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                Log::warning("Attempt to truncate non-existent table: {$table}");
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Truncate the table
            DB::table($table)->truncate();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::info("Successfully truncated table: {$table}");

            return response()->json([
                'success' => true,
                'message' => "Table '{$table}' truncated successfully",
                'table' => $table,
            ]);
        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            Log::error("Error truncating table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to truncate table: {$table}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // =========================================================================
    // LIST ALL TABLES
    // =========================================================================

    Route::get('/list-tables', function () {
        try {
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            $tableKey = "Tables_in_{$databaseName}";

            $tableList = array_map(function ($table) use ($tableKey) {
                return $table->$tableKey;
            }, $tables);

            return response()->json([
                'success' => true,
                'database' => $databaseName,
                'count' => count($tableList),
                'tables' => $tableList,
            ]);
        } catch (\Exception $e) {
            Log::error("Error listing tables: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to list tables',
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // =========================================================================
    // TABLE INFO
    // =========================================================================

    Route::get('/table-info/{table}', function ($table) {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Get columns
            $columns = DB::select("DESCRIBE {$table}");

            // Get row count
            $rowCount = DB::table($table)->count();

            return response()->json([
                'success' => true,
                'table' => $table,
                'row_count' => $rowCount,
                'columns' => $columns,
            ]);
        } catch (\Exception $e) {
            Log::error("Error getting table info for {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to get table info: {$table}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

    // =========================================================================
// VIEW TABLE ROWS (with pagination and search)
// =========================================================================

    Route::get('/view-table/{table}', function (Illuminate\Http\Request $request, $table) {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Get pagination parameters
            $perPage = (int)$request->get('per_page', 15);
            $page = (int)$request->get('page', 1);
            $perPage = min($perPage, 100); // Max 100 per page

            // Get search parameters
            $search = $request->get('search');
            $searchColumn = $request->get('search_column');

            // Get sort parameters
            $sortBy = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'desc');
            $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

            // Build query
            $query = DB::table($table);

            // Apply search if provided
            if ($search && $searchColumn) {
                $columns = Schema::getColumnListing($table);
                if (in_array($searchColumn, $columns)) {
                    $query->where($searchColumn, 'like', "%{$search}%");
                }
            }

            // Apply sorting (if column exists)
            $columns = Schema::getColumnListing($table);
            if (in_array($sortBy, $columns)) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Get total count
            $total = $query->count();

            // Get paginated data
            $data = $query->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Get column information
            $columnInfo = DB::select("DESCRIBE {$table}");

            return response()->json([
                'success' => true,
                'table' => $table,
                'columns' => $columnInfo,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($total / $perPage),
                    'from' => (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $total),
                ],
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            Log::error("Error viewing table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to view table: {$table}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

// =========================================================================
// GET SINGLE ROW
// =========================================================================

    Route::get('/view-row/{table}/{id}', function ($table, $id) {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Get the row
            $row = DB::table($table)->where('id', $id)->first();

            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => "Row not found: {$table} with id {$id}",
                ], 404);
            }

            // Get column information
            $columnInfo = DB::select("DESCRIBE {$table}");

            return response()->json([
                'success' => true,
                'table' => $table,
                'columns' => $columnInfo,
                'data' => $row,
            ]);
        } catch (\Exception $e) {
            Log::error("Error viewing row {$id} in table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to view row: {$table}#{$id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

// =========================================================================
// UPDATE ROW (specific columns)
// =========================================================================

    Route::post('/update-row/{table}/{id}', function (Illuminate\Http\Request $request, $table, $id) {
        try {
            // Protected tables that should never be edited via this route
            $protectedTables = [
                'migrations',
                'password_resets',
                'password_reset_tokens',
            ];

            // Check if table is protected
            if (in_array($table, $protectedTables)) {
                Log::warning("Attempt to update protected table: {$table}");
                return response()->json([
                    'success' => false,
                    'message' => "Cannot update protected table: {$table}",
                    'protected_tables' => $protectedTables,
                ], 403);
            }

            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Get all columns for validation
            $columns = Schema::getColumnListing($table);

            // Get update data from request
            $updateData = $request->except(['_token', '_method']);

            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided for update',
                ], 400);
            }

            // Filter only valid columns
            $validData = [];
            $invalidColumns = [];

            foreach ($updateData as $column => $value) {
                if (in_array($column, $columns)) {
                    // Convert string 'null' to actual null
                    if ($value === 'null' || $value === '') {
                        $validData[$column] = null;
                    } else {
                        $validData[$column] = $value;
                    }
                } else {
                    $invalidColumns[] = $column;
                }
            }

            if (empty($validData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid columns to update',
                    'invalid_columns' => $invalidColumns,
                    'valid_columns' => $columns,
                ], 400);
            }

            // Check if row exists
            $exists = DB::table($table)->where('id', $id)->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => "Row not found: {$table} with id {$id}",
                ], 404);
            }

            // Update the row
            $updated = DB::table($table)->where('id', $id)->update($validData);

            // Get updated row
            $row = DB::table($table)->where('id', $id)->first();

            Log::info("Updated row {$id} in table {$table}", [
                'table' => $table,
                'id' => $id,
                'columns_updated' => array_keys($validData),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Row updated successfully: {$table}#{$id}",
                'table' => $table,
                'id' => $id,
                'columns_updated' => array_keys($validData),
                'invalid_columns' => $invalidColumns,
                'data' => $row,
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating row {$id} in table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to update row: {$table}#{$id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

// =========================================================================
// DELETE ROW
// =========================================================================

    Route::delete('/delete-row/{table}/{id}', function ($table, $id) {
        try {
            // Protected tables that should never have rows deleted via this route
            $protectedTables = [
                'migrations',
//                'users', // Uncomment if you want to protect users table
            ];

            // Check if table is protected
            if (in_array($table, $protectedTables)) {
                Log::warning("Attempt to delete from protected table: {$table}");
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete from protected table: {$table}",
                    'protected_tables' => $protectedTables,
                ], 403);
            }

            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Check if row exists
            $row = DB::table($table)->where('id', $id)->first();

            if (!$row) {
                return response()->json([
                    'success' => false,
                    'message' => "Row not found: {$table} with id {$id}",
                ], 404);
            }

            // Delete the row
            DB::table($table)->where('id', $id)->delete();

            Log::info("Deleted row {$id} from table {$table}");

            return response()->json([
                'success' => true,
                'message' => "Row deleted successfully: {$table}#{$id}",
                'table' => $table,
                'id' => $id,
                'deleted_data' => $row,
            ]);
        } catch (\Exception $e) {
            Log::error("Error deleting row {$id} from table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to delete row: {$table}#{$id}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });

// =========================================================================
// BULK UPDATE ROWS
// =========================================================================

    Route::post('/bulk-update/{table}', function (Illuminate\Http\Request $request, $table) {
        try {
            // Check if table exists
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'success' => false,
                    'message' => "Table does not exist: {$table}",
                ], 404);
            }

            // Get data from request
            // Expected format: { "ids": [1,2,3], "data": {"column": "value"} }
            $ids = $request->input('ids', []);
            $updateData = $request->input('data', []);

            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No IDs provided for bulk update',
                ], 400);
            }

            if (empty($updateData) || !is_array($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided for bulk update',
                ], 400);
            }

            // Get valid columns
            $columns = Schema::getColumnListing($table);

            // Filter only valid columns
            $validData = [];
            foreach ($updateData as $column => $value) {
                if (in_array($column, $columns)) {
                    $validData[$column] = $value === 'null' || $value === '' ? null : $value;
                }
            }

            if (empty($validData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid columns to update',
                ], 400);
            }

            // Update rows
            $affected = DB::table($table)->whereIn('id', $ids)->update($validData);

            Log::info("Bulk updated {$affected} rows in table {$table}", [
                'table' => $table,
                'ids' => $ids,
                'columns_updated' => array_keys($validData),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Bulk update completed: {$affected} rows updated",
                'table' => $table,
                'rows_affected' => $affected,
                'columns_updated' => array_keys($validData),
            ]);
        } catch (\Exception $e) {
            Log::error("Error bulk updating table {$table}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => "Failed to bulk update: {$table}",
                'error' => $e->getMessage(),
            ], 500);
        }
    });
});
