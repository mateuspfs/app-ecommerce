<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\CartModel;
use App\Models\UserModel;
use App\Models\UserTokenModel;
use DateTime;

class AuthController extends BaseController
{
    protected $userModel;
    protected $tokenModel;
    protected $cartModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->tokenModel = new UserTokenModel();
        $this->cartModel = new CartModel();
    }

    public function index()
    {
        echo View('site/auth/index');
    }

    public function store()
    {
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('password');

        $userFound = $this->userModel->where('email', $email)->first();

        if(!$userFound){
            return redirect()->route('auth.login')->withInput()->with('error', 'Credenciais não válidas, tente novamente');
        } 

        if(!password_verify((string)$senha, $userFound->password)){
            return redirect()->route('auth.login')->withInput()->with('error', 'Credenciais não válidas, tente novamente');
        } 
        
        if($userFound->status === '0'){
            return redirect()->route('auth.login')->withInput()->with('error', 'Sua conta foi inativada, entre em contato com o suporte caso aja dúvidas.');
        }
        
        unset($userFound->password);
        session()->set('user', $userFound);

        $this->addProductsCart($userFound->id);

        if(session()->has('redirect_to')){
            $redirect_to = session()->get('redirect_to');
            session()->remove('redirect_to');
            return redirect()->to($redirect_to);
        }

        return redirect()->route('index');
    }

    public function forgotPassword(){
        echo View('site/auth/forgotPassword');
    }

    public function forgotPasswordSubmit(){
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('email', $email)->first();

        if(!$user){
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro, tente novamente');
        } 

        $date = new DateTime();
        $date->modify('+30 minutes');
        $token = md5(uniqid());

        $tokenExists = $this->tokenModel->where('email', $user->email)->first();

        if(!$tokenExists){  
            $this->tokenModel->insert([
                'token' => $token,
                'email' => $user->email,
                'expired_at' => $date->format('Y-m-d H:i:s')
            ]);
        } else {
            $timestamp = new DateTime();
            $expired_at = new DateTime($tokenExists->expired_at);
            $currentDate = $timestamp->format('Y-m-d H:i:s');
            $tokenExpiredDate = $expired_at->format('Y-m-d H:i:s');

            if($currentDate > $tokenExpiredDate){ 
                $this->tokenModel->update($tokenExists->id, [
                    'token' => $token,
                    'expired_at' => $date->format('Y-m-d H:i:s')
                ]); 
            } else {
                return redirect()->back()->withInput()->with('error', 'Já foi enviado um email para redefinição de senha, confira sua caixa de email!');
            }
        }

        $email = \Config\Services::email();
        $email->setTo($user->email);
        $email->setSubject('Redefinição de senha');
        $template = View('site/mails/resetPassword', ['user' => $user, 'token' =>$token]);
        $email->setMessage($template);

        if ($email->send()) {
            return redirect()->route('auth.login')->withInput()->with('success', 'Redefinição enviada, verifique a caixa de entrada do seu email!');
        } else {
            log_message('error', 'Erro ao enviar o e-mail: ' . $email->printDebugger(['headers']));
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente mais tarde.');
        }
    }

    public function resetPassword(){
        echo View('site/auth/resetPassword');
    }

    public function resetPasswordSubmit(){
        $email = $this->request->getPost('user_email');
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $cpassword = $this->request->getPost('cpassword');

        $tokenExists = $this->tokenModel
                            ->where('email', $email)
                            ->where('token', $token)
                            ->first();

        if(!$tokenExists){
            return redirect()->route('auth.login')->with('error', 'Ocorreu um erro, refaça o processo.');
        }

        if($password === $cpassword){
            $user = $this->userModel->where('email', $email);

            $this->userModel->update($user->id, [
                'password' => password_hash((string)$password, PASSWORD_DEFAULT)
            ]);

            return redirect()->route('auth.login')->with('success', 'Senha atualizada com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Senhas não coincidem.');
        }
    }

    public function logout(){
        if(session()->has('user')){
            unset($_SESSION['user']);
        }

        return redirect()->route('index')->with('success', 'Deslogado com sucesso!');
    }

    private function addProductsCart($userId)
    {
        $produtos = session()->get('produtos') ?? [];

        foreach($produtos as $produto){
            $productExists = $this->cartModel
                                ->where('userId', $userId)
                                ->where('produtoId', $produto->produtoId)
                                ->first();

            if($productExists){
                $this->cartModel->update($productExists->id,[
                    'quantidade' => $produto->quantidade
                ]);
            } else {
                $this->cartModel->insert([
                    'userId' => $userId,
                    'produtoId' => $produto->produtoId,
                    'quantidade' => $produto->quantidade
                ]);
            }
        }
        
        session()->remove('produtos');
    }
}
