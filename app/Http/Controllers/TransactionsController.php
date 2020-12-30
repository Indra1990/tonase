<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\User;
use App\Models\GradeTotals;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }    

    public function create_save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:u,w,t',
            'amount' => 'required|numeric',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        if($request->amount > 0){

            $user = User::with(['grade_totals'])
                        ->where('idusers',Auth::user()->idusers)
                        ->first();

            $idtransfer = NULL;
            if (!empty($request->idtransfers)) {
                $idtransfer = $request->idtransfers;
            }
    
            $transactions = new Transactions;
            $transactions->idgradetotals = $user->grade_totals->idgradetotals;
            $transactions->type = $request->type;
            $transactions->amount = $request->amount;
            $transactions->idtransfers = $idtransfer ;
            $transactions->save();

            // amount grade total
            $this->grade_totals_amount($user->grade_totals->idgradetotals,$request->type, $request->amount,$idtransfer);

            return response()->json([
                'user' => $user,
                'transactions' => $transactions   
            ], 200);
        }else{
            return response()->json([
                'amount' => 'the amount of cannot be 0 '
            ]);

        }
    }

    protected function grade_totals_amount($idgradetotals,$type,$amount,$idtransfers)
    {
        
        $save_total =  GradeTotals::find($idgradetotals);
        if ($type == 'u') {
            $save_total->total += $amount;
        }elseif($type == 'w'){
            $save_total->total -= $amount;
        }elseif($type == 't'){
            $save_total->total -= $amount;
        }

        $save_total->save();
    }
}
