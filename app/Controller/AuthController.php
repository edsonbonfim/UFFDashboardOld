<?php

namespace App\Controller;

use Bonfim\Application\Controller;

class AuthController extends Controller
{
    public function logout()
    {
        unset($_SESSION['uff']);
        unset($_SESSION['login']);
        unset($_SESSION);
        session_destroy();
        header('Location: /');
        exit;
    }

    public function login($request)
    {
        $iduff = $request->getArgs('iduff');
            
        if (empty($iduff)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Não iremos conseguir sem o CPF :(';
            header('Location: /');
            exit;
        }

        if (empty($request->getArgs('senha'))) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Não iremos conseguir sem a senha :(';
            header('Location: /');
            exit;
        }

        if (!is_numeric($iduff)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Por favor, o CPF só deve conter numeros.';
            header('Location: /');
            exit;
        }

        $iduff = preg_replace( '/[^0-9]/is', '', $iduff);

        if (strlen($iduff) != 11) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Um CPF é composto por 11 numeros.';
            header('Location: /');
            exit;
        }

        if (preg_match('/(\d)\1{10}/', $iduff)) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Tentando nos enganar com esse CPF?';
            header('Location: /');
            exit;
        }

        // Calculo para validar o CPF
        for ($i = 9; $i < 11; $i++) {
            for ($k = 0, $c = 0; $c < $i; $c++) {
                $k += $iduff{$c} * (($i + 1) - $c);
            }
            $k = ((10 * $k) % 11) % 10;
            if ($iduff{$c} != $k) {
                $_SESSION['login'] = 'failed';
                $_SESSION['message'] = 'CPF inválido.';
                header('Location: /');
                exit;
            }
        }

        // Chamada externa para scraping da pagina da UFF
        exec("./UFF '{$iduff}' '{$request->getArgs('senha')}'", $output, $return_var);

        // Connection timed out
        if ($return_var == 110) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Tempo de conexão esgotado.';
            header('Location: /');
            exit;
        }

        if (count($output) == 0) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'Ocorreu um erro, tente novamente.';
            header('Location: /');
            exit;
        }

        $uff = json_decode($output[0]);

        if ($uff->status == -1) {
            $_SESSION['login'] = 'failed';
            $_SESSION['message'] = 'CPF ou senha inválidos.';
            header('Location: /');
            exit;
        }

        $_SESSION['login'] = 'success';
        $_SESSION['uff'] = $uff;
        header('Location: /');
        exit;
    }
}
