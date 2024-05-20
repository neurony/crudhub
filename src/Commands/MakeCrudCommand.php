<?php

namespace Zbiller\Crudhub\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Foundation\Console\OptimizeClearCommand;
use Zbiller\Crudhub\Crud\CrudOptions;
use Zbiller\Crudhub\Crud\CrudSchema;
use Zbiller\Crudhub\Crud\CrudTrait;
use Zbiller\Crudhub\Crud\CrudGenerator;
use Zbiller\Crudhub\Exceptions\GenerateCrudException;
use Zbiller\Crudhub\Exceptions\MakeCrudException;

class MakeCrudCommand extends Command
{
    use CrudTrait;
    use InteractsWithIO;

    /**
     * @var string
     */
    protected $signature = 'crudhub:make-crud   {table_name? : The table name}';

    /**
     * @var string
     */
    protected $description = 'Generate CRUD';

    /**
     * @var CrudSchema
     */
    protected CrudSchema $schema;

    /**
     * @var CrudOptions
     */
    protected CrudOptions $options;

    /**
     * @var array
     */
    protected array $result;

    /**
     * @param CrudSchema $schema
     * @param CrudOptions $options
     */
    public function __construct(CrudSchema $schema, CrudOptions $options)
    {
        parent::__construct();

        $this->schema = $schema;
        $this->options = $options;
    }

    /**
     * @return int
     */
    public function handle()
    {
        $this->components->info('Starting the CRUD generation');

        try {
            $this->setTableAndColumns($this->argument('table_name'));

            $this->askForTableFields();
            $this->askForFilterFields();
            $this->askForFormFields();
            $this->askForTranslatableFields();
            $this->askForModelRelations();
            $this->askForMediaCollections();
            $this->askForReorderableFeature();

            $this->components->task('Generating the CRUD', function () {
                $this->result = (new CrudGenerator($this->schema, $this->options))->generate();

                if (!$this->result['success']) {
                    throw new MakeCrudException('Something went wrong! Please revert and check your migration file and the options you selected.');
                }
            });

            $this->finalSteps();

            return Command::SUCCESS;
        } catch (MakeCrudException|GenerateCrudException $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
