<?php

namespace AppBundle\Entity;

use AppBundle\Form\Extension\Filtro as Filtro;

/**
 * Entidade base com métodos padrões
 */
class EntidadeBase
{
    /**
     * Retorna a entidade como array.
     *
     * @return array
     */
    public function toArray()
    {
        $campos = array();
        foreach (get_class_methods($this) as $metodo) {
            if (preg_match('/^get.*/', $metodo)) {
                $campo = lcfirst(str_replace('get', '', $metodo));
                $campos[$campo] = $this->$metodo();
            }
        }
        return $campos;
    }

    /**
     * Retorna a entidade em cascata como array
     *
     * @return array
     */
    public function toArrayRecursivo()
    {
        $campos = array();
        foreach (get_class_methods($this) as $metodo) {
            if (preg_match('/^get.*/', $metodo)) {
                $campo = lcfirst(str_replace('get', '', $metodo));
                $valor = $this->$metodo();

                if ($valor instanceof \Doctrine\ORM\PersistentCollection) {
                    foreach ($valor as $chave => $row) {
                        $campos[$campo][] = $row->toArray();
                    }
                    continue;
                }

                if ($valor instanceof EntidadeBase) {
                    $campos[$campo] = $valor->toArray();
                } else {
                    $campos[$campo] = $this->$metodo();
                }
            }
        }
        return $campos;
    }

    /**
     * Popular a entidade se existir o método set
     *
     * @param  array  $dados Array com os campos da entidade
     * @return mixed
     */
    public function popular(array $dados)
    {
        foreach ($dados as $metodo => $valor) {
            $metodo = 'set'.ucfirst($metodo);
            if (method_exists($this, $metodo)) {
                $this->$metodo($valor);
            }
        }
        return $this;
    }

    /**
     * Cria a nova entidade
     * @param  Object $entity
     * @param  String $metodo
     * @param  mixed $valor
     * @param  String $namespace
     * @return Object
     */
    private function buscaEntidade($entity, $metodo, $valor, $namespace = null)
    {
        if (is_null($namespace)) {
            $reflection = new \ReflectionClass($entity);
            $namespace = $reflection->getNamespaceName();
        }

        if (is_numeric($metodo)) {
            $namespaceEntidade = get_class($entity);
            $entity = new $namespaceEntidade();
        } else {
            $namespaceEntidade = $namespace . '\\' . key($valor);
            if (class_exists($namespaceEntidade)) {
                $entity = new $namespaceEntidade();
            }
        }
        return $entity;
    }

        /**
     * Popular a entidade recursivo se existir o método set
     *
     * @param  array  $namespace Namespace da entidade
     * @param  array  $dados Array com os campos da entidade
     * @return mixed
     */
    public function popularRecursivo(array $dados, $namespace = null)
    {
        $arrEntidades = array();

        foreach ($dados as $metodo => $valor) {
            if (is_array($valor)) {
                if ($this->arrayDepth($valor) == 1) {
                    $entidade = $this->buscaEntidade($this, $metodo, $valor, $namespace);
                    $arrEntidades[$metodo] = $entidade->popularRecursivo($valor, $namespace);
                } else {
                    $entidade = $this->buscaEntidade($this, $metodo, $valor, $namespace);
                    if (!is_array(reset($valor))) {
                        $arrEntidades[$metodo] = $entidade->popularRecursivo($valor, $namespace);
                    } else {
                        $entidades = $entidade->popularRecursivo(reset($valor), $namespace);
                        $this->setEntity($this, $entidades, $metodo);
                    }
                }
            } else {
                $this->setEntity($this, $valor, $metodo);
            }
        }

        if (count($arrEntidades) > 0) {
            return $arrEntidades;
        }
        return $this;
    }

    /**
     * Retorna a profundidade do array
     * @param  mixed  $array
     * @param  integer $depthCount
     * @param  array   $depthArray
     * @return int
     */
    private function arrayDepth($array, $depthCount = -1, $depthArray = array())
    {
        $depthCount++;
        $depth = 0;
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $depthArray[] = $this->arrayDepth($value, $depthCount);
            }
        } else {
            return $depthCount;
        }

        foreach ($depthArray as $chave => $value) {
            $depth = ($value > $depth)? $value : $depth;
        }

        return $depth;
    }
    // /**
    //  * Popular a entidade recursivo se existir o método set
    //  *
    //  * @param  array  $namespace Namespace da entidade
    //  * @param  array  $dados Array com os campos da entidade
    //  * @return mixed
    //  */
    // public function popularRecursivo(array $dados, $namespace = null)
    // {
    //     foreach ($dados as $metodo => $valor) {
    //         if (count($valor) == 0) {
    //             continue;
    //         }
    //         if (is_array($valor)) {
    //             $entities = array();

    //             if (is_null($namespace)) {
    //                 $reflection = new \ReflectionClass($this);
    //                 $namespace = $reflection->getNamespaceName();
    //             }

    //             if (is_numeric($metodo)) {
    //                 $namespaceEntidade = get_class($this);
    //                 $entidade = new $namespaceEntidade();

    //             } else {
    //                 $namespaceEntidade = $namespace . '\\' . key($valor);
    //                 $entidade = new $namespaceEntidade();
    //             }

    //             foreach ($valor as $chave => $entityVal) {
    //                 $entities = array();
    //                 if (count($entityVal) == 0) {
    //                     continue;
    //                 }
    //                 if (is_array($entityVal)) {
    //                     // Valida a profundidade do array
    //                     if (max(array_map('count', $entityVal)) > 1) {
    //                         foreach ($entityVal as $key => $value) {
    //                             $namespaceEntidade = get_class($entidade);
    //                             $entidade = new $namespaceEntidade();
    //                             $entities[] = $entidade->popularRecursivo($value, $namespace);
    //                         }
    //                     } else {
    //                         $entities[] = $entidade->popularRecursivo($entityVal, $namespace, $metodo);
    //                     }
    //                 } else {
    //                     $entities[] = $entidade->popularRecursivo($entityVal, $namespace, $metodo);
    //                 }
    //             }

    //             foreach ($entities as $entityKey => $entityValue) {
    //                 $this->setEntity($this, $entityValue, $metodo);
    //             }

    //             continue;
    //         }

    //         $this->setEntity($this, $valor, $metodo);
    //     }

    //     return $this;
    // }

    /**
     * Seta o campo na entidade
     * @param Object $entity
     * @param mixed $valor
     * @param String $metodo
     */
    private function setEntity($entity, $valor, $metodo)
    {
        $setMetodo = $this->buscaMetodo($entity, $metodo);

        if (!is_null($setMetodo)) {
            if (is_array($valor)) {
                foreach ($valor as $chave => $val) {
                    $entity->$setMetodo($val);
                }
            } else {
                $entity->$setMetodo($valor);
            }
        }
    }

    /**
     * Busca o método set da entidade
     * @param  Object $entity
     * @param  String $metodo
     * @return String|null
     */
    private function buscaMetodo($entity, $metodo)
    {
        $setMetodo = 'set'.ucfirst($metodo);

        if (method_exists($entity, $setMetodo)) {
            return $setMetodo;
        } else if (method_exists($entity, $metodo)) {
            return $metodo;
        }

        return null;
    }

    /**
     * formatarCpfCnpj: Criado para formatar o cpf e cnpj na listagem de dados
     * @param  $string [Espera uma string com cnpf ou cpf]
     * @return string         [Retorna a string formatada de acordo com seu tipo]
     */
    public function formatarCpfCnpj($string)
    {
        $contador = strlen($string);

        if ($contador == 14) {
            $string = substr($string, 0, 2) . '.' . substr($string, 2, 3) .
                '.' . substr($string, 5, 3) . '/' . substr($string, 8, 4) .
                '-' . substr($string, 12, 2);
        } elseif ($contador == 11) {
            $string = substr($string, 0, 3) . '.' . substr($string, 3, 3) .
                '.' . substr($string, 6, 3) . '-' . substr($string, 9, 2);
        }

        return $string;
    }

    public function formatarNumeroReal($numero, $precisao = 2)
    {
        return sprintf('%.2f', (double) $numero);
    }

    public function filtroAlfanumerico($string, $limite = null)
    {
        if (!is_null($limite)) {
            return $this->limite($this->removeEspacosBranco(Filtro\Alfanumerico::filtrarComEspacos($string, true)), $limite);
        }

        return $this->removeEspacosBranco(Filtro\Alfanumerico::filtrar($string, true));
    }

    public function removeEspacosBranco($string)
    {
        return trim($string);
    }

    public function limite($string, $limite = 15)
    {
        return Filtro\Limite::filtrar($string, $limite);
    }

    public function filtroNumerico($string, $limite = null)
    {
        if (!is_null($limite)) {
            return $this->limite(Filtro\Numerico::filtrar($string, true), $limite);
        }

        return Filtro\Numerico::filtrar($string, true);
    }
}
