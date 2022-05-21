<?php 

namespace Abdu\PaymentGateway;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class Payment
{
    public $endPoint = 'http://localhost:8050/api';

    public function checkout(array $data)
    {

        // try {
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        $key = env("PAYMENT_GATEWAY_KEY");
        if (array_key_exists('amount', $data) && array_key_exists('redirect_url', $data)) {
            $new_encrypter = new Encrypter($key, config('app.cipher'));
            $encrypted_data = $new_encrypter->encrypt($data);
            $checkIfTheCredentialExist = Http::post(
                "$this->endPoint/create/temporary-transaction",
                [
                    'username' => $username,
                    'password' => $password,
                    'data' => $encrypted_data
                ]
            );
            // dd($checkIfTheCredentialExist->status());
            if ($checkIfTheCredentialExist->status() != 200) {

                abort(500, $checkIfTheCredentialExist->body());
            }
            // dd($checkIfTheCredentialExist->body());

            return $this->proccedPayment(json_decode($checkIfTheCredentialExist->body()),$data['redirect_url']);
        } else {

            abort(500, 'You have to specified an amount or redirect url in your data');
        }
    }
    public function proccedPayment($transaction,$redirect_url)
    {
        // dd($transaction);
        

        return Redirect::to("http://localhost:8050/pay/".$transaction->id.'?redirectTo='.$redirect_url);
        // return redirect('https://google.com');
    }

    public function queryBalance(){ // return false or the balance array
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        

        $balance = Http::get(
            "$this->endPoint/balance/query",
            [
                'username' => $username,
                'password' => $password,
            ]
        );

        if ($balance->status() != 200) {
            return false;
        }
        return $balance->body();

    }

    public function getInvoice(int $invoice_id){ //return error code or the invoice array
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        
        $invoice = Http::get(
            "$this->endPoint/invoice/$invoice_id",
            [
                'username' => $username,
                'password' => $password,
                
            ]
        );
        // dd($invoice->status());
        if($invoice->status() != 200){
            return $invoice->status();
        }
        return $invoice;
    }

    public function invoice($start,$end,int $year, bool $paginate = false, $paginatePerpage = 10){



    }
}