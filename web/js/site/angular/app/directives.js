'use strict';

angular.module('site.app.directives', [])
.directive('validaForm', ['$http', '$timeout', function ($http, $timeout) {

    //Objeto para armazenar erros de formulário
    var formErros = {
        email: 'Esse email não é válido',
        max: 'O valor desse campo deve ser no máximo <%max%>',
        maxlength: 'Esse campo deve ter no máximo <%maxlength%> caracteres',
        min: 'O valor desse campo deve ser no mínimo <%min%>',
        minlength: 'Esse campo deve ter no mínimo <%minlength%> caracteres',
        number: 'Esse campo deve ser numérico',
        pattern: 'O formato desse campo não é valido',
        required: 'Esse campo é obrigatório',
        url: 'Essa URL não é válida',
        customizados: new Object()
    };

    return {
        priority: 1,
        restrict: 'A',
        controller: function($scope, $element, $attrs) {

            this.formErros = formErros;
            this.formNome = $attrs.name;

            var callbacks = [];

            $attrs.$set('novalidate', 'novalidate');
            //Adiciona evento para verificar validade do formulário antes de submitar
            $element.on('submit', function() {

                exibirAbaComErro();

                if (angular.isFunction($scope.preFormSubmit)){
                    if (!$scope.preFormSubmit()) {
                        return false;
                    }
                }

                if (!validaCallback()) {
                    chamaPosFormSubmit(false);
                    return false;
                }

                //Evento para sinalizar quer formulário foi submitado
                $scope.$broadcast('form-submitted', {formNome:$attrs.name});

                if (!$scope[$attrs.name].$valid) {
                    $scope.$broadcast('exibir-erros', {formNome:$attrs.name});
                    alertify.error('Verifique o(s) erro(s) no formulário');
                    chamaPosFormSubmit(false);
                    return false;
                } else {
                    var dados = new Object();
                    dados[$attrs.model] = $scope[$attrs.model];

                    $http({
                        method: $attrs.type,
                        url: $attrs.url,
                        data: dados,
                    }).success(function(resposta, status) {
                        if(status == 200 || status == 201) {
                            chamaPosFormSubmit(true);
                            $scope.modalService.alerta(resposta.mensagem, 'success').then(function(resultado) {
                                if (angular.isString($attrs.redireciona)) {
                                    window.location=$attrs.redireciona;
                                };
                            });
                        } else {
                            chamaPosFormSubmit(false);
                            $scope.modalService.alerta(response.data.mensagem, 'danger');
                        }
                    }).error(function(resposta, status) {
                        chamaPosFormSubmit(false);
                        if (status == 412 || status == 422) {
                            chamaFormValidacao(false, resposta);
                            alertify.error('Verifique o(s) erro(s) no formulário');
                            $scope.adicionaErroBackend(resposta.detalhes);
                            $timeout(exibirAbaComErro);
                        } else {
                            $scope.modalService.alerta(resposta.mensagem, 'danger');
                        }
                    });
                }
            });

            // Muda para a primeira tabela que tenha algum elemento marcado com
            // a classe ng-invalid.
            function exibirAbaComErro()
            {
                var tabList = $('ul.nav-tabs');
                //Verifica se existe elemento com abas
                if (tabList.length == 0) return;
                //
                $('a[role=tab]').css('background', '#FFF');

                var abasComErro = new Object();
                var campos = $('.ng-invalid').not('form');
                //Verifica se existem campos inválidos
                if (campos.length == 0) return;

                angular.forEach(campos, function(campo) {
                    var painel = $(campo).parents('div[role=tabpanel]');
                    var abaPainel = $('a[href="#'+painel.attr('id')+'"]');
                    abasComErro[painel.attr('id')] = abaPainel;
                    abaPainel.css('background', 'linear-gradient(rgb(255, 219, 219) 0%, rgb(255, 255, 255) 100%)');
                });
                //Exibe a primeira aba com erro de formulário
                if (angular.isDefined(Object.keys(abasComErro)[0])) {
                    abasComErro[Object.keys(abasComErro)[0]].tab('show');
                };
            }

            var chamaPosFormSubmit = function(status) {
                if (angular.isFunction($scope.posFormSubmit)){
                    $scope.posFormSubmit(status);
                }
            }
            var chamaFormValidacao = function(status, data) {
                if (angular.isFunction($scope.formValidacao, data)){
                    $scope.formValidacao(status, data);
                }
            }
            $scope.adicionarCallbackValidacao = function(callback) {
                if (angular.isFunction(callback)) {
                    callbacks.push(callback);
                }
            }

            var validaCallback = function() {

                var status = true;
                angular.forEach(callbacks, function(callback, indice) {
                    if (!callback()) {
                        status = false;
                        return false;
                    }
                });

                return status;
            }

            //Função para disparar listener do campo com erro de backend
            $scope.adicionaErroBackend = function(errosBackend) {
                if (angular.isUndefined(errosBackend)) return;
                //Se existir algum erro genêrico ('#') aplica pelo alertify;
                if (angular.isDefined(errosBackend['#'])) {
                    var mensagem = '<ul><li>'+errosBackend['#'].join('</li><li>')+'</li></ul>';
                    alertify.error(mensagem, 0);
                    delete(errosBackend['#']);
                }
                angular.forEach(errosBackend, function(erros, campo) {
                    var erroCampo = new Object();
                    erroCampo[campo] = erros;
                    $scope.$broadcast('exibir-erros-'+campo, erroCampo);
                });
            };

        }
    };
}])
.directive('pullDown', function() {
    return {
        restrict: 'C',
        link: function ($scope, iElement, iAttrs) {
            var $parent = iElement.parent();
            var $parentHeight = $parent.height();
            var height = iElement.height();

            iElement.css('margin-top', $parentHeight - height + 2);
        }
    };
})
.directive('formerro', ['$timeout', '$sce', function($timeout, $sce) {

    return {
        priority: 2,
        restrict: 'E',
        require: '?^validaForm',
        replace: true,
        scope: {
            form: '=form',
            formGroup: '@formGroup'
        },
        template: '<div ng-bind-html="getMensagens()"></div>',
        link: function(scope, element, attrs, ctrl) {
            if (
                !angular.isDefined(scope.form) &&
                !angular.isDefined(scope.formGroup)
            ) {
                return;
            }

            //Se for validação em grupo criar variáveis simulando um formController (https://docs.angularjs.org/api/ng/type/form.FormController)
            scope.formCtrl = scope.form;
            if (!angular.isDefined(scope.form) && angular.isDefined(scope.formGroup)) {
                scope.formCtrl = {
                    $validators: new Object(),
                    $error: {backend: false},
                    $name: scope.formGroup,
                    $setValidity: function(chave, valor) { if(angular.isDefined(this.$error[chave])) {this.$error[chave] = !valor;} return; }
                };
            }

            scope.mensagens = new Array();
            scope.submitted = false;
            var campo = $('[name="'+scope.formCtrl.$name+'"]');

            //Adiciona validador backend para todos os campos
            scope.formCtrl.$validators.backend = function() { return true; };

            //Monitora os erros do campo para exibir
            scope.$watchCollection('formCtrl.$error', function(erros) {
                if (!campo.attr('ignorar-form-error')) {
                    aplicaMensagens([scope.formCtrl.$name]);
                }
            });

            //Formata mensagens de erro
            scope.getMensagens = function(){
                return $sce.trustAsHtml(scope.mensagens.join('<br/>'));
            }
            //Função para verificar erros e aplicar mensagnes de erro aos campos
            function aplicaMensagens(campos, chamadaFormFactory) {
                $timeout(function() {
                    scope.mensagens = new Array();

                    // Caso houver um filtro por campos, somente exibir os erros contidos no mesmo
                    if (angular.isDefined(campos) && angular.isArray(campos) && campos.indexOf(scope.formCtrl.$name) == -1) {
                        return false;
                    }

                    // Caso não for uma chamada de validação customizada e o campo esteja com a flag de ignorar, marca-lo como valido
                    if (chamadaFormFactory != true && campo.attr('ignorar-form-error')) {
                        scope.formCtrl.$valid = true;
                        scope.formCtrl.$invalid = false;
                    }

                    angular.forEach(scope.formCtrl.$error, function(invalido , erro) {
                        // Caso não seja uma chamada de validação customizada e o campo esteja com a flag de ignora,
                        // marcar cada validação dele como válida
                        if (chamadaFormFactory != true && campo.attr('ignorar-form-error')) {
                            scope.formCtrl.$setValidity(erro, true);
                            return true;
                        }

                        if(invalido && scope.submitted) {
                            //Verifica se o erro está entre os erros
                            if (angular.isDefined(ctrl.formErros[erro])) {
                                var mensagem = ctrl.formErros[erro];
                                //Verifica se o erro possui parâmetros para substituir na mensagem
                                if (angular.isDefined(campo.attr(erro))) {
                                    mensagem = mensagem.replace('<%'+erro+'%>', campo.attr(erro), mensagem);
                                }
                                scope.mensagens.push(mensagem);
                            } else if (
                                //Verifica se existe mensagem customizadas para o campo atual
                                angular.isDefined(ctrl.formErros['customizados'][erro])
                                && angular.isDefined(ctrl.formErros['customizados'][erro][scope.formCtrl.$name])
                                )
                            {
                                var mensagem = ctrl.formErros['customizados'][erro][scope.formCtrl.$name];
                                //Verifica se o erro possui parâmetros para substituir na mensagem
                                if (angular.isDefined(campo.attr(erro))) {
                                    mensagem = mensagem.replace('<%'+erro+'%>', campo.attr(erro), mensagem);
                                }

                                if (angular.isArray(mensagem)) {
                                    scope.mensagens.push(mensagem.join('<br/>'));
                                } else {
                                    scope.mensagens.push(mensagem);
                                }
                            } else {
                                scope.mensagens.push('Mensagem de erro não definida');
                            }
                        }
                    });
                },0);
            }

            scope.$on('limpa-erros', function(event, filtro) {

                // Caso o evento não seja direcionado a esse form, ignorar.
                if (angular.isDefined(filtro.formNome) && ctrl.formNome != filtro.formNome) {
                    return false;
                }
                scope.submitted = false;
                scope.mensagens = new Array();
            });

            scope.$on('form-submitted', function(event, filtro) {
                if (angular.isDefined(filtro.formNome) && ctrl.formNome != filtro.formNome) {
                    return false;
                }

                scope.submitted = true;
                scope.mensagens = new Array();
                //Valida erro de backend
                scope.formCtrl.$setValidity('backend', true);
            });

            //Evento para aplicar erros do back-end
            scope.$on('exibir-erros-'+scope.formCtrl.$name, function(event, erros) {
                ctrl.formErros.customizados.backend = ctrl.formErros.customizados.backend || new Object();
                angular.extend(ctrl.formErros.customizados.backend, erros);
                //Invalida o validador backend
                scope.formCtrl.$setValidity('backend', false);
            });

            //Evento apra aplicar mensagens de front-end
            scope.$on('exibir-erros', function(event, filtro) {
                // Caso o evento não seja direcionado a esse form, ignorar.
                if (angular.isDefined(filtro.formNome) && ctrl.formNome != filtro.formNome) {
                    return false;
                }

                $(".load-transition:visible").remove();

                scope.mensagens = new Array();
                if (angular.isDefined(filtro.campos)) {
                    aplicaMensagens(filtro.campos, filtro.chamadaFormFactory);
                } else {
                    aplicaMensagens();
                }
            });
        }
    };

}])
/**
 * Faz o Enter se comportar como Tab nos campos do formulário.
 * Use a class "ignore-tab-enter" para um determinado elemento que deve fugir dessa regra
 */
.directive('tabEnter', function() {
    return {
        restrict: 'A',
        link: function(scope, element, attr) {

            $(document).keypress(function(e) {
                if (e.which !== 13 || $(":focus").hasClass('ignore-tab-enter')) {
                    return;
                }
                var campos = $(element).find(':enabled:visible').not('option, .ignore-element');
                campos.eq(campos.index($(":focus")) + 1).focus();
                return false;
            });
        }
    };
})

.directive('ngKeyEnter', function() {
        return function(scope, element, attrs) {
            element.bind("keydown keypress", function(event) {
                if (event.which === 13) {
                    scope.$apply(function() {
                        scope.$eval(attrs.ngKeyEnter);
                    });
                    event.preventDefault();
                }
            });
        };
})
/**
 * Aplica plugin Dropzone no elemento
 * ***REQUER QUE CARREGUE O JS "dropzone.min.js"
 */
.directive('dropzone',['$timeout', '$templateCache', function ($timeout, $templateCache) {

    return {
        restrict: 'A',
        required: 'ngModel',
        scope: {
            propriedade: '@propriedade',
            nomeOriginal: '@nomeOriginal',
            urlUpload: '@urlUpload',
            model: '=ngModel',
            template: '@',
        },
        link: function (scope, element, attr) {

            if(typeof scope.model === 'undefined') {
                scope.model = [];
            }
            var config, dropzone;
            Dropzone.autoDiscover = false;
            // Cria configuração para Dropzone
            var config = {
                options: {
                    url: scope.urlUpload,
                    addRemoveLinks: "remove",
                    dictRemoveFile: "Remover",
                    dictDefaultMessage: "<h3>Clique ou arraste para adicionar arquivos.</h3>",
                    dictMaxFilesExceeded: "Você pode adicionar no máximo 3 fotos",
                    dictCancelUpload: "Cancelar upload",
                    dictCancelUploadConfirmation: "Tem certeza que deseja cancelar?",
                    dictFallbackMessage: "Seu navagador não suporta que arraste os arquivos para upload",
                    dictFileTooBig: "O arquivo é muito grande(<%filesize%>MiB). Tamanho máximo: <%maxFilesize%>MiB.",
                    dictInvalidFileType: "Você não pode fazer upload deste tipo de arquivo.",
                    dictResponseError: "Ocorreu um erro ao salvar arquivo",
                    dictRemoveFileConfirmation: null,
                    maxFiles: 3,
                    clickable: true,
                    canceled: function(file) {
                        return this.emit("error", file, "Upload cancelado");
                    }
                }
            }

            // se precisar usar template
            // if (scope.template) {
            //     config.options.previewTemplate = $templateCache.get(scope.template);
            // }

            dropzone = new Dropzone(element[0], config.options);

            /**
             * Atualiza value de acordo com o ngModel
             */
            scope.$watch('model', function(valor, valorAnterior) {
                if (typeof scope.model !== 'undefined') {
                    angular.forEach(scope.model, function(value, key) {
                        var mockFile = {
                            url: value[scope.propriedade],
                            status: dropzone.ADDED,
                            accepted: true,
                        };

                        if (scope.nomeOriginal) {
                            mockFile['name'] = value[scope.nomeOriginal];
                        }

                        dropzone.emit("addedfile", mockFile );

                        var array = value[scope.propriedade].split('.');
                        var extensao = array[(array.length -1)];

                        if (extensao === 'jpg') {
                            dropzone.emit("thumbnail", mockFile, value[scope.propriedade]);
                        } else {
                            dropzone.emit("thumbnail", mockFile, '/img/default-file.png');
                        }

                        // remove label de tamanho do padrão.
                        $('.dz-size').html('');

                        dropzone.files.push(mockFile);
                        dropzone.emit("complete", mockFile);
                    });
                }
                return;
            });

            dropzone.on('success', function (arquivo, dados) {
                var caminhoArquivo = dados.dados[0];
                arquivo.url = caminhoArquivo;
                var elemento = {};
                elemento[scope.propriedade] = caminhoArquivo;
                if (scope.nomeOriginal) {
                    elemento[scope.nomeOriginal] = arquivo.name;
                }
                scope.model.push(elemento);
                scope.$apply();
            });

            dropzone.on('removedfile', function(arquivo) {
                this.enable();
                angular.forEach(scope.model, function(valor, chave) {
                    if (arquivo.url == valor[scope.propriedade]) {
                        scope.model.splice(chave, 1);
                        scope.$apply();
                    }
                });
            });

            dropzone.on('maxfilesexceeded', function(arquivo) {
                this.removeFile(arquivo);
                alertify.error('Você não pode adicionar mais de 3 arquivos.');
            });
        }
    };
}])
/**
* Trata checkbox para relações N:N
*/
.directive('multiploCheckbox',['$timeout', function ($timeout) {

    //Verifica se item esta no array
    function inArray(array, item) {
        if (angular.isArray(array)) {
            for (var i = array.length; i--;) {
                if (array[i] == item) {
                    return true;
                }
            }
        }
        return false;
    }

    //Adiciona intem ao array
    function add(array, item) {
        array = angular.isArray(array) ? array : [];
        if(!inArray(array, item)) {
            array.push(item);
        }
        return array;
    }

    //Remove item do array
    function remove(array, item) {
        if (angular.isArray(array)) {
            for (var i = array.length; i--;) {
                if (array[i] == item) {
                    array.splice(i, 1);
                    break;
                }
            }
        }
        return array;
    }

    return {
        restrict: 'A',
        link: function (scope, element, attr) {

            // Valida se existe a propriedade eval
            var camposDinamicos = attr.$attr.hasOwnProperty('camposDinamicos');

            if (camposDinamicos) {
                var valor = scope.$eval($(element).val());
            } else {
                var valor = $(element).val();
            }
            //Verifica se o model está definido
            if(!angular.isDefined(scope.$eval(attr.multiploCheckbox))) {
                throw 'O model ' + attr.multiploCheckbox + ' não está definido!';
            }

            scope.$watch(attr.multiploCheckbox, function(valorInput) {
                if (angular.isDefined(valorInput)) {
                    $timeout(function() {
                        if(inArray(scope.$eval(attr.multiploCheckbox),valor)) {
                            $(element).attr('checked', true);
                        }
                    },0)
                }
                return;
            });

            $(element).on('change', function() {
                if ($(element).is(':checked')) {
                    add(scope.$eval(attr.multiploCheckbox), valor);
                } else {
                    remove(scope.$eval(attr.multiploCheckbox), valor);
                }
                scope.$apply();
            });
        }
    };
}])
.directive('toggle', function(){
    return {
        restrict: 'A',
        link: function(scope, element, attrs){
            if (attrs.toggle == "tooltip"){
                $(element).tooltip();
            }
            if (attrs.toggle == "popover"){
                $(element).popover();
            }
        }
    };
})
.directive('ngEnter', function () {
    return {
        link: function (scope, element, attrs) {
            element.bind("keypress", function (event) {
                var keyCode = event.which || event.keyCode;
                if (keyCode === 13) {
                    scope.$apply(function (){
                        scope.$eval(attrs.ngEnter);
                    });

                    if (!angular.isDefined(attrs.propagarEventos)) {
                        event.preventDefault();
                    }
                }
            });
        }
    }
})
.directive("ngFileSelect",['$http', '$window', function($http, $window){
    var helper = {
        verificaArquivo: function(item) {
            return angular.isObject(item) && item instanceof $window.File;
        },
        verificaImagem: function(file) {
            var type =  '|' + file.type.slice(file.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    };
    return {
        link: function($scope,el, attrs) {
            var status = {};
            var model = attrs.ngModel;

            $scope.imagemSrc = attrs.imgPadrao;
            status[0] = true;

            $scope.$watch(model, function(foto) {
                if(angular.isDefined(foto) && foto.length !== 0) {
                    $scope.imagemSrc = foto.fsCaminhoImagem;
                }
            });

            el.bind("change", function(e){

                status[0] = false;
                status[1] = true;

                $scope.file = (e.srcElement || e.target).files[0];

                if (!helper.verificaArquivo($scope.file)) return;
                if (!helper.verificaImagem($scope.file)) return;

                var reader = new FileReader();

                reader.onload = function (e) {
                    $scope.imagemSrc = e.target.result;
                }

                reader.readAsDataURL($scope.file, $scope);

                var fd = new FormData();
                fd.append('file', $scope.file);

                $http.post(attrs.urlUpload, fd, {
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined}
                })
                .success(function(response){
                    var caminhoArquivo = response.dados;
                    var elemento = {};
                    elemento[attrs.propriedade] = caminhoArquivo;
                    $scope.$eval(model).fsCaminhoImagem = elemento[attrs.propriedade][0];
                    status[0] = false;
                    status[1] = false;
                    status[2] = true;
                })
                .error(function(){
                    alertify.error('Ocorreu um erro inesperado ao fazer upload da imagem.');
                    status[0] = false;
                    status[1] = false;
                    status[3] = true;
                });
            })
            $scope.statusEnvioImagem = status;
        }
    }
}]);