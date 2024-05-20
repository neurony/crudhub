Route::prefix(config('crudhub.admin.prefix', 'admin'))->middleware([
    'crudhub.inertia.handle_requests',
])->group(function () {
    Route::prefix('{{ $urlPrefix }}')->middleware([
        'auth:admin',
    ])->group(function () {
        Route::get('', [{{ $controllerFqn }}, 'index'])->name('{{ $routeNames['index'] }}')->middleware('permission:{{ $adminPermissions['list'] }}');
        Route::get('create', [{{ $controllerFqn }}, 'create'])->name('{{ $routeNames['create'] }}')->middleware('permission:{{ $adminPermissions['add'] }}');
        Route::post('', [{{ $controllerFqn }}, 'store'])->name('{{ $routeNames['store'] }}')->middleware('permission:{{ $adminPermissions['add'] }}');
        Route::get('{{ $routeBinding }}/edit', [{{ $controllerFqn }}, 'edit'])->name('{{ $routeNames['edit'] }}')->middleware('permission:{{ $adminPermissions['edit'] }}');
        Route::put('{{ $routeBinding }}', [{{ $controllerFqn }}, 'update'])->name('{{ $routeNames['update'] }}')->middleware('permission:{{ $adminPermissions['edit'] }}');
        Route::delete('{{ $routeBinding }}', [{{ $controllerFqn }}, 'destroy'])->name('{{ $routeNames['destroy'] }}')->middleware('permission:{{ $adminPermissions['delete'] }}');
@if($withSoftDelete)
        Route::delete('{id}/force-destroy', [{{ $controllerFqn }}, 'forceDestroy'])->name('{{ $routeNames['force_destroy'] }}')->middleware('permission:{{ $adminPermissions['delete'] }}');
        Route::patch('{id}/restore', [{{ $controllerFqn }}, 'restore'])->name('{{ $routeNames['restore'] }}')->middleware('permission:{{ $adminPermissions['edit'] }}');
@endif
        Route::post('bulk-destroy', [{{ $controllerFqn }}, 'bulkDestroy'])->name('{{ $routeNames['bulk_destroy'] }}')->middleware('permission:{{ $adminPermissions['delete'] }}');
        Route::patch('{id}', [{{ $controllerFqn }}, 'partialUpdate'])->name('{{ $routeNames['partial_update'] }}')->middleware('permission:{{ $adminPermissions['edit'] }}');
@if($withReorderable)
        Route::post('reorder', [{{ $controllerFqn }}, 'reorder'])->name('{{ $routeNames['reorder'] }}')->middleware('permission:{{ $adminPermissions['edit'] }}');
@endif
    });
});
