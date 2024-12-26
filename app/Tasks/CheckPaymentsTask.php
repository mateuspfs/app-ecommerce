<?php

namespace App\Tasks;

use App\Models\OrderModel;
use App\Models\PaymentModel;
use Config\Services;

class CheckPaymentsTask
{
    public function checkPaymentsByMethod($method)
    {
        log_message('info', 'Executando checkPaymentsByMethod para o método: ' . $method);

        $orderModel = new OrderModel();
        $paymentModel = new PaymentModel();
        $client = Services::curlrequest();
        $pagSeguroToken = $_ENV['PAGSEGURO_TOKEN'];

        $payments = $paymentModel->where('method', $method)->where('status', 'Aguardando o pagamento')->findAll();

        foreach ($payments as $payment) {
            $order = $orderModel->find($payment->pedidoId);

            $response = json_decode($payment->response_pagseguro);
            $paymentIdPagSeguro = $response->id;

            $response = $client->request('GET', 'https://sandbox.api.pagseguro.com/orders/' . $paymentIdPagSeguro, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $pagSeguroToken,
                    'accept' => '*/*',
                    'content-type' => 'application/json',
                ],
                'verify' => false
            ]);

            $responsePagseguro = $response->getBody();
            $response = json_decode($responsePagseguro);
            $lastCharge = end($response->charges);

            if ($lastCharge) {
                if ($lastCharge->payment_response->message === 'SUCESSO') {
                    switch ($lastCharge->status) {
                        case 'PAID':
                            $paymentModel->update($payment->id, [
                                'response_pagseguro' => $responsePagseguro,
                                'paid_at' => $lastCharge->paid_at,
                                'status' => 'Pago'
                            ]);

                            $orderModel->update($order->id, [
                                'status' => 'Pago'
                            ]);
                            break;
                        case 'CANCELED':
                        case 'REFUNDED':
                            $paymentModel->update($payment->id, [
                                'response_pagseguro' => $responsePagseguro,
                                'status' => 'Cancelado'
                            ]);

                            $orderModel->update($order->id, [
                                'status' => 'Cancelado'
                            ]);
                            break;
                        default:
                            break;
                    }
                }
            }
        }
    }

    public function checkPaymentsPIX()
    {
        $this->checkPaymentsByMethod('pix');
    }

    public function checkPaymentsBoleto()
    {
        $this->checkPaymentsByMethod('boleto');
    }

    public function checkPaymentsCreditCard()
    {
        $this->checkPaymentsByMethod('credit-card');
    }
}
