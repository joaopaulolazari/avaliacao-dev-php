<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Mink;
use Behat\Mink\WebAssert;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements KernelAwareContext
{
    private $kernel;
    private $parameters;
    public $array_campos_representante;

    //Defino uma constante de timeout em segundos que será usada em definições de tempo máximo de espera (multiplicada por 1000 quando apropriado).
    //Esta constante pode mudar conforme a performance esperada do ambiente de teste. Aumentar para ambientes mais lentos.
    const TIMEOUT = 40;

    /**
     * Helps to use doctrine and entity manager.
     *
     * @param KernelInterface $kernelInterface Interface for getting Kernel.
     */
    public function setKernel(KernelInterface $kernelInterface)
    {
        $this->kernel = $kernelInterface;
    }

    /**
     * Abre página inicial, logado como usuário especificado. Usuário usa a senha padrão.
     * @Given /^(?:|que )eu estou logado com(?:|o) (?:|o usuário )"(?P<usuario>[^"]+)"$/
     * @Given /^(?:|que )eu estou logado com(?:|o) (?:|o usuário )"(?P<usuario>[^"]+)" e senha "(?P<senha>[^"]+)"$/
     */
    public function euEstouLogadoComo($usuario, $senha = "123")
    {
        $this->visit("/login");

        try {
            $this->euDeveriaVer("2015 Meridional Cargas");
        } catch (Exception $e) {
            throw new Exception("Você pode acessar a tela de login.");
        }

        $this->preencheCampo("username", $usuario);
        $this->preencheCampo("password", $senha);
        $this->clicaBotao("Entrar");
        //Aguarda o login e o redirecionamento para home
        $this->aguardaXpath('//h3[contains(.,"Início")]');

        try {
            $this->euDeveriaVer("Início");
        } catch (Exception $e) {
            throw new Exception("Não conseguiu logar com o usuário ".$usuario." e a senha ".$senha.".");
        }
    }

    /**
     * Verifica que a página contém o texto.
     *
     * @Then /^eu deveria ver (?:|a mensagem |o texto )"(?P<text>(?:[^"]|\\")*)"$/
     */
    public function euDeveriaVer($text)
    {
        $text = $this->buscaParametros($text);
        return $this->assertPageContainsText($text);
    }

    /**
     * Espera um tempo em segundos
     * @When /^eu espero (?<tempo>[0-9]+)(?:| segundo(?:|s))$/
     */
    public function euEspero($tempo)
    {
        $this->getSession()->wait($tempo*1000);
    }


    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(Session $session)
    {
    }

    /**
     * Abre página.
     * @When /^(?:|que )eu (vou para a|acesso a) página "(?P<page>[^"]+)"$/
     * @Given /^(?:|que )eu (vou|acesso) (?:|em |na |no |a |o )"(?P<page>[^"]+)"$/
     */
    public function euVouParaAPagina($page)
    {
        // $pages = $this->_pages;
        // if (isset($pages[$page])) {
        //     $page = $pages[$page];
        // }

        $retorno = $this->visit($page);
    }

    /**
     * Desce scroll mouse para testes em monitores menores
     *
     * @Then /^eu desco o scroll do mouse/
     */
    public function desceFimPagina()
    {
        $this->getSession()->executeScript("$('body').scrollTop($(document).height());");
    }

    /**
     * Define cookie para manter o menu aberto durante os testes
     *
     * @Then /^eu mantenho o menu aberto$/
     */
    public function mantemMenuAberto()
    {
        $this->getSession()->executeScript('$(".sb-collapsed #sidebar-collapse").click(); ');
        setcookie("sideBarCollapsed", "true", time() + 86400, "/");
    }

    /**
     * Executa submit no formulario via form name
     *
     * @Then /^eu envio o formulario "(?P<formulario>(?:[^"]|\\")*)"$/
     */
    public function enviaFormulario($formulario)
    {
        $this->getSession()->executeScript("$(\"form[name='".$formulario."']\").find(\"input:first\").change();
            $(\"form[name='".$formulario."']\").find(\"button[type='submit']\").click();");
    }

    /**
     * Acionar o botão com identificador específico: id|nome|título|alt|valor.
     *
     * @When /^eu clico no botão "(?P<button>(?:[^"]|\\")*)"$/
     * @When /^eu clico em "(?P<button>(?:[^"]|\\")*)"$/
     * @When /^eu aciono o botão "(?P<button>(?:[^"]|\\")*)"$/
     */
    public function clicaBotao($button)
    {
        $button = $this->fixStepArgument($button);
        $this->getSession()->getPage()->pressButton($button);
    }

    /**
     * Clica em um link identificado por id do menu.
     *
     * @When /^eu clico no menu pelo id "(?P<link>(?:[^"]|\\")*)"(?:| da tabela ativa)$/
     */
    public function clicaMenuId($link)
    {
        $el = $this->getSession()->getPage()->find(
            'xpath',
            "//a[contains(@id-rota,'".$link."')]"
        );

        if ($el == null) {
            throw new Exception('Não foi possível encontrar um link identificado pelo ID "'.$link.'".');
        }

        $el->click();
    }


    /**
     * Clica em um link identificado por id|título|alt|texto na tabela de trecho ativa.
     *
     * @When /^eu clico no link "(?P<link>(?:[^"]|\\")*)"(?:| da tabela ativa)$/
     */
    public function clicaLink($link)
    {
        $el = $this->getSession()->getPage()->find(
            'xpath',
            "//a[contains(@id,'".$link."') or contains(@name,'".$link."') or contains(@title,'".$link."')
            or contains(.,'".$link."') or contains(@href, '".$link."') or contains(@alt, '".$link."')]"
        );

        if ($el == null) {
            throw new Exception('Não foi possível encontrar um link identificado por "'.$link.'".');
        }

        $el->click();
    }

    /**
     * Clica em um campo, especificado por nome ou ID ou valor ou texto.
     * Esta função foi escrita para tratar campos com comportamento especial quando o foco está neles. Os testes devem rodar em segundo plano.
     * @When /^eu clico no campo "(?P<campo>[^"]+)"$/
     */
    public function euClicoNoCampo($campo)
    {
        $el = $this->getSession()->getPage()->find('xpath', '//input[@id="'.$campo.'" or @value="'.$campo.'" or @name="'.$campo.'" or contains(.,"'.$campo.'")]');

        if ($el == null) {
            throw new Exception('Não foi possível encontrar um campo com nome, ID, valor ou texto igual a "'.$campo.'".');
        }

        $el->mouseOver();
        $this->getSession()->wait(500);
        $el->click();
    }

    /**
     * Preenche campo identificado por: id|nome|label|valor.
     *
     * @When /^eu preencho (?:|o campo )"(?P<campo>(?:[^"]|\\")*)" com (?:|o valor )"(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function preencheCampo($campo, $valor)
    {
        $this->euClicoNoCampo($campo);
        $campo = $this->fixStepArgument($campo);

        $valor = $this->fixStepArgument($this->buscaParametros($valor));
        $this->getSession()->getPage()->fillField($campo, $valor);
    }

    /**
     * @When /^eu preencho o typeahead "(?P<campo>(?:[^"]|\\")*)" com o valor "(?P<valor>(?:[^"]|\\")*)" e seleciono a opção "(?P<opcao>(?:[^"]|\\")*)"$/
     */
    public function preencheTypeahead($campo, $valor, $opcao)
    {
        $this->getSession()->executeScript("jQuery('#".$campo."').val('".$valor."').change();");
        if (!$this->getSession()->wait(10000, '0 != jQuery("#'.$campo.'").siblings("ul").children("li").length')) {
            throw new Exception('Tempo de espera máximo do typeAhead alcançado');
        }

        $el = $this->getSession()->getPage()->find('xpath', '//input[@id=\''.$campo.'\']/following-sibling::ul/li['.$opcao.']');

        if ($el->getText() == 'Nenhum cliente encontrado') {
            throw new Exception('O typeAhead não retornou nenhum resultado');
        }
        $el->click();
    }

    /**
     * Seleciona uma opção em um campo, especificado por id|nome|label|valor.
     * OU seleciona uma opção única (ou a primeira opção com aquele texto).
     *
     * @When /^eu fecho o modal$/
     */
    public function fechoOModal()
    {
        $this->getSession()->executeScript("$('button.close').click();");
    }

    /**
     * Seleciona uma opção em um campo, especificado por id|nome|label|valor.
     * OU seleciona uma opção única (ou a primeira opção com aquele texto).
     *
     * @When /^(?:|eu )(?:seleciono|escolho) a opção dinamica "(?P<option>(?:[^"]|\\")*)"(?:| (?:em|no campo) "(?P<select>(?:[^"]|\\")*)")$/
     */
    public function selecionaOpcaoDinamicamente($option, $select = null)
    {
        $this->getSession()->executeScript("
            $('#".$select." option').filter(function() {
                return ($(this).text() == '".$option."');
            }).prop('selected', true).change();");
    }

        /**
     * Seleciona uma opção em um select
     *
     * @When /^(?:|eu )seleciono as coletas "(?P<coletas>(?:[^"]|\\")*)"$/
     */
    public function selecionaColetas($coletas)
    {
        if (!is_array($coletas)) {
            $coletas = explode(",", $coletas);
        }

        foreach ($coletas as $coleta) {
            $this->getSession()->executeScript("$(\"input[value='coleta.fnColetaId']:eq(".$coleta.")\").click();");
        }
        $this->euEspero(1);
    }

    /**
     * Seleciona uma opção em umm select
     *
     * @When /^(?:|eu )clico (?:|na opção |no valor )"(?P<option>(?:[^"]|\\")*)"(?:| (?:em|no campo) "(?P<select>(?:[^"]|\\")*)")$/
     */
    public function clicaOpcao($option, $select = null)
    {
        if ($select != null) {
            $elementoSelect = $this->getSession()->getPage()->find('xpath', '//select[contains(@id,"'.$select.'") or contains(@name,"'.$select.'")]');
            $elementoOption = $this->getSession()->getPage()->find('xpath', '//select[contains(@id,"'.$select.'") or contains(@name,"'.$select.'")]/option[contains(.,"'.$option.'")]');
        } else { //Se eu não defini um argumento $select, eu defino ele como o campo de seleção que possui uma opção $option.
            $elementoSelect = $this->getSession()->getPage()->find('xpath', '//select[option[contains(.,"'.$option.'")]]');
            $elementoOption = $this->getSession()->getPage()->find('xpath', '//select/option[contains(.,"'.$option.'")]');
        }


        if ($elementoSelect == null) {
            throw new Exception("Impossível encontrar um campo de seleção ".$select.".");
        }

        if ($elementoOption == null) {
            throw new Exception("Impossível encontrar opção com texto ".$option.".");
        }

        $elementoOption->doubleClick($option);
    }

    /**
     * Seleciona uma opção em um campo, especificado por id|nome|label|valor.
     * OU seleciona uma opção única (ou a primeira opção com aquele texto).
     *
     * @When /^(?:|eu )(?:seleciono|escolho) (?:|a opção |o valor )"(?P<option>(?:[^"]|\\")*)"(?:| (?:em|no campo) "(?P<select>(?:[^"]|\\")*)")$/
     */
    public function selecionaOpcao($option, $select = null)
    {
        if ($select != null) {
            $elementoSelect = $this->getSession()->getPage()->find('xpath', '//select[contains(@id,"'.$select.'") or contains(@name,"'.$select.'")]');
            $elementoOption = $this->getSession()->getPage()->find('xpath', '//select[contains(@id,"'.$select.'") or contains(@name,"'.$select.'")]/option[contains(.,"'.$option.'")]');
        } else { //Se eu não defini um argumento $select, eu defino ele como o campo de seleção que possui uma opção $option.
            $elementoSelect = $this->getSession()->getPage()->find('xpath', '//select[option[contains(.,"'.$option.'")]]');
            $elementoOption = $this->getSession()->getPage()->find('xpath', '//select/option[contains(.,"'.$option.'")]');
        }


        if ($elementoSelect == null) {
            throw new Exception("Impossível encontrar um campo de seleção ".$select.".");
        }

        if ($elementoOption == null) {
            throw new Exception("Impossível encontrar opção com texto ".$option.".");
        }

        $elementoSelect->selectOption($option);
    }

    /**
     * Verifica que a página NÃO contém o texto.
     *
     * @Then /^eu não deveria ver (?:|a mensagem |o texto )"(?P<text>(?:[^"]|\\")*)"$/
     */
    public function euNaoDeveriaVer($text)
    {
        return $this->assertPageNotContainsText($text);
    }

    /**
     * Verifica que a página NÃO contem o campo
     *
     * @Then /^eu não deveria ver (?:|o campo |o elemento )"(?P<text>(?:[^"]|\\")*)" do tipo "(?P<tipo>(?:[^"]|\\")*)"$/
     */
    public function euNaoDeveriaVerOCampo($nome, $tipo)
    {
        // Verifica se existe algum campo select mostrando a opção desejada.
        $expXpath = "//input[@type='".$tipo."' and (contains(@id,'".$nome."') or contains(@name,'".$nome."'))]";

        $input = $this->getSession()->getPage()->find(
            'xpath',
            $expXpath
        );

        if (!is_null($input) && $input->isVisible()) {
            throw new Exception('O campo/elemento '.$nome. ' do tipo '.$tipo.' está visível');
        }
    }

    /**
     * Verifica se a tabela contem a tabela passada.
     *
     * @Then /^na tabela "(?P<tabela>[^"]+)" deve conter:/
     */
    public function verificaConteudoTabela($tabela, TableNode $tableNode)
    {
        //Pega o elemento da tabela passada
        $tabela = $this->getSession()->getPage()->find('xpath', '//table[@id="'.$tabela.'" or @class="'.$tabela.'" or contains(.,"'.$tabela.'")]');

        //Lê argumento tabelado.
        $hash = $tableNode->getHash();

        //Verifica cabeçalho da tabela
        foreach (array_keys($hash[0]) as $key => $cabecalho) {
            $ths = $tabela->findAll('css', 'thead>tr>th');

            if ($ths[$key]->getText() != $cabecalho) {
                throw new Exception("Cabeçalho" . $cabecalho . "não encontrado na tabela ");
            }
        }
        //Percorre as linhas da tabela comparando o conteúdo
        $trs = $ths = $tabela->findAll('css', 'tbody>tr');
        foreach ($trs as $key => $tr) {
            $tds = $tr->findAll('css', 'td');
            $count = 0;
            foreach ($hash[$key] as $td) {
                $td = $this->buscaParametros($td);

                if (preg_match("/#(.*?)#/", $td, $resultados)) {
                    if (!preg_match("#".$resultados[1]."#", $tds[$count]->getText())) {
                        throw new Exception("O valor " . $tds[$count]->getText() . " não atende a expressão ".$resultados[1]);
                    }
                } else if ($tds[$count]->getText() != $td) {
                    throw new Exception("O valor " . $td . " não encontrado na tabela ");
                }
                $count++;
            }
        }
    }

    /**
     * FUNÇÃO DE TESTE: espera um elemento javascript aparecer na tela. Determinar o tipo de elemento, um atributo identificador, e um valor.
     * @Given /^(?:|que )eu espero (?:|(?<tempo>[0-9]+) segundos (?:|pel))o elemento "(?P<elemento>[^"]+)" com atributo "(?P<atributo>[^"]+)" valor "(?P<valor>[^"]+)"(?:| carregar| aparecer)$/
     */
    public function euEsperoOElementoAparecer($elemento, $atributo, $valor, $tempo)
    {
        if (empty($tempo)) {
            $tempo = self::TIMEOUT;
        }

        $this->getSession()->wait($tempo*1000, '0 != jQuery(\''.$elemento.'['.$atributo.'="'.$valor.'"]\').length');

        if ($this->getSession()->getPage()->find('xpath', '//'.$elemento.'[@'.$atributo.'="'.$valor.'"]') == null) {
            throw new Exception("O elemento procurado não apareceu em ".$tempo." segundos.");
        }

        //Outras funções que podem ajudar no aguardo:
        //
        //$this->getSession()->wait($tempo*1000, '0 != jQuery("#'.$elemento.'").length');
        //jQuery("#ID .CLASS_ELEMENTO").length;
        //$this->getSession()->wait($tempo*1000, 'jQuery(li[data-value="'.$elemento.'"]).length');
        //$this->getSession()->wait($tempo*1000, "false === jQuery('#cliente #icone').hasClass('fa-spinner')");
        //$this->getSession()->wait($tempo*1000, "(0 === jQuery.active && 0 === jQuery(':animated').length)");
        //$this->getSession()->wait($tempo*1000, null !== $this->getSession()->getPage()->find('xpath', '//'.$tipo.'[contains(.,"'.$elemento.'")]'));
        //$this->assertElementContainsText('#modal-from-dom .modal-header h3', $title);
        //assertTrue($this->getSession()->getPage()->find('css', '#modal-from-dom')->isVisible());
    }

    /**
     * FUNÇÃO DE TESTE: espera um elemento javascript sumir da tela
     * @Given /^(?:|que )eu espero (?:|(?<tempo>[0-9]+) segundos (?:|pel))o elemento "(?P<elemento>[^"]+)" com atributo "(?P<atributo>[^"]+)" valor "(?P<valor>[^"]+)"(?: sumir| sair da tela)$/
     */
    public function euEsperoOElementoSumir($elemento, $atributo, $valor, $tempo)
    {
        if (empty($tempo)) {
            $tempo = self::TIMEOUT;
        }

        $this->getSession()->wait($tempo*1000, '0 == jQuery(\''.$elemento.'['.$atributo.'="'.$valor.'"]\').length');

        if ($this->getSession()->getPage()->find('xpath', '//'.$elemento.'[@'.$atributo.'="'.$valor.'"]') != null) {
            throw new Exception("O elemento aguardado não saiu da tela em ".$tempo." segundos.");
        }
    }

    /**
     * Espera o elemento de "Carregando..." aparecer e depois sumir, isto é, não continua o teste até que o ícone saia da tela.
     * @When /^(?:|que )eu (?:espero|aguardo) (?:|(?<tempo>[0-9]+) segundos (?:|pel))a mensagem de Carregando$/
     */
    public function euEsperoCarregando($tempo = self::TIMEOUT)
    {
        if (empty($tempo)) {
            $tempo = self::TIMEOUT;
        }

        //Espera a mensagem de "Carregando..." aparecer
        $this->getSession()->wait($tempo*1000, "true == jQuery('#overlay').is(':visible')");

        //O ícone apareceu?
        if ($this->getSession()->getPage()->find('xpath', '//div[contains(@id,"overlay") and not(contains(@style,"display: none"))]') == null) {
            throw new Exception("O overlay de Carregando... não apareceu na tela em ".$tempo." segundos.");
        }

        //Espera o ícone de loading sumir
        $this->getSession()->wait($tempo*1000, "false == jQuery('#overlay').is(':visible')");

        //O ícone de loading sumiu?
        if ($this->getSession()->getPage()->find('xpath', '//div[contains(@id,"overlay") and not(contains(@style,"display: none"))]') != null) {
            throw new Exception("O overlay de Carregando... não saiu da tela em ".$tempo." segundos.");
        }

        $this->getSession()->wait(1000);
    }

    /**
     * Aguarda um elemento xpath sumir
     *
     * @When /^eu aguardo o xpath "(?P<xpath>[^"]+)" sumir$/
     */
    public function aguardaXpathSumir($xpath)
    {
        // @WIP
        while (null != $this->getSession()->getPage()->find('xpath', $xpath)) {
            // Aguarda o xpath sumir da tela
        }
    }

    /**
     * Aguarda um elemento xpath aparecer
     *
     * @When /^eu aguardo o xpath "(?P<xpath>[^"]+)"$/
     */
    public function aguardaXpath($xpath)
    {
        // @WIP
        while (null == $this->getSession()->getPage()->find('xpath', $xpath)) {
            // Aguarda o xpath aparecer na tela
        }
    }

    /**
     * Verifica se uma opção está selecionada em um campo especificado.
     *
     * @Then /^o campo "(?P<select>(?:[^"]|\\")*)" deveria mostrar a opção de valor "(?P<opcao>(?:[^"]|\\")*)" e texto "(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function verificaOpcaoNoCampo($select, $option, $texto)
    {
        // Verifica se existe algum campo select mostrando a opção desejada.
        try {
            $campo = $this->getSession()->getPage()->find(
                'xpath',
                "//select[contains(@id,'".$select."') or contains(@name,'".$select."')]"
            );
        } catch (Exception $e) {
            throw new Exception("Não foi possível encontrar o campo ".$select.".");
        }

        $opcao = $campo->find('named', array('option', $option));

        if (is_null($opcao)) {
            throw new Exception("A opção " . $option . " não foi encontrada");
        }
        if ($opcao->getText() != $texto) {
            throw new Exception("O texto da opção " . $option . " não é igual a " . $texto);
        }
    }

    /**
    * Verifica se existe o DROPZONE com o texto
    *
    * @Then /^eu deveria ver o dropzone com o texto "(?P<texto>(?:[^"]|\\")*)"$/
    **/
    public function euDeveriaVerODropzone($texto)
    {
        $dropzone = $this->getSession()->getPage()->find(
            'xpath',
            "//div[contains(@class,'dropzone ng-pristine ng-untouched ng-valid ng-isolate-scope dz-clickable') and contains(.,'".$texto."')]"
        );

        if ($dropzone == null) {
            throw new Exception('Não foi possível encontrar o dropzone com o texto '.$texto);
        }
    }

    /**
     * Verifica o alertify de erro que contem um texto
     *
     * @Then /^eu deveria ver alertify de erro com (?:|a mensagem |o texto )"(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function euDeveriaVerAlertifyErro($texto)
    {
        $this->getSession()->wait(500);
        $alertify = $this->getSession()->getPage()->find(
            'xpath',
            "//article[contains(@class,'alertify-log alertify-log-error alertify-log-show') and contains(.,'".$texto."')]"
        );

        if ($alertify == null) {
            throw new Exception('Não foi possível encontrar a mensagem/texto"'.$texto.'" no alertify.');
        }

        $alertify->click();
    }

    /**
     * Verifica erro de formulário que contem um texto
     *
     * @Then /^no campo "(?P<campo>(?:[^"]|\\")*)" eu deveria ver (?:|a mensagem |o texto )de erro "(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function noCampoErro($campo, $texto)
    {
        $this->getSession()->wait(500);

        $texto = $this->buscaParametros($texto);
        // Remove as aspas duplas escapadas
        $texto = $this->fixStepArgument($texto);

        $erro = $this->getSession()->getPage()->find(
            'css',
            'div[form*="'.$campo.'"], div[form-group="'.$campo.'"]'
        );

        if ($erro == null) {
            throw new Exception('Não foi possível encontrar o campo "'.$campo.'"');
        }

        if (strstr($erro->getHtml(), $texto) === false) {
            throw new Exception('Não foi possível encontrar no campo "'.$campo.'" a mensagem/texto "'.$texto.'"');
        }
    }

    protected function calcularData($data, $intervalo = false, $operacao = '+', $formato = 'd/m/Y')
    {
        if ($data === 'now') {
            $data = new \DateTime('now');
        }

        if ($intervalo) {
            $acrescimo = new \DateInterval($intervalo);

            if ($operacao === '+') {
                $data->add($acrescimo);
            } else if ($operacao === '-') {
                $data->sub($acrescimo);
            }
        }

        return $data->format($formato);
    }

    protected function buscaParametros($texto)
    {
        $resultado = '';
        if (preg_match('#\<hoje([\+\-]){0,1}(P.*){0,1}\>#', $texto, $resultado)) {
            $data = '';
            if (isset($resultado[2])) {
                $data = $this->calcularData('now', $resultado[2], $resultado[1]);
            } else {
                $data = $this->calcularData('now');
            }
            return preg_replace('#\<hoje([\+\-]){0,1}(P.*){0,1}\>#', $data, $texto);

        } if (preg_match('#\<agoraHora([\+\-]){0,1}(P.*){0,1}\>#', $texto, $resultado)) {
            $data = '';
            if (isset($resultado[2])) {
                $data = $this->calcularData('now', $resultado[2], $resultado[1], 'H:i');
            } else {
                $data = $this->calcularData('now', false, '+', 'H:i');
            }
            return preg_replace('#\<agoraHora([\+\-]){0,1}(P.*){0,1}\>#', $data, $texto);

        } if (preg_match('#\<agoraDataHora([\+\-]){0,1}(P.*){0,1}\>#', $texto, $resultado)) {
            $data = '';
            if (isset($resultado[2])) {
                $data = $this->calcularData('now', $resultado[2], $resultado[1], 'd/m/Y H:i');
            } else {
                $data = $this->calcularData('now', false, '+', 'd/m/Y H:i');
            }
            return preg_replace('#\<agoraDataHora([\+\-]){0,1}(P.*){0,1}\>#', $data, $texto);

        }
         else if (strpos($texto, '<agora>')) {
            $data = new \DateTime('now');
            return str_replace('<agora>', $data->format('d/m/Y'), $texto);
        } else if (strpos($texto, '<ano>')) {
            $data = new \DateTime('now');
            return str_replace('<ano>', $data->format('Y'), $texto);
        }

        return $texto;
    }

    /**
     * Clica no checkbox cujo valor seja "elemento". (Usa função de clique do mouse.)
     *
     * @When /^eu clico no checkbox "(?P<elemento>[^"]+)"$/
     */
    public function euClicoNoCheckbox($elemento)
    {
        $el = $this
            ->getSession()
            ->getPage()
            ->find('xpath', '//input[@type="checkbox" and (contains(@value,"'.$elemento.'") or contains(@id,"'. $elemento .'") or contains(@name,"'.$elemento.'"))]');

        if ($el == null) {
            throw new Exception("Não foi possível encontrar um checkbox com valor contendo ".$elemento.".");
        }

        $el->click();
    }

    /**
     * Verifica se uma opção está selecionada em um campo especificado.
     *
     * @Then /^o input "(?P<nome>(?:[^"]|\\")*)" do tipo "(?P<tipo>(?:[^"]|\\")*)" (?:com valor) "(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function verificaCampoExiste($nome, $tipo, $valor = null)
    {
        // Verifica se existe algum campo select mostrando a opção desejada.
        $expXpath = "//input[@type='".$tipo."' and (contains(@id,'".$nome."') or contains(@name,'".$nome."'))";

        if ($valor != null) {
            $expXpath .=  " and contains(@value,'".$valor."')";
        }

        $expXpath .= "]";

        $input = $this->getSession()->getPage()->find(
            'xpath',
            $expXpath
        );

        if ($input == null) {
            $erro = "Não foi possível encontrar o campo ".$nome." do tipo ".$tipo;
            if ($valor != null) {
                $erro .= " com o valor ".$valor;
            }
            throw new Exception($erro);
        }
    }

    /**
     * Verifica se o campo checkbox está com a label e valor corretos
     *
     * @Then /^o input "(?P<nome>(?:[^"]|\\")*)" do tipo checkbox (?:com valor) "(?P<valor>(?:[^"]|\\")*)" (?:e label) "(?P<label>(?:[^"]|\\")*)"$/
     */
    public function verificaCheckboxValorELabel($nome, $valor, $label)
    {
        // Verifica se existe algum campo select mostrando a opção desejada.
        $expXpath = "//input[@type='checkbox' and (contains(@id,'".$nome."') or contains(@name,'".$nome."'))";
        $expXpath .=  " and contains(@value,'".$valor."')";
        $expXpath .=  " and contains(..,'".$label."')]";

        $input = $this->getSession()->getPage()->find(
            'xpath',
            $expXpath
        );

        if ($input == null) {
            throw new Exception("Não foi possível encontrar o checkbox ".$nome." com o valor ".$valor);
        }
    }

    public function verificaCheckboxExiste($id, $content)
    {
        // Verifica se existe algum campo checkbox mostrando a opção desejada.
        $expXpath = "//input[@type='checkbox' and (contains(@id,'".$id."') and contains(..,'".$content."'))]";

        $input = $this->getSession()->getPage()->find(
            'xpath',
            $expXpath
        );

        if ($input == null) {
            $erro = "Não foi possível encontrar o campo ".$id.' - '.$content." do tipo checkbox";

            throw new Exception($erro);
        }

        return $input;
    }

    /**
     * Verifica que a página contém um elemtento ou outro.
     *
     * @Then /^aqui pode aparecer "(?P<element>[^"]+)" ou "(?P<element1>[^"]+)"$/
     */
    public function aquiPodeAparecerOu($element, $element1)
    {
        if ($this->assertElementNotOnPage($element) && $this->assertElementNotOnPage($element1)) {
            throw new Exception('Não foi possível encontrar nenhum destes elementos "'.$element.'" '.$element1.'"');
        }
    }

    /**
     * Verifica se eu estou em uma determinada página
     */
    public function euEstouNaPagina($pagina)
    {
        if ($this->assertPageAddress($pagina)) {
        } else {
            throw new Exception('Você não está na página: '.$pagina);
        }
    }

    /**
     * Verifica a página tem um link com tooltip.
     *
     * @Then /^eu espero tooltip com texto "(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function verificaTooltip($texto)
    {
        $el = $this->getSession()->getPage()->find(
            'xpath',
            "//*[contains(@data-original-title,'".$texto."') or contains(@title,'".$texto."') ]"
        );

        if ($el === null) {
            throw new Exception('Não foi possível encontrar um tooltip com este texto "'.$texto.'".');
        }

        return $el;
    }

    /**
     * Verifica se existe todas as filiais no select.
     *
     * @Then /^deveriam ser listadas todas as filiais no campo "(?P<select>(?:[^"]|\\")*)"$/
     */
    public function verTodasAsFiliais($select)
    {
        $this->verificaOpcaoNoCampo($select, '1', 'BHZ - Belo Horizonte');
        $this->verificaOpcaoNoCampo($select, '2', 'RIO - Rio de Janeiro');
        $this->verificaOpcaoNoCampo($select, '3', 'SAO - São Paulo');
        $this->verificaOpcaoNoCampo($select, '4', 'CWB - Curitiba');
        $this->verificaOpcaoNoCampo($select, '5', 'CPQ - Campinas');
        $this->verificaOpcaoNoCampo($select, '6', 'VIX - Vitória');
        $this->verificaOpcaoNoCampo($select, '7', 'POA - Porto Alegre');
        $this->verificaOpcaoNoCampo($select, '8', 'FLN - Florianópolis');
        $this->verificaOpcaoNoCampo($select, '9', 'GVR - Governador Valadares');
        $this->verificaOpcaoNoCampo($select, '10', 'ITA - Blumenau');
    }

    /**
     * Teste de formulário de endereço cidades atendidas
     *
     * @When /^eu testo o endereço do model para cidades atendidas "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function euTestoEnderecoCidadesAtendidas($model)
    {
        $this->preencheCamposEnderecoEmBranco($model);
        $this->clicaBotao('Salvar');
        $this->verificaErrosEnderecoCamposEmBranco($model);
        $this->preencheCamposEnderecoCepExistente($model);
        $this->preencheCamposEnderecoCepCidadesAtendidas($model);
    }

    /**
     * Teste de formulário de endereço
     *
     * @When /^eu testo o endereço do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function euTestoEndereco($model)
    {
        $this->preencheCamposEnderecoEmBranco($model);
        $this->clicaBotao('Salvar');
        $this->verificaErrosEnderecoCamposEmBranco($model);
        $this->preencheCamposEnderecoCepExistente($model);
        $this->preencheCamposEnderecoCepNaoEncontrado($model);
    }

    /**
     * Teste de endereço em branco
     *
     * @When /^eu preencho o endereço em branco do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function preencheCamposEnderecoEmBranco($model)
    {
        $this->preencheCampo($model."[fsCep]", '');
        $this->preencheCampo($model."[fsNumero]", '');
        $this->preencheCampo($model."[fsComplemento]", '');
        $this->euEsperoCampoDesabilitado($model."[fsEndereco]");
        $this->euEsperoCampoDesabilitado($model."[fsBairro]");
        $this->euEsperoSelectDesabilitado($model."[fnEstado]");
        $this->euEsperoSelectDesabilitado($model."[fnCidade]");
    }

    /**
     * Teste de retorno endereço em branco
     *
     * @Then /^eu verifico mensagens de erro de endereço em branco do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function verificaErrosEnderecoCamposEmBranco($model)
    {
        $verificaModel = strripos($model, '[');

        if ($verificaModel === false) {
            $model = $model.'[fnEndereco]';
        }

        $this->noCampoErro($model."[fsCep]", 'Esse campo é obrigatório');
        $this->noCampoErro($model."[fsEndereco]", 'Esse campo é obrigatório');
        $this->noCampoErro($model."[fsNumero]", 'Esse campo é obrigatório');
        $this->noCampoErro($model."[fsBairro]", 'Esse campo é obrigatório');
        $this->noCampoErro($model."[fnCidade]", 'Esse campo é obrigatório');
        $this->noCampoErro($model."[fnEstado]", 'Esse campo é obrigatório');
    }

    /**
     * Verifica campo desabilitado
     *
     * @Then /^eu espero o campo "(?P<campo>(?:[^"]|\\")*)" desabilitado$/
     */
    public function euEsperoCampoDesabilitado($campo)
    {
        $xpath = $this->getSession()->getPage()->find(
            'xpath',
            "//*[contains(@name,'".$campo."') and contains(@disabled, 'disabled')]"
        );

        if ($xpath == null) {
            throw new Exception('Não foi possível encontrar o campo "'.$campo.'" desabilitado.');
        }
    }

    /**
     * Verifica select desabilitado
     *
     * @Then /^eu espero o select "(?P<campo>(?:[^"]|\\")*)" desabilitado$/
     */
    public function euEsperoSelectDesabilitado($campo)
    {
        $xpath = $this->getSession()->getPage()->find(
            'xpath',
            "//select[contains(@name,'".$campo."') and contains(@disabled, 'disabled')]"
        );

        if ($xpath == null) {
            throw new Exception('Não foi possível encontrar o select "'.$campo.'" desabilitado.');
        }
    }

    /**
     * Teste de endereço existente
     *
     * @When /^eu preencho o endereço com cep existente do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function preencheCamposEnderecoCepExistente($model)
    {
        $this->preencheCampo($model."[fsCep]", '32250010');
        $this->euEsperoCarregando();
        $this->euEsperoCampoDesabilitado($model."[fsEndereco]");
        $this->euEsperoCampoDesabilitado($model."[fsBairro]");
        $this->euEsperoSelectDesabilitado($model."[fnEstado]");
        $this->euEsperoSelectDesabilitado($model."[fnCidade]");
    }

    /**
     * Teste de endereço não encontrado
     *
     * @When /^eu preencho o endereço com cep não encotrado do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function preencheCamposEnderecoCepNaoEncontrado($model)
    {
        $this->preencheCampo($model."[fsCep]", '11140361');
        $this->euEsperoCarregando();
        $this->euDeveriaVerAlertifyErro('Não foi possível localizar o CEP.');

        $this->preencheCampo($model."[fsEndereco]", 'Rua Henrique Gorceix');
        $this->preencheCampo($model."[fsNumero]", '428');
        $this->preencheCampo($model."[fsComplemento]", 'apt 101');
        $this->preencheCampo($model."[fsBairro]", 'Padre Eustáquio');
        $this->selecionaOpcao('Minas Gerais', $model."[fnEstado]");
        $this->euEspero(1);
        $this->selecionaOpcao('Belo Horizonte', $model."[fnCidade]");
    }

    /**
     * Teste de endereço não encontrado
     *
     * @When /^eu preencho o endereço com cep cidades atendidas "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function preencheCamposEnderecoCepCidadesAtendidas($model)
    {
        $this->preencheCampo($model."[fsCep]", '11140361');
        $this->euEsperoCarregando();
        $this->euDeveriaVerAlertifyErro('Não foi possível localizar o CEP para as cidades atendidas.');

        $this->preencheCampo($model."[fsEndereco]", 'Rua Henrique Gorceix');
        $this->preencheCampo($model."[fsNumero]", '428');
        $this->preencheCampo($model."[fsComplemento]", 'apt 101');
        $this->preencheCampo($model."[fsBairro]", 'Padre Eustáquio');
        $this->selecionaOpcao('Minas Gerais', $model."[fnEstado]");
        $this->euEspero(1);
        $this->selecionaOpcao('Belo Horizonte', $model."[fnCidade]");
    }

    /**
     * Verifica formulário todos com mensagem de validação
     *
     * @Then /^em todos os "(?P<cont>(?:[^"]|\\")*)" campos obrigatórios$/
     */
    public function preencheDadosInvalidos($cont)
    {
            // /eu deveria ver o texto "(?P<texto>(?:[^"]|\\")*)"
        $this->getSession()->wait(500);
        $elementos_erro = $this->getSession()->getPage()->findAll(
            'xpath',
            "//label[contains(@class, 'control-label required')]"
        );

        if (count($elementos_erro) != (int)$cont) {
            throw new Exception('Existem ' . $elementos_erro . ' e o esperado eram ' . $cont);
        }
    }

    /**
     * Limpo todos os campos de um formulário (com a classe form-control)".
     *
     * @Then /^limpo todos os campos do formulário$/
     */
    public function limparCamposFormulario()
    {
        $registerForm = $this->getSession()->getPage()->findAll('xpath', "//*[contains(@class, 'form-control')]|//*[@multiplo-checkbox]");

        if ($registerForm ===null) {
            throw new Exception("Não existem elementos com a classe form-control nesta tela");
        }

        foreach ($registerForm as $rowForm) {
            $desabilitado = $rowForm->getAttribute('disabled');
            $visible = $rowForm->isVisible();
            if ($desabilitado != 'disabled' && $visible === true) {
                $rowForm->click();

                // Caso for um select sem opção padrão, alterar para a primeira opção disponível.
                if ($rowForm->getTagName() == 'select') {
                    $rowForm->selectOption('');
                } else {
                    $rowForm->setValue('');
                }
            }
        }
    }

    /**
     * Clica em um elemento que contem tooltip
     *
     * @Then /^eu clico no botão que contém o tooltip "(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function clicaTooltip($texto)
    {
        $elclica = $this->verificaTooltip($texto);
        $elclica->click();
    }

    /**
     * Clica em um registro para removê-lo
     *
     * @Then /^eu clico no botão para remover um registro "(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function removerRegistro($texto)
    {
        $elRemover = $this->verificaTooltip($texto);
        $elRemover->click();
    }

    /**
     * Verifica valores dos campos (com jquery)
     *
     * @Then /^ao entrar no campo "(?P<campo>(?:[^"]|\\")*)" deveria trazer a hora atual no formato "(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function testaHoraAtual($campo, $valor)
    {
        $data = new \DateTime('now');
        $valorCampoForm = $this->getSession()->evaluateScript("$('#". $campo ."').val()");

        if ($data->add(new DateInterval('PT9M'))->format($valor) == $valorCampoForm) {
            return;
        }

        if ($data->add(new DateInterval('PT1M'))->format($valor) == $valorCampoForm) {
            return;
        }

        if ($data->add(new DateInterval('PT1M'))->format($valor) == $valorCampoForm) {
            return;
        }

        if ($data->add(new DateInterval('PT1M'))->format($valor) == $valorCampoForm) {
            return;
        }

        $valor = $data->format($valor);

        throw new Exception('Hora atual não é válida.');

    }

    /**
     * Verifica valores dos campos (com jquery)
     *
     * @Then /^ao editar o campo "(?P<campo>(?:[^"]|\\")*)" deveria trazer o valor "(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function executaJquery($campo, $valor)
    {
        $valorCampoForm = $this->getSession()->evaluateScript("$('#". $campo ."').val()");

        $valor = $this->buscaParametros($valor);

        if ($valorCampoForm != $valor) {
            throw new Exception('O campo: '. $campo .' não contém o valor: '. $valor);
        }
    }

    /**
     * @Transform /^json:(.*)$/
     */
    public function collectionToArray($json)
    {
        $json = str_replace("'", '"', $json);
        $array = json_decode($json, true);

        if (is_null($array)) {
            throw new Exception('O valor informado não é um json: '.$json);
        }

        return $array;
    }

    /**
     * Verifica o valor de um checkbox baseado no texto do option (com jquery)
     *
     * @Then /^ao editar o checkbox "(?P<campo>(?:[^"]|\\")*)" deveria trazer os valores marcados "(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function verificaCheckboxMarcado($campo, $valor)
    {
        $checkBoxId = null;

        foreach ($valor as $key => $value) {
            $checkBoxId = $campo.'_'.$key;
            // Busca se o checkbox existe
            $input = $this->verificaCheckboxExiste($checkBoxId, $value);

            if (!$input->isChecked()) {
                throw new Exception("O checkbox ".$valor[$key]." não está marcado");
            }
        }

        // Verifica se existe algum campo checkbox que não deveria estar estar marcado
        $expXpath = "//input[@type='checkbox' and (contains(@id,'".$campo."') or contains(@name,'".$campo."'))]";

        $input = $this->getSession()->getPage()->findAll(
            'xpath',
            $expXpath
        );

        $checkboxValue = null;

        foreach ($input as $linha => $tag) {
            $checkboxValue = $tag->getAttribute('value');
            if (!array_key_exists($checkboxValue, $valor)) {
                if ($tag->isChecked()) {
                    throw new Exception("O checkbox ".$tag->getAttribute('value'). ' - '.' não deveria estar marcado');
                }
            }
        }
    }

    /**
     * Verifica os checkbox marcados(Checked)
     *
     * @Then /^ao editar o checkbox "(?P<campo>(?:[^"]|\\")*)" deveria trazer o valor "(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function verificaValorCheckbox($checkbox, $valor)
    {
        $valorCampoForm = $this->getSession()->evaluateScript("$('#". $checkbox ." option:selected').text()");

        if ($valorCampoForm != $valor) {
            throw new Exception('O checkbox: '. $campo .' não contém o valor: '. $valor);
        }
    }

    /**
     * Verifica se existe um elemento com uma Xpath específica, com a opção de clicar no mesmo
     * @Then /^deveria estar na tela o elemento "(?P<xpath_elemento>(?:[^"]|\\")*)" (?:e ao) "(?P<clicar>(?:[^"]|\\")*)"$/
     */
    public function verificaElementoPorXpath($xpath_elemento, $clicar = null)
    {
        $el = $this->getSession()->getPage()->find('xpath', $xpath_elemento);

        if (null === $el) {
            throw new Exception("Não existe o elemento com a seguinte xpath: ", $xpath_elemento);
        }

        if (null != $clicar) {
            $el->click();
        }
    }


    /**
     * Preenche campo do tipo textarea identificado por: id
     *
     * @When /^eu preencho o textarea (?:|o id )"(?P<id>(?:[^"]|\\")*)" com (?:|o valor )"(?P<valor>(?:[^"]|\\")*)"$/
     */
    public function preenchoTextArea ($id, $valor)
    {
        $el = $this->getSession()->getPage()->find('xpath', '//textarea[@id="'. $id .'"]');
        if ($el === null) {
            throw new Exception("Não foi possível preencher o campo:" . $el);
        }

        $el->setValue($valor);
    }

    /**
     * Teste de formulário de endereço não obrigatório
     *
     * @When /^eu testo o endereço não obrigatório do model "(?P<model>(?:[^"]|\\")*)"$/
     */
    public function euTestoEnderecoOpcional($model)
    {
        $this->preencheCamposEnderecoCepExistente($model);
        $this->preencheCamposEnderecoCepNaoEncontrado($model);
    }

    /**
     * Inserir arquivo no formulário
     * @When /^(?:|I )eu insiro o arquivo no campo "(?P<campo>(?:[^"]|\\")*)"$/
     */
    public function inserirArquivoFormulario($campo)
    {
        $el = $this->getSession()->getPage()->find('xpath', $campo);

        if (null === $el) {
            throw new Exception("Não existe o elemento com a seguinte xpath: ", $campo);
        }

        $img = $this->getSession()->getPage()->find('xpath', '//*[@id="head-nav"]/div/div/ul/li/a/img');

        $el->dragTo($img);
    }

    /**
     * Verifica um alertify qualquer (sem especificação de tipo [erro, sucesso])
     *
     * @Then /^eu deveria ver alertify com (?:|a mensagem |o texto )"(?P<texto>(?:[^"]|\\")*)"$/
     */
    public function verificarAlertify($texto)
    {
        $this->getSession()->wait(500);
        $alertify = $this->getSession()->getPage()->find(
            'xpath',
            "//article[contains(@class,'alertify-log alertify-log-show') and contains(.,'".$texto."')]"
        );

        if ($alertify == null) {
            throw new Exception('Não foi possível encontrar a mensagem/texto"'.$texto.'" no alertify.');
        }

        $alertify->click();
    }
}
