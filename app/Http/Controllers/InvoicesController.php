<?php

namespace App\Http\Controllers;

use App\Models\Invoices;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * @Oa\Get(
     *      path="/api/incomes/invoices/index",
     *      tags={"incomes"},
     *      security={ {"auth": {} } },
     *      description="get index",
     *      @OA\Response(
     *          response=200,
     *          @OA\JsonContent(
     *             type="object",
     *             @OA\Items()
     *         ),
     *          description="successful operation"
     *       ),
     *     )
     *
     * Returns list of persons
     */
    public function getNumber(){

        return response()
            ->json([
               'number'=>'INV-'.str_pad((Invoices::query()->max('id')+1), 5, '0', STR_PAD_LEFT)
            ]);
    }
}
