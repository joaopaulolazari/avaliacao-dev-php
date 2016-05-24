'use strict';

angular.module('site.app.services', [])
.service('rotaService', ['$log', function ($log) {

    var rotas = window.rotas;
    var parametros = window.paramsRota;
    var asset = window.asset;

    /*
     * path - Aplica  parâmetros a string da rota
     *
     * @param String nomeRota - nome da rota
     * @param Object parametros - objeto associativo com parâmetros para substituir na rota
     *
     * @return String - rota formatada
    */
    this.path = function(nomeRota, parametros) {
        if (angular.isUndefined(nomeRota)) {
            $log.error('O nome da rota não foi informado');
            return;
        }
        var rota = rotas[nomeRota];
        if (angular.isUndefined(rota)) {
            $log.error('Rota ' + nomeRota + ' não existe.');
            return;
        }
        for (var key in parametros) {
            rota = rota.replace('{' + key + '}', encodeURIComponent(parametros[key]));
        }
        //retira o parâmetro _format caso exista e retorna a rota formatada
        return rota.replace('.{_format}', '');
    };

    /*
     * get - Retorna o parametro da rota atual
     *
     * @param String parametro - o parametro
     *
     * @return String - o valor do parametro
    */
    this.get = function(parametro) {
        if(typeof parametros[parametro] != 'undefined') {
            return parametros[parametro];
        }
        $log.error('Parametro "' + parametro + '" não existe para a rota atual.')
    };

    /*
     * all - Retorna todos os parametros da rota atual
     *
     * @return String - o valor do parametro
    */
    this.all = function() {
        return parametros;
    };

    /*
     * asset - Retorna a URL base concatenada com o parâmetro (opcional)
     *
     * @param String    URI    String relativa ao URI
     *
     * @return String          URL + URI
    */
    this.asset = function(URI) {
        if (angular.isUndefined(URI)) {
            URI = '';
        }
        return asset + URI.replace(/^\s*\//,"");
    };

}])
.service('modalService', ['$modal', '$sce', function ($modal, $sce) {

    var modalPadrao = {
        keyboard: false,
        modalFade: true,
        backdrop: 'static'
    };

    this.formulario = function(config, templateFormulario, $event) {

        $event.preventDefault();

        //Preenche com valores temporários
        var tempConfigModal = {
            titulo: 'Modal Form',
            btnCancelar: 'Cancelar',
            btnSalvar: 'Salvar',
            templateUrl: 'modal-formulario.html',
            size: 'lg',
            templateFormulario: templateFormulario
        };
        //Inclui com valores padrão
        angular.extend(tempConfigModal, config);

        angular.extend(tempConfigModal, modalPadrao);

        tempConfigModal.formulario = $sce.trustAsHtml(tempConfigModal.formulario);

        config.scope.modalOptions = tempConfigModal;

        if (!tempConfigModal.controller)  {
            //Cria o método controller
            tempConfigModal.controller = function ($scope, $modalInstance) {
                $scope.modalOptions = tempConfigModal;
                $scope.modalOptions.sim = function (result) {
                    $modalInstance.close(result);
                };
                $scope.modalOptions.nao = function (result) {
                    $modalInstance.close(result);
                };
                $scope.modalOptions.fecharModal = function (result) {
                    $modalInstance.close(result);
                };
            }
        }
        return $modal.open(tempConfigModal).result;
    }

    this.confirmar = function(mensagem, configModal) {
        //Preenche com valores temporários
        var tempConfigModal = {
            mensagem: mensagem,
            btnSim: 'Sim',
            btnNao: 'Não',
            tipo: 'warning',
            simbolo: 'check',
            templateUrl: 'modal-confirmacao.html'
        };
        //Inclui com valores padrão
        angular.extend(tempConfigModal, modalPadrao);
        //Verifica se o parametro passado é um objeto e une como objeto padrão
        if(typeof configModal === 'object') {
            angular.extend(tempConfigModal, configModal);
        }
        tempConfigModal.mensagem = $sce.trustAsHtml(tempConfigModal.mensagem);
        //Se o objeto passado não tiver o método controller, cria-se um
        if (typeof configModal !== 'object' || !configModal.controller) {
            tempConfigModal.controller = function ($scope, $modalInstance) {
                $scope.modalOptions = tempConfigModal;
                $scope.modalOptions.sim = function (result) {
                    $modalInstance.close(result);
                };
                $scope.modalOptions.nao = function (result) {
                    $modalInstance.close(result);
                };
            }
        }
        return $modal.open(tempConfigModal).result;
    }

    this.alerta = function(mensagem, tipo, configModal) {
        //Preenche com valores temporários
        var tempConfigModal = {
            mensagem: mensagem,
            btnOk: 'Ok',
            tipo: tipo,
            templateUrl: 'modal-alerta.html'
        };

        switch (tipo) {
            case 'success':
                //função verifica string vazio
                if (!mensagem || 0 === mensagem.length || !mensagem.trim()) {
                    tempConfigModal.mensagem = 'Operação realizada com sucesso.'
                }
                tempConfigModal.simbolo = 'check';
                break;
            case 'warning':
                //função verifica string vazio
                if (!mensagem || 0 === mensagem.length || !mensagem.trim()) {
                    tempConfigModal.mensagem = 'Atenção.'
                }
                tempConfigModal.simbolo = 'warning';
                break;
            case 'info':
                //função verifica string vazio
                if (!mensagem || 0 === mensagem.length || !mensagem.trim()) {
                    tempConfigModal.mensagem = 'Atenção.'
                }
                tempConfigModal.simbolo = 'info';
                break;
            case 'danger':
                //função verifica string vazio
                if (!mensagem || 0 === mensagem.length || !mensagem.trim()) {
                    tempConfigModal.mensagem = 'Um erro inesperado ocorreu.<br>Se o problema persistir entre em contato com o departamento de TI.';
                }
                tempConfigModal.simbolo = 'times';
                break;
        }

        //Inclui com valores padrão
        angular.extend(tempConfigModal, modalPadrao);
        //Verifica se o parametro passado é um objeto e une como objeto padrão
        if(typeof configModal === 'object') {
            angular.extend(tempConfigModal, configModal);
        }
        tempConfigModal.mensagem = $sce.trustAsHtml(tempConfigModal.mensagem);
        //Se o objeto passado não tiver o método controller, cria-se um
        if (typeof configModal !== 'object' || !configModal.controller) {
            tempConfigModal.controller = function ($scope, $modalInstance) {
                $scope.modalOptions = tempConfigModal;
                $scope.modalOptions.ok = function (result) {
                    $modalInstance.close(result);
                };
            }
        }
        return $modal.open(tempConfigModal).result;
    }
}])
.service('errosService', [ function () {

    var mensagens = {
        minlength: "Esse campo deve ter no mínimo %%minlength%% caracteres",
        maxlength: "Esse campo deve ter no máximo %%maxlength%% caracteres",
        required: "Esse campo é obrigatório",
        email: "Esse email não é válido",
        pattern: "O formato desse campo não é valido",
        cpf: "CPF com formato inválido",
        time: "Esse campo deve ser um tempo válido."
    };

    var mensagensBackend = [];
    var mensagensGenericasBackend = [];


    function procuraParametros(mensagem, elemento) {
        var parametros = mensagem.match(/%%[a-z\-A-Z]*%%/g);
        var elemento = angular.element('#'+elemento);
        var parametrosComValor = {};

        angular.forEach(
            parametros,
            function(parametro, indice) {
                parametrosComValor[parametro] = elemento.attr(parametro.replace(/%%/g, ''));
            }
        )

        return parametrosComValor;
    }

    function substituiParametros(mensagem, parametros){

        angular.forEach(
            parametros,
            function(parametro, indice) {
                mensagem = mensagem.replace(indice, parametro);
            }
        )
        return mensagem;
    }

    function formataMensagem(mensagem, elemento) {
        var parametros = procuraParametros(mensagem, elemento);
        return substituiParametros(mensagem, parametros);
    }

    /*
     * adicionaErro - Adiciona uma mensagem de erro no form
     *
     * @formController - Form do elemento
     * @identificador - Identificador único do erro
     * @mensagem - Mensagem descrevendo o erro
     * @backend - Flag booleano que informa se o erro é de backend, necessário para a função removeErrosBackend
     *
    */
    this.adicionaErro = function(formController, identificador, mensagem, backend, generico) {
            formController.$setValidity( identificador, false );
            mensagens[identificador] = mensagem;

            if(backend) {
                mensagensBackend.push(identificador);
            }

            if(generico) {
                mensagensGenericasBackend.push(identificador);
            }
    }

    /*
     * removeErro - Remove um erro de determinado form
     *
     * @formController - Form do elemento
     * @identificador - Identificador do erro
     *
    */
    this.removeErro = function(formController, identificador) {
            formController.$setValidity( identificador, true );
    }

    /*
     * removeErrosBackend - Remove todos os erros do form que foram marcados como backend
     *
     * @formController - Objeto do form
     *
    */
    this.removeErrosBackend = function(formController) {
        angular.forEach(
            mensagensBackend,
            function(erroNome, indice) {
                // Marca o campo como válido e remove da array de erros de backend
                if(typeof formController[erroNome] !== 'undefined') {
                    formController[erroNome].$setValidity( erroNome, true );
                } else if (typeof formController.$error[erroNome] !== 'undefined') {
                    formController.$setValidity( erroNome, true );
                }
            }

        );
        mensagensBackend = [];
        mensagensGenericasBackend = [];
    }

    this.buscaMensagem = function(erro, elemento) {

        if(typeof mensagens[erro] == 'undefined') {
            return "Mensagem de erro para o erro "+erro+" não definida.";
        }

        // console.log(elemento);
        return formataMensagem(mensagens[erro], elemento);
    }

    this.buscaMensagensGenericas = function() {
        var erros = [];
        angular.forEach(
            mensagensGenericasBackend,
            function(identificador, indice) {
                erros.push(mensagens[identificador]);
            }
        );

        return erros;
    }
}])
.service('utilidadesService', [function () {
    // Função que procura o proximo indice disponível em um objeto
    // que será indexado como uma array sequencial
    this.proximoIndice = function(objeto, indice)
    {
        if (typeof indice == 'undefined') {
            var indice = this.tamanhoObjeto(objeto);
        }

        // Caso o indice já existir, buscar o proximo
        if (typeof objeto[indice] != 'undefined') {
            return this.proximoIndice(objeto, indice+1);
        }

        return indice;
    }

    // Verifica quantas propriedades um objeto contém
    this.tamanhoObjeto = function(objeto) {
        if (typeof objeto == 'undefined' || typeof objeto == 'null' ) {
            return 0;
        }
        return Object.keys(objeto).length;
    }
}])
.service('cubagemService', [function () {
    /**
     * Calcula o peso cubado de acodo com as medidas
     *
     * @param float altura
     * @param float largura
     * @param float comprimento
     * @param float taxaCubagem
     *
     * @return float peso cubado
    */
    this.calculaCubagem = function(altura, largura, comprimento, taxaCubagem)
    {

        if (!angular.isDefined(taxaCubagem)) {
            taxaCubagem = 300;
        }

        return parseFloat(altura*largura*comprimento*taxaCubagem);
    }

}])
/**
 * angularLoadService - Serviço que adiciona dinamicamente scripts CSS e JS na página.
 */
.service('angularLoadService', ['$document', '$q', '$timeout', function ($document, $q, $timeout) {

    /**
     * loader - Função que pega a Url enviada verifica existencia e inclui o script na página se existente
     * @param  string createElement url de script a ser incluido
     */
    function loader(createElement) {
        var promises = {};

        return function(url) {
            if (typeof promises[url] === 'undefined') {
                var deferred = $q.defer();
                var element = createElement(url);

                element.onload = element.onreadystatechange = function (e) {
                    $timeout(function () {
                        deferred.resolve(e);
                    });
                };
                element.onerror = function (e) {
                    $timeout(function () {
                        deferred.reject(e);
                    });
                };

                promises[url] = deferred.promise;
            }

            return promises[url];
        };
    }

    /**
     * Dynamically loads the given script
     * @param src The url of the script to load dynamically
     * @returns {*} Promise that will be resolved once the script has been loaded.
     */
    this.loadScript = loader(function (src) {
        var script = $document[0].createElement('script');

        script.src = src;

        $document[0].body.appendChild(script);
        return script;
    });

    /**
     * Dynamically loads the given CSS file
     * @param href The url of the CSS to load dynamically
     * @returns {*} Promise that will be resolved once the CSS file has been loaded.
     */
    this.loadCSS = loader(function (href) {
        var style = $document[0].createElement('link');

        style.rel = 'stylesheet';
        style.type = 'text/css';
        style.href = href;

        $document[0].head.appendChild(style);
        return style;
    });
}])
.service('buscarClientesTypeahead', ['$rootScope', '$http', 'rotaService', function ($rootScope, $http, rotaService) {
    this.buscaClientes = function (string) {
        var urlBuscaCliente = rotaService.path('api_comercial_cliente_busca_autocomplete');
        return $http.get(urlBuscaCliente, {ignoreLoadingBar: true, params:{'buscar': string}})
        .then(function(resposta) {
            if (resposta.data.dados.clientes.length === 0) {
                return [{label: 'Nenhum cliente encontrado'}];
            }
            return resposta.data.dados.clientes.map(function(cliente){
                return {
                    label: cliente.fsRazaoSocial + ' - ' + cliente.fsCnpjCpf,
                    fnClienteId: cliente.fnClienteId
                };
            });
        });
    }

    this.selecionaCliente = function($item, $model, $campo, $callback) {
        if (!angular.isDefined($model)) {
            console.log('O model '+$model+ 'deve ser definido');
            return;
        }
        $model[$campo] = $item.fnClienteId;

        if (angular.isDefined($callback)) {
            $callback();
        }
    };
}])
.service('treeGridService', [function () {

    this.organizaArvoreAreaOrganizacional = function(arrDados, fnAreaOrganizacionalSuperiorId)
    {
        var aux;
        if(arrDados.length == 0) {
            return [];
        }

        var arvore = []
        for(var i in arrDados) {
            if (angular.isDefined(arrDados[i])) {
                aux = 0;
                if (angular.isDefined(arrDados[i].fnAreaOrganizacionalSuperior)) {
                    aux = arrDados[i].fnAreaOrganizacionalSuperior.fnAreaOrganizacionalId;
                }

                if(aux == fnAreaOrganizacionalSuperiorId) {
                    var children = this.organizaArvoreAreaOrganizacional(arrDados, arrDados[i].fnAreaOrganizacionalId);
                    if(children.length) {
                        arrDados[i].children = children;
                    }
                    arvore.push(arrDados[i])
                }
            }
        }
        return arvore;
    }

}]);