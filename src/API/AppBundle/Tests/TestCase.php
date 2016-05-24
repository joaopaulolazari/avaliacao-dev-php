<?php

namespace API\AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class TestCase extends WebTestCase
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;

    protected $client = null;

    protected $user = null;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();

        parent::setUp();
    }

    public function randomString($length = 10, $tipo = 'string')
    {
        $caracteresValidos = "";
        switch ($tipo) {
            case 'string':
                $caracteresValidos = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                break;
            case 'string-alfa':
                $caracteresValidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                break;
            case 'string-int':
                $caracteresValidos = "0123456789";
                break;
        }
        $maxCaracteresValidos = strlen($caracteresValidos);

        $resultado = "";

        for ($i = 0; $i < $length; $i++) {
            $index = mt_rand(0, $maxCaracteresValidos - 1);
            $resultado .= $caracteresValidos[$index];
        }

        return $resultado;
    }

    public function randomEmail()
    {
        return sprintf('%s@meridionalcargas.com.br', $this->randomString(10));
    }

    private function mod($dividendo, $divisor)
    {
        return round($dividendo - (floor($dividendo/$divisor)*$divisor));
    }

    public function randomCpf()
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9*2+$n8*3+$n7*4+$n6*5+$n5*6+$n4*7+$n3*8+$n2*9+$n1*10;
        $d1 = 11 - ($this->mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0 ;
        }
        $d2 = $d1*2+$n9*3+$n8*4+$n7*5+$n6*6+$n5*7+$n4*8+$n3*9+$n2*10+$n1*11;
        $d2 = 11 - ($this->mod($d2, 11));
        if ($d2 >= 10) {
            $d2 = 0;
        }
        return ''.$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$d1.$d2;
    }

    public function randomCnpj()
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = 0;
        $n10= 0;
        $n11= 0;
        $n12= 1;
        $d1 = $n12*2+$n11*3+$n10*4+$n9*5+$n8*6+$n7*7+$n6*8+$n5*9+$n4*2+$n3*3+$n2*4+$n1*5;
        $d1 = 11 - ($this->mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0 ;
        }
        $d2 = $d1*2+$n12*3+$n11*4+$n10*5+$n9*6+$n8*7+$n7*8+$n6*9+$n5*2+$n4*3+$n3*4+$n2*5+$n1*6;
        $d2 = 11 - ($this->mod($d2, 11) );
        if ($d2>=10) {
            $d2 = 0;
        }
        return ''.$n1.$n2.$n3.$n4.$n5.$n6.$n7.$n8.$n9.$n10.$n11.$n12.$d1.$d2;
    }

    protected function geraDadosAleatorios($descricao, $teste)
    {
        if ($teste == 'valido') {
            if (isset($descricao['cnpj'])) {
                return $this->randomCnpj();
            } else if (isset($descricao['email'])) {
                return $this->randomEmail();
            } else if (isset($descricao['cpf'])) {
                return $this->randomCpf();
            }
        }

        switch ($descricao['tipo']) {
            case 'int':
                if (isset($descricao['min']) && $teste == 'min') {
                    return $descricao['min']-1;
                } else if (isset($descricao['max']) && $teste == 'max') {
                    return $descricao['max']+1;
                } else if (isset($descricao['obr']) && $teste == 'obr' && $descricao['obr']) {
                    return ' ';
                } else if ($teste == 'valido') {
                    return rand($descricao['min'], $descricao['max']);
                }
                break;
            case 'string-int':
            case 'string-alfa':
            case 'string':
                if (isset($descricao['min']) && $teste == 'min') {
                    return $this->randomString($descricao['min']-1, $descricao['tipo']);
                } else if (isset($descricao['max']) && $teste == 'max') {
                    return $this->randomString($descricao['max']+1, $descricao['tipo']);
                } else if (isset($descricao['obr']) && $teste == 'obr' && $descricao['obr']) {
                    return ' ';
                } else if ($teste == 'valido') {
                    return $this->randomString(rand($descricao['min'], $descricao['max']), $descricao['tipo']);
                }
                break;
        }

        return false;
    }

    public function montaArrayDeCaracteristicas($array, $teste)
    {
        if (array_key_exists('tipo', $array)) {
            return $this->geraDadosAleatorios($array, $teste);
        }

        $retorno = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $dadoAleatorio = $this->montaArrayDeCaracteristicas($value, $teste);
                if ($dadoAleatorio != false) {
                    $retorno[$key] = $dadoAleatorio;
                }
            }
        }
        return $retorno;
    }


    public function buscaCamposTestados($camposRecursivo, $teste)
    {
        if (array_key_exists($teste, $camposRecursivo) && $camposRecursivo[$teste] !== false) {
            if ($teste == 'min' && $camposRecursivo['min'] == 0 && $camposRecursivo['tipo'] != 'int') {
                return false;
            }
            return true;
        }

        $retorno = array();
        foreach ($camposRecursivo as $key => $value) {
            if (is_array($value)) {
                $resultado = $this->buscaCamposTestados($value, $teste);
                if ($resultado === true) {
                    $retorno[] = $key;
                } else if ($resultado !== false) {
                    $retorno = array_merge($resultado, $retorno);
                }
            }
        }

        return $retorno;
    }

    public function checaSeChaveExiste($arrayDeCaracteristicas, $chave, $recursivo = false)
    {
        if (!$recursivo) {
            return array_key_exists($chave, $arrayDeCaracteristicas);
        }

        if (array_key_exists($chave, $arrayDeCaracteristicas)) {
            return true;
        } else {
            foreach ($arrayDeCaracteristicas as $valor) {
                if (is_array($valor)) {
                    if ($this->checaSeChaveExiste($valor, $chave, true)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function randomDate($start_date = '2015-01-01', $end_date = '2016-01-01')
    {
        $min = strtotime($start_date);
        $max = strtotime($end_date);

        $val = rand($min, $max);

        return date('Y-m-d H:i:s', $val);
    }

    public function randomHour($start_hour = '00:00:01', $end_hour = '23:59:59')
    {
        $min = strtotime($start_hour);
        $max = strtotime($end_hour);

        $val = rand($min, $max);

        return date('H:i:s', $val);
    }

    public function getConstantes($entity)
    {
        $ReflectionClass = new \ReflectionClass($entity);
        return $ReflectionClass->getConstants();
    }

    protected function carregaFixtures(array $arrFixtures)
    {
        $arrFixturesPadrao = $this->getFixturesPadrao();

        if (count($arrFixtures) > 0) {
            $arrFixturesPadrao =  array_unique(array_merge($arrFixturesPadrao, $arrFixtures));
        }
        // $this->loadFixtures($arrFixturesPadrao);
    }

    /**
     * Retorna um objeto para uma rota do sistema
     *
     * @param mixed $nomeDaRota
     * @param array $parametros
     * @return mixed
     */
    protected function getRota($nomeDaRota, $parametros = array())
    {
        // rota para excluir um motorista pelo número ID
        return $this->client->getContainer()->get('router')->generate($nomeDaRota, $parametros);
    }
}
