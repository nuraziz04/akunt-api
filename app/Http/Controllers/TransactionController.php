<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transaction = Transaction::orderBy('time', 'DESC')
            ->select('id', 'title', 'amount', DB::raw('time AS createdDate'), 'type')
            ->get();

        $response = [
            'message' => 'List transaction order by time',
            'data' => $transaction
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
            'type' => ['required', 'in:expense,revenue']
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors()
            ],Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $trasaction = Transaction::create($request->all());
            $response = [
                'message' => 'Transaction created',
                'data' => $trasaction
            ];

            return response()->json($response, Response::HTTP_CREATED);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed " .$e->errorInfo
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
             $transaction = Transaction::findOrFail($id);
            $response = [
                'message' => 'Detail of transaction resource',
                'data' => $transaction
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Transaction not found',
                'data' => null
            ], Response::HTTP_NOT_FOUND);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function findById(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $response = [
            'message' => 'Detail of transaction resource',
            'data' => $transaction
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
            'type' => ['in:expense,revenue']
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => 'error',
                'data' => $validator->errors()
            ]);
        }

        try {
            try {
                $transaction = Transaction::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                // handle the exception here, for example:
                return response()->json(['message' => 'Transaction not found', 'data' => null], Response::HTTP_NOT_FOUND);
            }

            $transaction->update($request->all());
            $response = [
                'message' => 'Transaction updated',
                'data' => $transaction
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed " . $e->errorInfo
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            try {
                $transaction = Transaction::findOrFail($id);
            } catch (ModelNotFoundException $e) {
                return response()->json(['message' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
            }

            $transaction->delete();
            $response = [
                'message' => 'Transaction deleted'
            ];

            return response()->json($response, Response::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'message' => "Failed " .$e->errorInfo
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
