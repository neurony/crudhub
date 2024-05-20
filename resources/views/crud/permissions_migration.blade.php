@php echo "<?php" @endphp

/** Generated with Crudhub */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use Spatie\Permission\Contracts\Permission;

return new class extends Migration
{
    /**
     * @var array|string[][]
     */
    protected array $permissions = [
@foreach($adminPermissions as $group => $name)
        [
            'name' => '{{ $name }}',
            'guard_name' => 'admin',
        ],
@endforeach
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $model = App::make(Permission::class);

        foreach ($this->permissions as $permission) {
            if ($model->whereName($permission['name'])->exists()) {
                continue;
            }

            $model->create($permission);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $model = App::make(Permission::class);

        foreach ($this->permissions as $permission) {
            $model->where($permission)->delete();
        }
    }
};
