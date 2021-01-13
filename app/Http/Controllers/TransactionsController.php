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
        $this->middleware('jwt.verify');
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
            if ($request->type == 'u') {
                $type_log = 'Top up';
            }elseif($request->type == 'w'){
                $type_log = 'Withdraw';
            }elseif($request->type == 't'){
                $type_log = 'Transfer';
            }

            $user = User::with(['grade_totals'])
                        ->where('idusers',Auth::user()->idusers)
                        ->first();

            if ($request->type == 't' || $request->type == 'w') {
                if ($user->grade_totals->total == 0 || $request->amount > $user->grade_totals->total ) {
                    return response()->json([
                        'msg' => 'your balance is insufficient'   
                    ], 400);
                }
            }

            $idtransfer = $idfrom =  NULL;
            if (!empty($request->idtransfers)) {
                $idtransfer = $request->idtransfers;
                $idfrom = Auth::user()->idusers;
            }
    
            $transactions = new Transactions;
            $transactions->idgradetotals = $user->grade_totals->idgradetotals;
            $transactions->type = $request->type;
            $transactions->amount = $request->amount;
            $transactions->idtransfers = $idtransfer ;
            $transactions->idfrom = $idfrom ;
            $transactions->save();

            // log activity
            log_activity('Create Transaction '.$type_log.' Successfully',url()->current(), $request->method() ,Auth::user()->idusers);

            // amount grade total
            $this->grade_totals_amount($user->grade_totals->idgradetotals,$request->type, $request->amount,$idtransfer);

            return response()->json([
                'transactions' => $transactions   
            ], 200);
        }else{
            return response()->json([
                'amount' => 'the amount of cannot be 0 '
            ]);

        }
    }


    public function get_mutasi(Request $request)
    {
  
        $get_mutasi = GradeTotals::with(['transaction'])->where('idusers',Auth::user()->idusers)
                        ->first();
                        
        $transfers = Transactions::with(['to_transfer','from_transfer'])
                    ->where('idtransfers',Auth::user()->idusers)->get();

        $data_mutasi = [];
        foreach ($get_mutasi->transaction as $trans) {
            if ($trans->type == 'u') {
                $type_transfer = 'Top Up';
            }elseif($trans->type == 'w'){
                $type_transfer = 'Widtdraw';
            }elseif($trans->type == 't'){
                $type_transfer = 'Transfer';
            }

            $to = NULL;
            if (!is_null($trans->idtransfers )) {
                $to = $trans->to_transfer->name;
            }

            $data_mutasi[] = [
                'type' =>  $type_transfer,
                'amount'=> $trans->amount,
                'to' => $to,
                'from' => NULL,
                'date' => date('Y-m-d',strtotime($trans->created_at))
            ];
        }

        foreach ($transfers as $transfer) {
            $transfer_type = NULL;
            if ($transfer->type =='t') {
                $transfer_type = 'Get Transfer';
            }

            $data_mutasi[] = [
                'type' =>   $transfer_type,
                'amount' => $transfer->amount,
                'to' => NULL,
                'from' => $transfer->from_transfer->name,
                'date' => date('Y-m-d',strtotime($transfer->created_at)) 
            ];
        }
        rsort($data_mutasi);
        // 
        log_activity('Show Mutasi',url()->current(), $request->method() ,Auth::user()->idusers);
        return response()->json(['mutasi' => $data_mutasi],200);
    }

    protected function grade_totals_amount($idgradetotals,$type,$amount,$idtransfers)
    {
        $save_total =  GradeTotals::find($idgradetotals);
        if ($type == 'u') {
            $save_total->total += $amount;
        }elseif($type == 'w'){
            $save_total->total -= $amount;
        }elseif($type == 't'){
            $transfer = GradeTotals::where('idusers',$idtransfers)->first();
            $transfer->total += $amount;
            $transfer->save();

            $save_total->total -= $amount;
        }
        $save_total->save();

        return $save_total;
    }
}
