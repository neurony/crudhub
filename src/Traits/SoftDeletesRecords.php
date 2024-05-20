<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Throwable;
use Zbiller\Crudhub\Facades\Flash;

trait SoftDeletesRecords
{
    /**
     * @return string
     */
    abstract public function softDeleteModel(): string;

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function restore(Request $request, int $id)
    {
        try {
            $model = $this->findSoftDeletedModel($id);

            DB::beginTransaction();

            $model->restore();

            DB::commit();

            Flash::success($this->restoreSuccessMessage());

            return Redirect::back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            Flash::error($this->restoreErrorMessage(), $e);
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function forceDestroy(Request $request, int $id)
    {
        try {
            $model = $this->findSoftDeletedModel($id);

            DB::beginTransaction();

            $model->forceDelete();

            DB::commit();

            Flash::success($this->forceDestroySuccessMessage());

            return Redirect::back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            Flash::error($this->forceDestroyErrorMessage(), $e);
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @param int $id
     * @return Model
     * @throws ModelNotFoundException
     */
    protected function findSoftDeletedModel(int $id): Model
    {
        return App::make($this->softDeleteModel())
            ->withoutGlobalScopes()
            ->findOrFail($id);
    }

    /**
     * @return string
     */
    public function forceDestroySuccessMessage(): string
    {
        return 'Record deleted successfully!';
    }

    /**
     * @return string
     */
    public function forceDestroyErrorMessage(): string
    {
        return 'Operation failed!';
    }

    /**
     * @return string
     */
    public function restoreSuccessMessage(): string
    {
        return 'Record restored successfully!';
    }

    /**
     * @return string
     */
    public function restoreErrorMessage(): string
    {
        return 'Operation failed!';
    }
}
