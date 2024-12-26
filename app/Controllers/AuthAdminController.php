<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Admin;
use App\Models\AdminModel;
use App\Models\AdminToken;
use App\Models\AdminTokenModel;
use DateTime;

class AuthAdminController extends BaseController
{
    protected $adminModel;
    protected $tokenModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->tokenModel = new AdminTokenModel();
    }

    public function index()
    {
        if(session()->has('admin')){
            return redirect()->route('admin.index');  
        }

        echo View('admin/auth/index');
    }

    public function store()
    {
        $validate = $this->validate([
            'email' => 'required|valid_email',
            'password' => 'required'
        ], [
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'O email deve ser válido',
            ],
            'password' => [
                'required' => 'A senha é obrigatória'
            ]
        ]);

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('password');

        if(!$validate){
            return redirect()->route('authAdmin.index')->withInput()->with('error', 'Credenciais não válidas, tente novamente');
        } 

        $userFound = $this->adminModel->where('email', $email)->first();

        if(!$userFound){
            return redirect()->route('authAdmin.index')->withInput()->with('error', 'Credenciais não válidas, tente novamente');
        } 

        if(!password_verify((string)$senha, $userFound->password)){
            return redirect()->route('authAdmin.index')->withInput()->with('error', 'Credenciais não válidas, tente novamente');
        } 
        
        unset($userFound->password);
        session()->set('admin', $userFound);

        return redirect()->route('admin.index');
    }

    public function forgotPassword(){
        echo View('admin/auth/forgotPassword');
    }

    public function forgotPasswordSubmit(){
        $email = $this->request->getPost('email');
        $user = $this->adminModel->where('email', $email)->first();

        if(!$user){
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro, tente novamente');
        } 

        $date = new DateTime();
        $date->modify('+30 minutes');
        $token = md5(uniqid());

        $tokenExists = $this->tokenModel->where('email', $this->adminModel->email)->first();

        if(!$tokenExists){  
            $this->tokenModel->insert([
                'token' => $token,
                'email' => $this->adminModel->email,
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
        $email->setTo($this->adminModel->email);
        $email->setSubject('Redefinição de senha');
        $template = View('admin/mails/resetPassword', ['user' => $user, 'token' =>$token]);
        $email->setMessage($template);

        if ($email->send()) {
            return redirect()->route('authAdmin.index')->withInput()->with('success', 'Redefinição enviada, verifique a caixa de entrada do seu email!');
        } else {
            log_message('error', 'Erro ao enviar o e-mail: ' . $email->printDebugger(['headers']));
            return redirect()->back()->withInput()->with('error', 'Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente mais tarde.');
        }
    }

    public function resetPassword(){
        echo View('admin/auth/resetPassword');
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
            return redirect()->route('authAdmin.index')->with('error', 'Ocorreu um erro, refaça o processo.');
        }

        if($password === $cpassword){
            $admin = $this->adminModel->where('email', $email);

            $this->adminModel->update($admin->id, [
                'password' => password_hash((string)$password, PASSWORD_DEFAULT)
            ]);

            return redirect()->route('authAdmin.index')->with('success', 'Senha atualizada com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Senhas não coincidem.');
        }
    }

    public function logout(){
        if(session()->has('admin')){
            unset($_SESSION['admin']);
        }

        return redirect()->route('index')->with('success', 'Deslogado com sucesso!');
    }
}
