<?php

namespace App\Http\Controllers;

use App\Models\Companies;
use App\Models\Customers;
use App\Models\Persons;
use Illuminate\Http\Request;

class CustomersController extends Controller
{

    const PAGINATE = null;

    const TYPE = [
      'COMPANIES'=>Companies::class,
      'PERSONS'=>  Persons::class
    ];

    /**
     * @Oa\Get(
     *      path="/api/customers/index",
     *      tags={"customers"},
     *      security={ {"auth": {} } },
     *      description="get custumers",
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="number of page",
     *         required=false,
     *        @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *      @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="get by type [COMPANIES,PERSONS]",
     *         required=false,
     *        @OA\Schema(
     *             type="string",
     *         )
     *     ),
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $companies = null;
        $persons = null;

        if($request->has('type')){
            if(isset(self::TYPE[$request['type']])){
                ${strtolower($request['type'])} = (self::TYPE[$request['type']])::query()
                    ->whereHas('customers')
                    ->with('customers')->paginate(self::PAGINATE);
            }
        }else {
            $companies = Companies::query()->whereHas('customers')
                ->with('customers')->paginate(self::PAGINATE);
            $persons = Persons::query()->whereHas('customers')
                ->with('customers')->paginate(self::PAGINATE);
        };

        $data = [
            'companies' => $companies,
            'persons' => $persons
        ];
        return response()->json($data);
    }


    /**
     * @Oa\Get(
     *      path="/api/customers/search",
     *      tags={"customers"},
     *      security={ {"auth": {} } },
     *      @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="name  of  customer persons",
     *         required=false,
     *        @OA\Schema(
     *             type="string",
     *         )
     *     ),
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $data = [
            'persons' => Persons::query()->whereHas('customers')
                ->with('customers')
                ->where('name','LIKE','%'.$request['search'].'%')
                ->get(),
            'companies' => Companies::query()->whereHas('customers')
                ->with('customers')
                ->where('name','LIKE','%'.$request['search'].'%')
                ->get(),

        ];

        return response()->json($data);
    }


    public function create(Request $request)
    {

        $customer = Customers::_save($request->all());

        $data = [
            'message' => 'Created'
        ];
        return response()->json($data);
    }

    public function destroy(Request $request)
    {

        $customer = Customers::query()->findOrFail($request['id']);

        $customer->delete();

        $data = [
            'companies' => Companies::query()->whereHas('customers')
                ->with('customers')->paginate(self::PAGINATE),
            'persons' => Persons::query()->whereHas('customers')
                ->with('customers')->paginate(self::PAGINATE),
            'message' => 'deleted'
        ];
        return response()->json($data);
    }


    public function getSelectsItems(Request $request)
    {


        if (isset($request['type'])) {
            $model = Customers::TYPE[$request['type']];
        } else $model = Companies::class;

        $data = [
            'items' => $model::query()->doesntHave('customers')->get()
        ];

        return response()->json($data);
    }

}
