<?php

class Auth {

    public $table = 'membro';

    public function authenticate(array $param, $http_referer = false) {

        /**
         *  Valida os dados do formulario
         */
        if ($param['login']) {

            $db = new Db();
            $slt = new Select();

            $email = mysql_real_escape_string($param['login']['email'], $db->_resource);
            $senha = mysql_real_escape_string($param['login']['senha'], $db->_resource);

            $result = $slt->select()
                    ->column("idMembro, nome, email, senha, filiacao, ativo")
                    ->from($this->table)
                    ->where(" WHERE senha = MD5('" . $senha . "') AND email = LOWER('" . $email . "') AND ativo = 1")
                    ->query();

            if (count($result) == 1) {

                $_SESSION['s_ativo'] = $result[0]['ativo'];
                $_SESSION['s_id'] = $result[0]['idMembro'];
                $_SESSION['s_nome'] = $result[0]['nome'];
                $_SESSION['s_email'] = $result[0]['email'];
                $_SESSION['s_filiacao'] = $result[0]['filiacao'];

                if ($http_referer === false) {
                    if ($result[0]['senha'] == 'e5d47f9db1bcba0ad3c30c94c1dab5bc') {
                        Crud::_redirect(WWWROOT . "/admin/primeiroacesso");
                    } else {
                        Crud::_redirect('http://' . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI']);
                    }
                } else {
                    if (isset($_SESSION['inscricao_id'])) {
                        new Signup($_SESSION);
                    } else {
                        unset($_SESSION['http_referer']);
                        Crud::_redirect($http_referer);
                    }
                }
            }
        }
    }

    public function passwordGenerator(array $param) {

        /**
         *  Valida os dados do formulario
         */
        if ($param['password']) {

            $db = new Db();
            $crud = new Crud();

            $data = null;

            $email = mysql_real_escape_string($param['password']['email'], $db->_resource);
            $cpf = mysql_real_escape_string($param['password']['cpf'], $db->_resource);

            if (Crud::is_cpf($cpf)) {
                $result = $crud->select()
                        ->column("idMembro, nome, email")
                        ->from($this->table)
                        ->where("WHERE cpf = '" . $cpf . "' AND email = LOWER('" . $email . "')")
                        ->query();

                if (count($result) == 1) {

                    $chave = self::password(8, false, true, true, false);

                    $data['chave'] = $chaveMD5 = MD5($chave);
                    $data['ativo'] = 2;

                    $to = utf8_decode(EMAIL);
                    $subject = utf8_decode('Alteração de senha FBN Brasil');

                    $message = utf8_decode("Olá {$result[0]['nome']},\n\r\n\rVocê solicitou uma nova senha. Por medida de segurança, não enviamos senhas por e-mail. Você deve criar uma nova senha para substituir a antiga. Siga os passos abaixo e assim que a nova senha for confirmada a antiga será anulada.\n\r
                    1. Clique no link para trocar de senha:
                    " . WWWROOT . "/nova-senha/{$chaveMD5}\n\r
                    2. Preencha todos os campos solicitados. Atenção: a nova senha deve ter entre 5 e 8 caracteres.\n\r\n\rAtenciosamente,\n\rEquipe FBN Brasil.");

                    $headers = "From: " . utf8_decode($result[0]['nome']) . " <{$result[0]['email']}>\n\r";

                    if (mail($to, $subject, $message, $headers)) {
                        $crud->update($this->table, $data, "idMembro = " . $result[0]['idMembro']);
                        Crud::_redirect(WWWROOT . "/acesso-restrito/esqueci-senha/sucesso");
                    }
                    return true;
                }
            }
            return false;
        }
    }

    public function passwordNew(array $param, $chave = null) {

        /**
         *  Valida os dados do formulario
         */
        if ($param['senha']) {

            $db = new Db();
            $crud = new Crud();

            $data = null;

            $cpf = mysql_real_escape_string($param['senha']['cpf'], $db->_resource);
            $senha = mysql_real_escape_string($param['senha']['senha'], $db->_resource);

            if (Crud::is_cpf($cpf)) {
                $result = $crud->select()
                        ->column('idMembro')
                        ->from($this->table)
                        ->where("WHERE chave = '$chave' AND cpf = '$cpf' AND ativo = 2")
                        ->query();

                if (count($result) == 1) {

                    $data['senha'] = MD5($senha);
                    $data['ativo'] = 1;

                    $crud->update($this->table, $data, "chave = '$chave'");
                    Crud::_redirect(WWWROOT . "/nova-senha/sucesso");
                    return true;
                }
            }
            return false;
        }
    }

    private function password($size, $maiuscula, $minuscula, $numeros, $codigos) {

        $password = $base = null;

        $base .= ($maiuscula) ? "ABCDEFGHIJKLMNOPQRSTUWXYZ" : '';
        $base .= ($minuscula) ? "abcdefghijklmnopqrstuwxyz" : '';
        $base .= ($numeros) ? "0123456789" : '';
        $base .= ($codigos) ? '!@#$%-+:|' : ''; //!@#$%&*()-+.,;?{[}]^><:|

        srand((float) microtime() * 10000000);

        for ($i = 0; $i < $size; $i++) {
            $password .= substr($base, rand(0, strlen($base) - 1), 1);
        }
        return $password;
    }

}
