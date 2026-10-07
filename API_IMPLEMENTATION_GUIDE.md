# API Implementation Guide

## Route Setup
Create API routes in `routes/api.php` using versioned endpoints when possible.

Example:
```php
use App\Http\Controllers\Api\InstallationController;

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('installations', [InstallationController::class, 'store']);
    Route::post('installations/bulk', [InstallationController::class, 'bulkUpload']);
    Route::get('installations/{id}', [InstallationController::class, 'show']);
    Route::get('installations', [InstallationController::class, 'index']);
});
```

## Controller Actions
Use form requests for validation and return standardized JSON responses.

Example:
```php
public function store(InstallationRequest $request)
{
    $data = $request->validated();
    $installation = Installation::create($data);

    return response()->json([
        'status' => 'success',
        'message' => 'Installation recorded successfully',
        'data' => $installation,
    ], 201);
}
```

## Bulk Upload Handling
- Accept `multipart/form-data`
- Parse uploaded file using Maatwebsite/Excel
- Validate each row and collect errors
- Save valid rows in a transaction when possible
- Return success/failure summary with row-level details

## Error Handling
Return `422` for validation errors and `500` for unexpected failures.

Example:
```php
return response()->json([
    'status' => 'error',
    'message' => 'Validation failed',
    'errors' => $exception->errors(),
], 422);
```

## Webhooks
Support external integrations by implementing secure webhook endpoints and history logging.

Example:
```php
Route::post('webhooks/installation-completed', [WebhookController::class, 'installationCompleted']);
```

## API Versioning
- Use `/api/v1/` prefix
- Support future versions with `/api/v2/`
- Keep backward-compatible responses whenever possible
