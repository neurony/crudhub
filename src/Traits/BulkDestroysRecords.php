<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Throwable;
use Zbiller\Crudhub\Facades\Flash;

trait BulkDestroysRecords
{
    /**
     * @return string
     */
    abstract public function bulkDestroyModel(): string;

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function bulkDestroy(Request $request)
    {
        $model = $this->bulkDestroyModel();
        $ids = $this->bulkDestroyIds($request);

        if (empty($ids)) {
            return Redirect::back();
        }

        try {
            DB::beginTransaction();

            $items = App::make($model)->whereIn('id', $ids)->get();

            foreach ($items as $item) {
                $item->delete();
            }

            Flash::success($this->bulkDestroySuccessMessage());

            DB::commit();

            return Redirect::back();
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error($this->bulkDestroyErrorMessage(), $e);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function bulkDestroyIds(Request $request): array
    {
        return (array)($request->get('ids') ?? []);
    }

    /**
     * @return string
     */
    public function bulkDestroySuccessMessage(): string
    {
        return 'Records deleted successfully!';
    }

    /**
     * @return string
     */
    public function bulkDestroyErrorMessage(): string
    {
        return 'Could not delete the records!';
    }
}
