<?php

namespace Abdu\PaymentGateway;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class Payment
{
    public $endPoint = 'http://localhost:8050/api';

    public function checkout(float $amount,string $redirect_url)
    {

        // try {
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        $key = env("PAYMENT_GATEWAY_KEY");
        $data = [
            'amount' => $amount,
            'redirect_url' => $redirect_url,
        ];
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
            dd($checkIfTheCredentialExist->body());
            abort(500, $checkIfTheCredentialExist->body());
        }
        // dd($checkIfTheCredentialExist->body());

        return $this->proccedPayment(json_decode($checkIfTheCredentialExist->body()), $data['redirect_url']);
    
    }
    public function proccedPayment($transaction, $redirect_url)
    {
        // dd($transaction);


        return Redirect::to("http://localhost:8050/pay/" . $transaction->id . '?redirectTo=' . $redirect_url);
        // return redirect('https://google.com');
    }

    public function queryBalance()
    { // return false or the balance array
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");


        $balance = Http::get(
            "$this->endPoint/balance/query",
            [
                'username' => $username,
                'password' => $password,
            ]
        );

        // if ($balance->status() != 200) {
        //     return false;
        // }
        return [
            'status' => $balance->status(),
            'body' => $balance->body()
        ];
        // return $balance->body();

    }

    public function getInvoice(int $invoice_id)
    { //return error code or the invoice array
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
        // if($invoice->status() != 200){
        //     return $invoice->status();
        // }
        // return $invoice->body();
        return [
            'status' => $invoice->status(),
            'body' => $invoice->body()
        ];
    }

    /**
     *get invoice of the transaction

     *@return array 

     */

    public function invoice(string $start = null, string $end = null, int $year = null, bool $paginate = false, $paginatePerpage = 10)
    {

        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");

        $invoice = null;
        if ($paginate == false) {
            $invoice = Http::get(
                "$this->endPoint/invoice",
                [
                    'username' => $username,
                    'password' => $password,
                    'start' => $start,
                    'end' => $end,
                    'year' => $year,
                    'perpage' => $paginatePerpage
                ]
            );
        } else {
            $invoice = Http::get(
                "$this->endPoint/invoice",
                [
                    'username' => $username,
                    'password' => $password,
                    'start' => $start,
                    'end' => $end,
                    'year' => $year,
                    'paginate' => true,
                    'perpage' => $paginatePerpage
                ]
            );
        }

        return [
            'status' => $invoice->status(),
            'body' => $invoice->body(),
        ];
    }

    public function send(string|array $address, float|array $amount)
    {
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        $key = env("PAYMENT_GATEWAY_KEY");
        $data = [
            'address' => $address,
            'amount' => $amount
        ];
        $new_encrypter = new Encrypter($key, config('app.cipher'));
        $encrypted_data = $new_encrypter->encrypt($data);
        $send = Http::get(
            "$this->endPoint/send",
            [
                'username' => $username,
                'password' => $password,
                'data' => $encrypted_data
            ]
        );

        return [
            'status' => $send->status(),
            'body' => $send->body(),
        ];
    }
}
