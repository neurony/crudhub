@php echo "<?php" @endphp

/** Generated with Crudhub */

namespace {{ $classNamespace }};

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
@foreach($importRelations as $fqn => $relation)
use {{ $fqn }};
@endforeach
@if($usesSoftDelete)
use Illuminate\Database\Eloquent\SoftDeletes;
@endif
@if($withTranslatable)
use Spatie\Translatable\HasTranslations;
@endif
@if(count($mediaCollections))
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Zbiller\Crudhub\Traits\AutoSavesMediaFiles;
use Zbiller\Crudhub\Traits\HasGlobalMediaConversions;
@endif
use Zbiller\Crudhub\Traits\FiltersRecords;
@if($withReorderable)
use Zbiller\Crudhub\Traits\ReordersRecords;
@endif
use Zbiller\Crudhub\Traits\SortsRecords;

class {{ $className }} extends Model @if(count($mediaCollections))implements HasMedia @endif

{
    use HasFactory;
@if($withTranslatable)
    use HasTranslations;
@endif
    use FiltersRecords;
    use SortsRecords;
@if($withReorderable)
    use ReordersRecords;
@endif
@if($usesSoftDelete)
    use SoftDeletes;
@endif
@if(count($mediaCollections))
    use AutoSavesMediaFiles;
    use HasGlobalMediaConversions;
    use InteractsWithMedia;
@endif

    /**
    * @var string
    */
    protected $table = '{{ $tableName }}';

    /**
    * @var array
    */
    protected $fillable = [
@foreach($fieldsToFill as $field)
        '{{ $field }}',
@endforeach
    ];

    /**
    * @var array
    */
    protected $casts = [
@foreach($fieldsToCast as $field => $cast)
        '{{ $field }}' => '{{ $cast }}',
@endforeach
    ];

@if($withTranslatable)
    /**
     * @var string[]
     */
    public $translatable = [
@foreach($fieldsToTranslate as $field)
        '{{ $field }}',
@endforeach
    ];

@endif
@foreach($definedRelations as $relation)
    /**
    * @return {{ $relation['relation_class'] }}
    */
    public function {{ $relation['name'] }}(): {{ $relation['relation_class'] }}
    {
@if($relation['type'] == 'belongsTo')
        return $this->{{ $relation['type'] }}({{ $relation['model_class'] }}::class, '{{ $relation['foreign_key'] }}');
@elseif($relation['type'] == 'belongsToMany')
        return $this->{{ $relation['type'] }}({{ $relation['model_class'] }}::class, '{{ $relation['table_name'] }}', '{{ $relation['foreign_key'] }}', '{{ $relation['owner_key'] }}');
@endif
    }
@endforeach

@if(count($mediaCollections))
    /**
    * @return void
    */
    public function registerMediaCollections(): void
    {
@foreach($mediaCollections as $collection)
        $this
            ->addMediaCollection('{{ $collection['name'] }}')
            ->useDisk(config('crudhub.media.disk_name'))@if(!$collection['is_single'] && !$collection['is_image']);@endif

@if($collection['is_single'])
            ->singleFile()@if(!$collection['is_image']);@endif

@endif
@if($collection['is_image'])
            ->registerMediaConversions(function (Media $media) {

            });
@endif

@endforeach
    }

    /**
     * @param Media|null $media
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->registerGlobalMediaConversions($media);
    }
@endif
@if($withReorderable && $reorderableColumn)

    /**
     * @return string
     */
    public function getOrderColumnName(): string
    {
        return '{{ $reorderableColumn }}';
    }
@endif
}
