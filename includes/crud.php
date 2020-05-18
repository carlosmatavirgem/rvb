<?php

class Crud extends Select {

    public function insert($table, array $array, $toSql = null) {

        $this->db = new Db();

        try {
            foreach ($array as $key => $val) {

                if ($key != 'x' && $key != 'y') {
                    $prepare[":{$key}"] = $val;
                    $column[] = $key;
                    $value[] = ":{$key}";
                }
            }

            $sql = "INSERT INTO "
                    . $table
                    . " (" . implode(",", $column) . ") "
                    . "VALUES (" . implode(",", $value) . ")";

            if (!is_null($toSql)) {
                return Crud::p($sql, true);
            }

            if ($this->db->saveDb($sql, $prepare)) {
                return $this->db->getId();
            }
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function update($table, array $array, $where = '', $toSql = null) {

        $this->db = new Db();
        $param['data'] = array();
        $param['prepare'] = array();

        try {

            foreach ($array as $key => $val) {
                $param['data'][] .= "{$key}=:{$key}";
                $param['prepare'][":{$key}"] = $val;
            }

            $sql = "UPDATE "
                    . $table
                    . " SET " . implode(", ", $param['data'])
                    . (($where) ? " WHERE $where" : '');

            if (!is_null($toSql)) {
                return Crud::p($sql, true);
            }

            $this->db->saveDb($sql, $param['prepare']);

            return true;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public function quote($array) {

        $param['data'] = array();
        $param['prepare'] = array();

        foreach ($array as $key => $val) {

            $val = preg_replace('/(\'|\")/', '', $val);

            if (is_array($val)) {
                foreach ($val as $quote => $value) {
                    $param[] .= "$key=$value";
                }
            } else {
                $param['data'][] .= "{$key}=:{$key}";
                $param['prepare'][":{$key}"] = $val;
            }
        }

        return $param;
    }

    public function delete($table, $array, $toSql = null) {

        $this->db = new Db();

        try {
            foreach ($array as $key => $val) {
                if ($key != 'x' && $key != 'y') {
                    $where[] = "$key='$val'";
                }
            }

            $sql = "DELETE FROM " . $table . " WHERE " . implode(" AND ", $where);

            if (!is_null($toSql)) {
                return Crud::p($sql, true);
            }

            $this->db->saveDb($sql);

            return $this;
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }

    public static function formatDate($param, $time = false) {
        if (strpos($param, ' ')) {
            list($param, $hora) = explode(' ', $param);
        }

        if (strpos($param, '/')) {
            $param = implode('-', array_reverse(explode('/', $param)));
        } else {
            $param = implode('/', array_reverse(explode('-', $param)));
        }
        return $param . ($time !== false ? ' ' . (isset($hora) ? $hora . ':00' : date('H:i:s')) : null);
    }

    public static function formatHora($param, $seg = false) {
        list($param, $hora) = explode(' ', $param);
        return $seg === false ? substr($hora, 0, -3) : $hora;
    }

    public static function dataMesEscrito($m) {
        switch ($m) {
            case 1: $param = "Janeiro";
                break;
            case 2: $param = "Fevereiro";
                break;
            case 3: $param = "Março";
                break;
            case 4: $param = "Abril";
                break;
            case 5: $param = "Maio";
                break;
            case 6: $param = "Junho";
                break;
            case 7: $param = "Julho";
                break;
            case 8: $param = "Agosto";
                break;
            case 9: $param = "Setembro";
                break;
            case 10: $param = "Outubro";
                break;
            case 11: $param = "Novembro";
                break;
            case 12: $param = "Dezembro";
                break;
        }
        return $param;
    }

    public static function dataMesEscritoMine($m) {
        //self::vd($m);
        switch ((string) $m) {
            case '01': $param = "Jan";
                break;
            case '02': $param = "Fev";
                break;
            case '03': $param = "Mar";
                break;
            case '04': $param = "Abr";
                break;
            case '05': $param = "Mai";
                break;
            case '06': $param = "Jun";
                break;
            case '07': $param = "Jul";
                break;
            case '08': $param = "Ago";
                break;
            case '09': $param = "Set";
                break;
            case '10': $param = "Out";
                break;
            case '11': $param = "Nov";
                break;
            case '12': $param = "Dez";
                break;
        }
        return $param;
    }

    public static function dataSemanaEscrito($s) {

        switch ($s) {
            case 0: $param = "Domingo";
                break;
            case 1: $param = "Segunda";
                break;
            case 2: $param = "Terça";
                break;
            case 3: $param = "Quarta";
                break;
            case 4: $param = "Quinta";
                break;
            case 5: $param = "Sexta";
                break;
            case 6: $param = "Sábado";
                break;
        }
        return $param;
    }

    public static function is_cnpj($param) {

        if (!preg_match('|^(\d{2,3})\.?(\d{3})\.?(\d{3})\/?(\d{4})\-?(\d{2})$|', $param, $matches))
            return false;

        array_shift($matches);

        $param = implode('', $matches);

        if (strlen($param) > 14)
            $param = substr($param, 1);

        $sum1 = $sum2 = $sum3 = 0;
        $calc1 = 5;
        $calc2 = 6;

        for ($i = 0; $i <= 12; $i++) {
            $calc1 = $calc1 < 2 ? 9 : $calc1;
            $calc2 = $calc2 < 2 ? 9 : $calc2;

            if ($i <= 11)
                $sum1 += $param[$i] * $calc1;

            $sum2 += $param[$i] * $calc2;
            $sum3 += $param[$i];
            $calc1--;
            $calc2--;
        }

        $sum1 %= 11;
        $sum2 %= 11;

        return ($sum3 && $param[12] == ($sum1 < 2 ? 0 : 11 - $sum1) && $param[13] == ($sum2 < 2 ? 0 : 11 - $sum2)) ? $param : false;
    }

    public static function is_cpf($param) {

        if (!preg_match('|^(\d{3})\.?(\d{3})\.?(\d{3})\-?(\d{2})$|', $param, $matches))
            return false;

        array_shift($matches);

        $param = implode('', $matches);

        for ($i = 0; $i < 10; $i++)
            if ($param == str_repeat($i, 11))
                return false;

        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++)
                $d += $param[$c] * ($t + 1 - $c);

            $d = ((10 * $d) % 11) % 10;

            if ($param[$c] != $d)
                return false;
        }

        return $param;
    }

    public static function download($file, $module) {

        $module = str_replace('-', '/', $module);

        $dir = DIRROOT . "/images/arquivos/{$module}/";
        $file = $dir . $file;

        if (!file_exists($file))
            exit('Operação não permitida.');

        header('Content-type: octet/stream');
        header('Content-disposition: attachment; filename="' . basename($file) . '";');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    /**
     * Reduz o texto sem cortar a ultima palavra
     * @param APPEND - Caso queria usar algun tipo de caracter no final
     * da String - Ex.: "..."
     */
    public static function str_reduce($string, $max_length, $append = null) {

        $string = substr($string, 0, $max_length);
        $append = !is_null($append) && strlen($string) >= $max_length ? $append : null;

        if (strlen($string) >= $max_length)
            $string = substr($string, 0, strrpos($string, ' '));

        return $string . $append;
    }

    public static function _redirect($url) {
        header("Location: $url");
    }

    public static function vd($n, $exit = false) {
        echo "<pre>";
        var_dump($n);
        echo "</pre>";
        echo "<hr>";
        if ($exit === true)
            exit;
    }

    public static function p($n, $exit = false) {
        echo "<pre>";
        print_r($n);
        echo "</pre>";
        echo "<hr>";
        if ($exit === true)
            exit;
    }

}
