<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use App\Models\OrderDetails;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function save_order(Request $request)
    {

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $all_total = 0;
        foreach ($request->details as $pr){
            $all_total += $pr['price'];
        }

        $saveOrders = new Orders;
        $saveOrders->idusers = Auth::user()->idusers;
        $saveOrders->total =  $all_total; // total semua harga kelas
        $saveOrders->codeorder = $this->code_orders();
        $saveOrders->status_order = 'pending';
        $saveOrders->status_payments = 'unpaid';
        $saveOrders->save();

        $details = [];
        $data_item = [];

        foreach ($request->details as $idx => $val) {
            $saveDetail = new OrderDetails;
            $saveDetail->idorders =   $saveOrders->idorders;
            $saveDetail->idclass = $val['idclass'];
            $saveDetail->price = $val['price'];
            $saveDetail->save();
            
            $data_item[] = [ 
                'id' => $val['idclass'],
                'price' => $val['price'],
                'quantity' => 1,
                'name' => 'example'.'-'.$idx 
            ];
             
            array_push($details, $saveDetail);
        }

        $transaction_details = array(
            'order_id'    => $this->code_orders(),
            'gross_amount'  => $all_total
          );

        $customer_details = array(
            'first_name'       => Auth::user()->name,
            'last_name'        => Auth::user()->name,
            'email'            => Auth::user()->email,
            'phone'            => "081322311801"
        );
  
        $transaction_data = array(
            'transaction_details' => $transaction_details,
            'item_details'        => $data_item,
            'customer_details'    => $customer_details
        );
  
        $snapToken = \Midtrans\Snap::getSnapToken($transaction_data);
     
        return response()->json([ 
            'headers' =>  $saveOrders,
            'details' => $details,
            'token' => $snapToken,
            'transaction' => $transaction_data
        ]);

    }

    protected function code_orders()
    {
        $date_ym = date('ym');
        $date_between =  [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')];
        $dataOrders = Orders::select('codeorder')
  			->whereBetween('created_at',$date_between)
  			->orderBy('codeorder','desc')
  			->first();
  		if(is_null($dataOrders)) {
  			$nowcode = '00001';
  		} else {
  			$lastcode = $dataOrders->codeorder;
  			$lastcode1 = intval(substr($lastcode, -5))+1;
  			$nowcode = str_pad($lastcode1, 5, '0', STR_PAD_LEFT);
  		}

  		return 'PO-'.$date_ym.'-'.$nowcode;
    }


    public function status_payment(Orders $order)
    {
        $order  = Orders::where('idorders',$order->idorders)->first();
        $status = \Midtrans\Transaction::status($order->codeorder);

        return response()->json([ 
            'status' =>  $status 
        
        ]);
    }
}
