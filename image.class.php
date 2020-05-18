<?php

/**
 * Classe para corrigir tamanho das imagens,
 * colocar marca d'água e usar o 'cache'.
 */
class Image {

    /**
     * Caminho para a imagem padrão
     * @var string
     */
    private $_defaultPath = 'images/indisponivel.jpg';

    /**
     * Caminho para salvar as imagens (cache)
     * @var string
     */
    private $_savePath = 'images/cache/';

    /**
     * Caminho para a imagem padr�o
     * @var string
     */
    private $_watermarkPath = null;

    /**
     * Caminho para a fonte da imagem
     * @var string
     */
    private $_src = null;

    /**
     * Informações b�sicas sobre o arquivo
     * Basicamente getimagesize();
     * @var array
     */
    private $_info = array();

    /**
     * Flag para salvar ou não um arquivo na pasta de cache
     * @var bool
     */
    private $_canSave = true;

    /**
     * Largura máxima da imagem
     * @var integer
     */
    private $_maxWidth = 800;

    /**
     * Altura máxima para a imagem
     * @var integer
     */
    private $_maxHeight = 600;

    /**
     * Nova largura da imagem
     * @var integer
     */
    private $_newWidth = null;

    /**
     * Nova altura da imagem
     * @var integer
     */
    private $_newHeight = null;

    /**
     * Tipo da imagem
     * @var string
     */
    private $_type = null;

    /**
     * Tipo da saída da imagem
     * @var string
     */
    private $_outputType = null;

    /**
     * Variável para armazenar o canvas
     * @var null
     */
    private $_canvas = null;

    /**
     * Variável para armazenar a imagem
     * @var null
     */
    private $_image = null;

    /**
     * Variável para armazenar a imagem da marca d'�gua
     * @var null
     */
    private $_watermark = null;

    /**
     * Variável para armazenar a cor do background
     * @var array
     */
    private $_backgroundColor = array(0, 0, 0);

    /**
     * Variável para armazenar o zoom/crop
     * @var boolean
     */
    private $_zc = 0;

    /**
     * Variável para armazenar a quantidade de vezes que a imagem será ampliada no zoom
     * @var float
     */
    private $_zoom = 2;

    /**
     * Variável para armazenar o alinhamento
     * @var string
     */
    private $_align = 'center';

    /**
     * Variável para armazenar o hash do arquivo gerado
     * @var string
     */
    private $_hash = null;

    /**
     * Define as informações básicas da imagem
     * @param string $output Define
     */
    public function __construct($output = 'jpeg') {
        $this->_outputType = $output;
    }

    /**
     * Define as dimensões máximas da imagem
     * @param integer $w
     * @param integer $h
     * @return Image
     */
    public function setMaxSize($w = null, $h = null, $zc = 2) {
        $w = (int) $w;
        $h = (int) $h;
        $zc = (float) $zc;

        $this->_maxWidth = (int) $w;
        $this->_maxHeight = (int) $h;
        $this->_zc = (int) $zc;

        return $this;
    }

    /**
     * Define o caminho da imagem
     * @param string $src
     * @return Image
     */
    public function setSource($src = null) {
        if (is_null($src)) {
            return $this;
        }

        if (is_file(realpath('.') . DIRECTORY_SEPARATOR . $src)) {
            $this->_src = $src;
            $this->_canSave = true;
        } elseif (is_file(realpath('.') . DIRECTORY_SEPARATOR . $this->_defaultPath)) {
            $this->_src = realpath('.') . DIRECTORY_SEPARATOR . $this->_defaultPath;
            $this->_canSave = false;
        } elseif ($this->_rootPath != '' && !is_file($this->_src)) {
            $this->error('Imagem não encontrada em: "' . $this->_src . '"', __LINE__);
        } else {
            $this->_src = DIRECTORY_SEPARATOR . $src;
        }
        $this->_info = getimagesize($this->_src);
        $this->_type = $this->getFuncType($this->_info['mime']);

        return $this;
    }

    /**
     * Calcula as novas dimensões da imagem
     */
    private function recalculateSize() {
        /**
         * Calcula a diagonal (width/height)
         */
        $diagonal = ((int) $this->_info[0]) / ((int) $this->_info[1]);

        if ($this->_maxWidth == 0) {
            $this->_maxWidth = $this->_info[0];
        }
        if ($this->_maxHeight == 0) {
            $this->_maxHeight = $this->_info[1];
        }

        if ($diagonal >= 1) {
            /**
             * Se a imagem for "paisagem"
             */
            if ($this->_maxWidth > 0) {
                $this->_newWidth = min($this->_info[0], $this->_maxWidth);
                $this->_newHeight = $this->_newWidth / $diagonal;
            } elseif ($this->_maxWidth == 0 && $this->_maxHeight > 0) {
                $this->_maxWidth = $this->_maxHeight * $diagonal;
            } else {
                $this->_newWidth = $this->_info[0];
            }
        } else {
            /**
             * Se a imagem for "retrato"
             */
            if ($this->_maxHeight > 0) {
                $this->_newHeight = min($this->_info[1], $this->_maxHeight);
                $this->_newWidth = $this->_newHeight * $diagonal;
            } elseif ($this->_maxHeight == 0 && $this->_maxWidth > 0) {
                $this->_maxHeight = $this->_maxWidth / $diagonal;
            } else {
                $this->_newWidth = $this->_info[1];
            }
        }

        if ($this->_newHeight > $this->_maxHeight) {
            $this->_newWidth = $this->_maxHeight * $diagonal;
            $this->_newHeight = $this->_maxHeight;
        }
        if ($this->_newWidth > $this->_maxWidth) {
            $this->_newHeight = $this->_maxWidth / $diagonal;
            $this->_newWidth = $this->_maxWidth;
        }

        if ($this->_zc == 0) {
            $this->_maxWidth = $this->_newWidth;
            $this->_maxHeight = $this->_newHeight;
        } elseif ($this->_zc == 1) {
            $this->_maxWidth = $this->_newWidth;
            $this->_maxHeight = $this->_newHeight;
            $this->_newWidth = $this->_newWidth * $this->_zoom;
            $this->_newHeight = $this->_newHeight * $this->_zoom;
        } elseif ($this->_zc == 2) {
            if (($this->_maxWidth / $this->_maxHeight) < $diagonal) {
                $this->_newHeight = $this->_maxHeight;
                $this->_newWidth = $this->_newHeight * $diagonal;
            } else {
                $this->_newWidth = $this->_maxWidth;
                $this->_newHeight = $this->_newWidth / $diagonal;
            }
        }
    }

    /**
     * Saída para imagem
     */
    public function output() {
        $this->_hash = md5(md5_file($this->_src) . serialize($this)) . '.' . ($this->_outputType == 'jpeg' ? 'jpg' : $this->_outputType);

        if (is_file($this->_savePath . $this->_hash)) {
            $this->_showCachedFile();
        } else {
            $this->recalculateSize();

            header("Content-type: image/{$this->_outputType}");
            $output = 'image' . $this->_outputType;

            $this->_canvas = imagecreatetruecolor($this->_maxWidth, $this->_maxHeight);

            if (!empty($this->_backgroundColor)) {
                $this->backgroundColor();
            }

            if (!empty($this->_src) && is_file($this->_src)) {
                $create = 'imagecreatefrom' . $this->_type;
                $this->_image = $create($this->_src);
            } else {
                $create = 'imagecreatefrom' . $this->_type;
                $this->_image = $create($this->_defaultPath);
            }

            $x = $this->_getAlignX();
            $y = $this->_getAlignY();

            if (!is_null($this->_image)) {
                imagecopyresampled($this->_canvas, $this->_image, $x, $y, 0, 0, $this->_newWidth, $this->_newHeight, $this->_info['0'], $this->_info['1']);
            }

            if (!empty($this->_watermarkPath)) {
                $this->watermark();
            }

            if ($this->_canSave) {
                $save = $this->_savePath . $this->_hash;
                $output($this->_canvas, $save, 100);
                chmod($save, 0777);
            }

            $output($this->_canvas, null, 100);

            imagedestroy($this->_canvas);

            if ($this->_image) {
                imagedestroy($this->_image);
            }

            if ($this->_watermark) {
                imagedestroy($this->_watermark);
            }
        }

        die;
    }

    private function _getAlignX() {
        switch ($this->_align) {
            case strpos($this->_align, 'left') !== false :
                $x = 0;
                break;
            case strpos($this->_align, 'right') !== false :
                $x = $this->_maxWidth - $this->_newWidth;
                break;
            default :
                $x = ($this->_maxWidth - $this->_newWidth) / 2;
                break;
        }
        return $x;
    }

    private function _getAlignY() {
        switch ($this->_align) {
            case strpos($this->_align, 'top') !== false :
                $y = 0;
                break;
            case strpos($this->_align, 'bottom') !== false :
                $y = $this->_maxHeight - $this->_newHeight;
                break;
            default :
                $y = ($this->_maxHeight - $this->_newHeight) / 2;
                break;
        }
        return $y;
    }

    /**
     * Função para definir a cor de fundo da imagem
     * @param string $color
     */
    public function setBackgroundColor($color) {
        if ($color[0] == '#') {
            $color = substr($color, 1);
        }

        if (strlen($color) == 6) {
            list($r, $g, $b) = array($color[0] . $color[1],
                $color[2] . $color[3],
                $color[4] . $color[5]);
        } elseif (strlen($color) == 3) {
            list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
        } else {
            $this->error('A cor designada não pode ser convertida', __LINE__);
        }

        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        $this->_backgroundColor = array($r, $g, $b);
        return $this;
    }

    public function backgroundColor() {
        $color = imagecolorallocate($this->_canvas, $this->_backgroundColor[0], $this->_backgroundColor[1], $this->_backgroundColor[2]);
        imagefill($this->_canvas, 0, 0, $color);
    }

    /**
     * Define o caminho onde a imagem deve ser salva
     * @param string $path
     * @return Image
     */
    public function setSavePath($path) {
        $this->_savePath = $path;
        return $this;
    }

    public function setZoom($zoom = 2) {
        $this->_zoom = (float) $zoom;
        return $this;
    }

    public function setSave($flag = true) {
        $this->_canSave = (bool) $flag;
        return $this;
    }

    public function setAlign($align) {
        $this->_align = $align;
        return $this;
    }

    /**
     * Define o caminho para a imagem padrão
     * @param string $path
     * @return Image
     */
    public function setDefaultPath($path) {
        $this->_defaultPath = $path;

        return $this;
    }

    /**
     * Define o caminho para imagem da marca d'água
     * @param string $path
     * @return Image
     */
    public function setWatermarkPath($path) {
        $this->_watermarkPath = $path;
        return $this;
    }

    /**
     * Aplica marca d'água na imagem
     */
    private function watermark() {
        if (function_exists('imagecopymergegray')) {
            if (!is_file($this->_watermarkPath)) {
                $this->error("Imagem para marca d'água em: '" . $this->_watermarkPath . "'", __LINE__);
            }
            $info = getimagesize($this->_watermarkPath);
            $type = $this->getFuncType($info['mime']);
            $create = 'imagecreatefrom' . $type;
            $this->_watermark = $create($this->_watermarkPath);

            $dest_x = imagesx($this->_canvas) - imagesx($this->_watermark) - 5;
            $dest_y = imagesy($this->_canvas) - imagesy($this->_watermark) - 5;

            imagecopymerge($this->_canvas, $this->_watermark, $dest_x, $dest_y, 0, 0, imagesx($this->_watermark), imagesy($this->_watermark), 50);
        } else {
            $this->error('Função "imagecopymergegray" não suportada', __LINE__);
        }
    }

    private function getFuncType($mime) {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/pjpeg':
            case 'image/jpg':
                return 'jpeg';
                break;
            case 'image/gif':
                return 'gif';
                break;
            case 'image/png':
                return 'png';
                break;
            default:
                $this->error('MIME type não suportado: ' . $mime, __LINE__);
                break;
        }
    }

    public function setCachePath($path) {
        if (!is_dir(realpath('./' . $path))) {
            mkdir('./' . $path, 0777);
            chmod('./' . $path, 0777);
        }
        $this->_savePath = realpath('./' . $path) . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * Exception para erros
     * @param string $msg
     * @param integer $line
     * @param integer $code
     */
    public function error($msg, $line) {
        header('HTTP/1.1 400 Bad Request');
        trigger_error($msg . ' na linha: "' . $line . '"');
        die;
    }

    private function _showCachedFile() {
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            if (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) < strtotime('now')) {
                header('HTTP/1.1 304 Not Modified');
                die();
            }
        }

        if (is_file($this->_savePath . $this->_hash)) {
            $gmdate_expires = gmdate('D, d M Y H:i:s', strtotime('now +10 days')) . ' GMT';
            $gmdate_modified = gmdate('D, d M Y H:i:s', filemtime($this->_savePath . $this->_hash)) . ' GMT';

            header('Content-Type: ' . $this->_info['mime']);
            header('Accept-Ranges: bytes');
            header('Last-Modified: ' . $gmdate_modified);
            header('Content-Length: ' . filesize($this->_savePath . $this->_hash));
            header('Cache-Control: max-age=864000, must-revalidate');
            header('Expires: ' . $gmdate_expires);

            if (!@readfile($this->_savePath . $this->_hash)) {
                $content = file_get_contents($this->_savePath . $this->_hash);
                if ($content != false) {
                    echo $content;
                } else {
                    $this->error('O arquivo de cache não pode ser carregado!', __LINE__);
                }
            }

            die();
        }
        return false;
    }

    public function pe($var = null) {
        if ($var === null) {
            pe($this);
        } else {
            pe($var);
        }
    }

}
