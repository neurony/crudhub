<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Throwable;
use Zbiller\Crudhub\Facades\Flash;

trait PartiallyUpdatesRecords
{
    /**
     * @return string
     */
    abstract public function partialUpdateModel(): string;

    /**
     * @param Request $request
     * @return array
     */
    abstract public function partialUpdateData(Request $request): array;

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function partialUpdate(Request $request, int $id)
    {
        try {
            $model = App::make($this->partialUpdateModel())->findOrFail($id);

            DB::beginTransaction();

            foreach ($this->partialUpdateData($request) as $field => $value) {
                if (!$request->filled($field)) {
                    continue;
                }

                $model->{$field} = $value;
            }

            $model->save();

            DB::commit();

            Flash::success($this->partialUpdateSuccessMessage());

            return Redirect::back();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            Flash::error($this->partialUpdateNotFoundMessage(), $e);
        } catch (Throwable $e) {
            DB::rollBack();

            Flash::error(exception: $e);
        }
    }

    /**
     * @return string
     */
    public function partialUpdateSuccessMessage(): string
    {
        return 'Record modified successfully!';
    }

    /**
     * @return string
     */
    public function partialUpdateNotFoundMessage(): string
    {
        return 'Record not found!';
    }
}
