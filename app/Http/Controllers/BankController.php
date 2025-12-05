<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bank;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Paginate results, showing parent::$show_per_page items per page
        $banks = Bank::paginate(parent::$show_per_page)->withQueryString();;
        if($banks){
            return response()->json([
                'message' => 'Bank lists successfully fetched.',
                'user' => $banks
            ]);
        }else{
            return response()->json([
                'message' => 'No banks found.',
            ], 403);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'bank_address' => 'required',
            'bank_email' => 'required',
        ]);

        $bank = Bank::create([
            'bank_name' => $request->bank_name,
            'bank_address' => $request->bank_address,
            'bank_contact_no' => $request->bank_contact_no ?? '',
            'bank_email' => $request->bank_email
        ]);

        return response()->json([
            'message' => 'Bank Created Successful',
            'bank' => $bank
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bank = Bank::find($id);
        if($bank){
            return response()->json([
                'message' => 'Bank Fetched Successful',
                'user' => $bank
            ]);
        }else{
            return response()->json([
                'message' => 'Bank not found',
            ], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $request->validate([
            'bank_name' => 'required',
            'bank_address' => 'required',
            'bank_email' => 'required',
        ]);

        $bank = Bank::find($id)
                    ->update([
                        'bank_name' => $request->bank_name,
                        'bank_address' => $request->bank_address,
                        'bank_contact_no' => $request->bank_contact_no ?? '',
                        'bank_email' => $request->bank_email
                    ]);
        if($bank){
            return response()->json([
                'message' => 'Bank Successful Updated',
                'user' => $bank
            ]);
        }else{
            return response()->json([
                'message' => 'Bank not found',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bank = Bank::find(1);

        if ($bank) {
            // Permanently deletes the record from the database
            $bank->delete();
            return response()->json([
                'message' => 'Bank Successful Deleted',
                'bank' => $bank
            ]);
        }
    }
}
