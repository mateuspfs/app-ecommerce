<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CupomModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\ProductModel;
use App\Models\ProductsOrderModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use Exception;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Util\Json;

class PaymentController extends BaseController
{
    protected $pagSeguroToken;
    protected $orderModel;
    protected $productModel;
    protected $productOrderModel;
    protected $userModel;
    protected $paymentModel;
    protected $cupomModel;
    protected $client;

    public function __construct()
    {
        $this->pagSeguroToken = $_ENV['PAGSEGURO_TOKEN'];
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
        $this->productOrderModel = new ProductsOrderModel();
        $this->userModel = new UserModel();
        $this->cupomModel = new CupomModel();
        $this->paymentModel = new PaymentModel();
        $this->client = new \GuzzleHttp\Client();
    }

    public function payOrder($orderId, $paymentMethod, $cardEncrypted = null)
    {
        $order = $this->orderModel->find($orderId);
        $user = $this->userModel->find($order->userId);
        $productsOrder = $this->productOrderModel->where('pedidoId', $order->id)->findAll();

        $inserted = $this->paymentModel->insert([
            'pedidoId' => $orderId,
            'method' => $paymentMethod,
            'status' => 'Aguardando criação de pagamento'
        ]);

        if (!$inserted) {
            dd($this->paymentModel->errors());
        }

        $payment = $this->paymentModel->find($this->paymentModel->insertID());

        if ($order->valor == 0.00) {
            $this->paymentModel->update($payment->id, [
                'status' => 'Pago'
            ]);

            $this->orderModel->update($order->id, [
                'status' => 'Pago'
            ]);

            return redirect()->route('order.index')->with('success', 'Pedido feito com sucesso!');
        }

        // dd($payment);

        // Preparar dados 
        $products = [];
        foreach ($productsOrder as $productOrder) {
            $product = $this->productModel->find($productOrder->produtoId);

            $products[] = [
                'reference_id' => $product->id,
                'name' => $product->nome,
                'quantity' => $productOrder->quantidade,
                'unit_amount' => onlyNumber($product->preco)
            ];
        }

        $endereco = json_decode($order->endereco, true);

        switch ($paymentMethod) {
            case 'credit-card':
                $charges = [
                    [
                        'reference_id' => $payment->id,
                        'description' => 'Pagamento do pedido' . $order->codigo,
                        'amount' => [
                            'value' => onlyNumber($order->valor),
                            'currency' => 'BRL'
                        ],
                        'payment_method' => [
                            'type' => 'CREDIT_CARD',
                            'installments' => 1,
                            'capture' => true,
                            'card' => [
                                'encrypted' => $cardEncrypted,
                                'store' => false
                            ],
                            'holder' => [
                                'name' => $user->nome,
                                'tax_id' => $user->cpf,
                            ]
                        ]
                    ]
                ];
                break;
            case 'boleto':
                $currentDate = new DateTime();
                $currentDate->modify('+1 week');
                $formattedDate = $currentDate->format('Y-m-d');

                $charges = [
                    [
                        'reference_id' => $payment->id,
                        'description' => 'Pagamento do pedido ' . $order->codigo . ' na plataforma xxxxxx',
                        'amount' => [
                            'value' => onlyNumber($order->valor),
                            'currency' => 'BRL'
                        ],
                        'payment_method' => [
                            'type' => 'BOLETO',
                            'boleto' => [
                                'due_date' => $formattedDate,
                                'holder' => [
                                    'name' => $user->nome,
                                    'email' => $user->email,
                                    'tax_id' => $user->cpf,
                                    'address' => [
                                        'country' => 'Brasil',
                                        'region' => getEstadoPorSigla($endereco['uf']),
                                        'region_code' => $endereco['uf'],
                                        'city' => $endereco['cidade'],
                                        'postal_code' => onlyNumber($endereco['cep']),
                                        'street' => $endereco['rua'],
                                        'number' => onlyNumber($endereco['numero']),
                                        'locality' => $endereco['bairro']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ];
                break;
            default:
                $charges = null;
                break;
        };

        $body = [
            'reference_id' => $payment->id,
            'customer' => [
                'name' => $user->nome,
                'email' => $user->email,
                'tax_id' => $user->cpf,
                'phones' => [
                    [
                        'country' => '55',
                        'area' => substr($user->telefone, 0, 2),
                        'number' => substr($user->telefone, 2),
                        'type' => 'MOBILE'
                    ]
                ]
            ],
            'items' => $products,
            'qr_codes' => [
                [
                    'amount' => [
                        'currency' => 'BRL',
                        'value' => onlyNumber($order->valor),
                    ]
                ]
            ],
            'shipping' => [
                'address' => [
                    'street' => $endereco['rua'],
                    'number' => onlyNumber($endereco['numero']),
                    'complement' => $endereco['complemento'] || '',
                    'locality' => $endereco['bairro'],
                    'city' => $endereco['cidade'],
                    'region_code' => $endereco['uf'],
                    'postal_code' => onlyNumber($endereco['cep']),
                    'country' => 'BRA'
                ]
            ],
            'billing' => [
                'address' => [
                    'street' => $endereco['rua'],
                    'number' => $endereco['numero'],
                    'complement' => $endereco['complemento'] || '',
                    'locality' => $endereco['bairro'],
                    'city' => $endereco['cidade'],
                    'region_code' => $endereco['uf'],
                    'postal_code' => onlyNumber($endereco['cep']),
                    'country' => 'BRA'
                ]
            ],
            'notification_urls' => [
                'https://meusite.com/notificacoes'
            ]
        ];

        if (isset($charges)) {
            $body['charges'] = [$charges[0]];
        }

        try {
            $response = $this->client->request('POST', 'https://sandbox.api.pagseguro.com/orders', [
                'body' => json_encode($body),
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->pagSeguroToken,
                    'accept' => '*/*',
                    'content-type' => 'application/json',
                ],
                'verify' => false
            ]);

            $responseBody = $response->getBody()->getContents();

            $this->paymentModel->update($payment->id, [
                'response_pagseguro' => $responseBody,
                'status' => 'Aguardando o pagamento'
            ]);
        } catch (RequestException $e) {
            $errorResponse = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();

            $this->paymentModel->update($payment->id, [
                'status' => 'Erro ao emitir pagamento',
                'response_pagseguro' => $errorResponse
            ]);
        }

        return $payment->id;
    }

    public function pay($order)
    {
        $user = session('user')->id;
        $orderFind = $this->orderModel->where('codigo', $order)->first();


        if ($user != $orderFind->userId) {
            return redirect()->route('index')->with('error', 'Houve um erro com o pagamento!');
        }

        $payment = $this->paymentModel->where('pedidoId', $orderFind->id)->orderBy('id', 'DESC')->first();

        if ($payment->status === 'Aguardando o pagamento') {
            $response = json_decode($payment->response_pagseguro);
            if ($payment->method === 'pix') {
                $linksPay = [
                    'method' => 'pix',
                    'qr_code' => $response->qr_codes[0]->links[0]->href,
                    'text' => $response->qr_codes[0]->text,
                    'pdf' => null,
                ];
            } elseif ($payment->method === 'boleto') {
                $lastCharge = end($response->charges);
                $linksPay = [
                    'method' => 'boleto',
                    'pdf' => $lastCharge->links[0]->href,
                    'text' => $lastCharge->payment_method->boleto->formatted_barcode,
                    'qr_code' => null,
                ];
            } else {
                return redirect()->route('order.index');
            }

            return view('site/payOrder', [
                'returnPayment' => $linksPay
            ]);
        }

        return redirect()->route('order.index');
    }

    public function consultaPayment($orderCodigo)
    {
        $order = $this->orderModel->where('codigo', $orderCodigo)->first();

        if (!$order) {
            return redirect()->route('index')->with('error', 'Houve um erro!');
        }

        $payment = $this->paymentModel->where('pedidoid', $order->id)->orderBy('id', 'DESC')->first();

        $response = json_decode($payment->response_pagseguro);
        $paymentIdPagSeguro = $response->id;

        $response = $this->client->request('GET', 'https://sandbox.api.pagseguro.com/orders/' . $paymentIdPagSeguro, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->pagSeguroToken,
                'accept' => '*/*',
                'content-type' => 'application/json',
            ],
            'verify' => false
        ]);

        $responsePagseguro = $response->getBody()->getContents();
        $response = json_decode($responsePagseguro);

        if (isset($response->charges)) {
            $lastCharge = end($response->charges);

            if ($lastCharge) {
                if ($lastCharge->payment_response->message === 'SUCESSO') {
                    switch ($lastCharge->status) {
                        case 'PAID':
                            $this->paymentModel->update($payment->id, [
                                'response_pagseguro' => $responsePagseguro,
                                'paid_at' => $lastCharge->paid_at,
                                'status' => 'Pago'
                            ]);

                            $this->orderModel->update($order->id, [
                                'status' => 'Pago'
                            ]);
                            break;
                        case 'CANCELED':
                            $this->paymentModel->update($payment->id, [
                                'response_pagseguro' => $responsePagseguro,
                                'status' => 'Cancelado'
                            ]);

                            $this->orderModel->update($order->id, [
                                'status' => 'Cancelado'
                            ]);
                            break;
                        case 'REFUNDED':
                            $this->paymentModel->update($payment->id, [
                                'response_pagseguro' => $responsePagseguro,
                                'status' => 'Cancelado'
                            ]);

                            $this->orderModel->update($order->id, [
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

    public function cancelPayment($orderCodigo)
    {
        $order = $this->orderModel->where('codigo', $orderCodigo)->first();

        if (!$order) {
            return redirect()->route('index')->with('error', 'Houve um erro!');
        }

        $payment = $this->paymentModel->where('pedidoId', $order->id)->orderBy('id', 'DESC')->first();

        $response = json_decode($payment->response_pagseguro);

        if ($response == null || $order->valor == 0.00) {
            $user = $this->userModel->find(session('user')->id);
            $pago = ($order->status == 'Pago') ? true : false;

            $this->orderModel->update($order->id, [
                'status' => 'Cancelado'
            ]);

            $this->paymentModel->update($payment->id, [
                'status' => 'Cancelado'
            ]);

            if ($pago) {
                $this->userModel->update($user->id, [
                    'credito' => $user->credito + $order->valor
                ]);

                $this->paymentModel->update($payment->id, [
                    'status' => 'Devolvido via crédito no sistema'
                ]);

                return redirect()->back()->with('success', 'Pedido cancelado com sucesso, valor pago convertido em crédito no sistema!');
            } else {
                return redirect()->back()->with('success', 'Pedido cancelado com sucesso!');
            }
        }

        $lastCharge = end($response->charges);

        $body = [
            'amount' => [
                'value' => onlyNumber($order->valor)
            ]
        ];

        try {
            if ($payment->method === 'credit-card') {
                $responseApi = $this->client->request('POST', 'https://sandbox.api.pagseguro.com/charges/' . $lastCharge->id . '/cancel', [
                    'body' => json_encode($body),
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->pagSeguroToken,
                        'accept' => '*/*',
                        'content-type' => 'application/json',
                    ],
                    'verify' => false
                ]);

                $responsePagseguro = $responseApi->getBody()->getContents();
                $response = json_decode($responsePagseguro);

                if (isset($response->error_messages)) {
                    $errorMessage = $response->error_messages[0]->message;
                    return redirect()->route('index')->with('error', 'Houve um erro ao estornar valor, tente novamente mais tarde ou entre em contato com o suporte! Detalhe do erro: ' . $errorMessage);
                }

                $this->paymentModel->update($payment->id, [
                    'response_pagseguro' => $responsePagseguro
                ]);

                if ($response->message === 'unable_refund') {
                    return redirect()->route('index')->with('error', 'Houve um erro ao estronar valor, tente novamente mais tarde ou entre em contato com o suporte!');
                }

                $this->orderModel->update($order->id, [
                    'status' => 'Cancelado'
                ]);
            } else {
                $user = $this->userModel->find(session('user')->id);
                $pago = ($order->status === 'Pago') ? true : false;

                $this->orderModel->update($order->id, [
                    'status' => 'Cancelado'
                ]);

                $this->paymentModel->update($payment->id, [
                    'status' => 'Cancelado'
                ]);

                if ($pago) {
                    $this->userModel->update($user->id, [
                        'credito' => $user->credito + $order->valor
                    ]);

                    $this->paymentModel->update($payment->id, [
                        'status' => 'Devolvido via crédito no sistema'
                    ]);

                    return redirect()->back()->with('success', 'Pedido cancelado com sucesso, valor pago convertido em crédito no sistema!');
                }
            }

            return redirect()->back()->with('success', 'Pedido cancelado com sucesso, sua cobrança será estornada em até 90 dias!');
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();
            $response = json_decode($responseBody);

            if (isset($response->error_messages)) {
                $errorMessage = $response->error_messages[0]->description;
                return redirect()->route('index')->with('error', 'Houve um erro ao estornar valor, tente novamente mais tarde ou entre em contato com o suporte! Detalhe do erro: ' . $errorMessage);
            }

            return redirect()->route('index')->with('error', 'Houve um erro ao processar o cancelamento, tente novamente mais tarde ou entre em contato com o suporte!');
        } catch (\Exception $e) {
            return redirect()->route('index')->with('error', 'Houve um erro ao processar o cancelamento, tente novamente mais tarde ou entre em contato com o suporte!');
        }
    }

    public function getPublicKey()
    {
        $response = $this->client->request('POST', 'https://sandbox.api.pagseguro.com/public-keys', [
            'body' => '{"type":"card"}',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->pagSeguroToken,
                'accept' => '*/*',
                'content-type' => 'application/json',
            ],
            'verify' => false
        ]);

        $responsePagseguro = $response->getBody()->getContents();
        $publicKey = json_decode($responsePagseguro);
        return $publicKey->public_key;
    }
}
