<?php

namespace Abdu\PaymentGateway;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class Payment
{
    public $endPoint = 'https://payment.hager-bet.com/api';

    public function checkout(float $amount, string $redirect_url, $error_redirect_url)
    {

        // try {
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");
        $key = env("PAYMENT_GATEWAY_KEY");
        $data = [
            'amount' => $amount,
            'redirect_url' => $redirect_url,
        ];
        $data = json_encode($data);
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

        return $this->proccedPayment(json_decode($checkIfTheCredentialExist->body()), $redirect_url, $error_redirect_url);
    }
    public function proccedPayment($transaction, $redirect_url, $error_redirect_url)
    {
        // dd($transaction);


        return Redirect::to("https://payment.hager-bet.com/pay/" . $transaction->id . '?redirectTo=' . $redirect_url . "&errorRedirectTo=$error_redirect_url");
        // return redirect('https://google.com');
    }

    public function queryBalance()
    { // return false or the balance array
        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");


        $balance = Http::post(
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

        $invoice = Http::post(
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

    public function invoice(int $year = null, bool $paginate = false, $paginatePerpage = 10)
    {

        $username = env("PAYMENT_GATEWAY_USERNAME");
        $password = env("PAYMENT_GATEWAY_PASSWORD");

        $invoice = null;

        $invoice = Http::post(
            "$this->endPoint/invoice",
            [
                'username' => $username,
                'password' => $password,
                'year' => $year,
                'paginate' => $paginate,
                'perpage' => $paginatePerpage
            ]
        );

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
        $data = json_encode($data);
        $new_encrypter = new Encrypter($key, config('app.cipher'));
        $encrypted_data = $new_encrypter->encrypt($data);
        $send = Http::post(
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
