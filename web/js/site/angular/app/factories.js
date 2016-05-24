'use strict';

angular.module('site.app.factories', [])
.factory('formFactory', ['$rootScope', '$timeout', '$log', function ($rootScope, $timeout, $log) {

    var servico = {};

    servico.validar = function(formulario, campos, exibirErros) {

        if (angular.isUndefined(exibirErros)){
            exibirErros = true;
        }

        var formNome = formulario.$name;
        var valido = true;

        angular.forEach(campos, function(campo, indice) {

            formulario[campo].$validate();
            if (!formulario[campo].$valid) {
                valido = false;
            }
        });

        if (!valido && exibirErros) {
            servico.exibirErros(formulario, campos);
        }

        return valido;
    }

    servico.exibirErros = function(formulario, campos, alertifyAtivo) {
        if (angular.isUndefined(alertifyAtivo)){
            alertifyAtivo = true;
        }

        formulario.$setSubmitted(true);
        $rootScope.$broadcast('form-submitted', {formNome:formulario.$name});
        $rootScope.$broadcast('exibir-erros', {formNome:formulario.$name, campos:campos, chamadaFormFactory: true});

        $(".load-transition:visible").remove();

        if (alertifyAtivo) {
            alertify.error('Verifique o(s) erro(s) no formulário');
        }

    }

    servico.validarFormPesquisa = function(formulario, campos, mensagem) {
        if (angular.isUndefined(mensagem)){
            mensagem = 'Você deve preencher pelo menos um campo de pesquisa';
        }

        var valido = false;

        for (var campo in campos) {
            var keyCampo = campos[campo];
            //Verifica se o campo existe no formulário
            if (angular.isUndefined(formulario[keyCampo])) {
                $log.error('O campo ' + keyCampo + ' não existe no formulário');
                return;
            };
            var viewValue = formulario[keyCampo].$viewValue;
            var campoValido = servico.validar(formulario, [keyCampo], false);

            if (
                campoValido &&
                angular.isDefined(viewValue) &&
                viewValue != ""
            ) {
                valido = true;
            } else if (!campoValido) {
                servico.exibirErros(formulario, [keyCampo], false);
                valido = false;
                break;
            }
        }

        if(!valido) {
            alertify.error(mensagem);
        }
        return valido;
    }

    servico.limparErros = function(formulario) {
        var formNome = formulario.$name;
        $rootScope.$broadcast('limpa-erros', {formNome:formNome});
    }

    return servico;
}]);