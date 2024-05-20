<?php

namespace Zbiller\Crudhub\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;

trait SavesRecordsOrder
{
    /**
     * @return string
     */
    abstract public function reorderModel(): string;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        App::make($this->reorderModel())->setNewOrder(
            array_filter(array_values($request->input('ids')))
        );

        return Response::json([
            'status' => true,
        ]);
    }
}
