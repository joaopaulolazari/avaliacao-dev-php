'use strict';

/* Controllers */
angular.module('site.app.controllers', [])
.controller('MaterialController', ['$scope', '$http', '$timeout', 'formFactory', function ($scope, $http, $timeout, formFactory) {
    $scope.materialLivro =  {
       'autores' : new Array(),
    };

    $scope.materialDicionario =  {
       'autores' : new Array(),
    };

    $scope.index = function() {
        var url = $scope.rotaService.path('api_app_materiais');
        $http.get(url)
        .then(
            function successCallback(response) {
                $scope.materiais = response.data.dados.materiais;
            },
            function errorCallback(response) {
                $scope.modalService.alerta('Não foi possível listar os materiais.', 'danger');
            }
        );
    };

    $scope.editarLivro = function() {
        var url = $scope.rotaService.path('api_app_material_livro', {'id': $scope.rotaService.get('id')});
        $http.get(url)
        .then(
            function successCallback(response) {
                $scope.materialLivro = response.data.dados.materialLivro;
            },
            function errorCallback(response) {
                $scope.modalService.alerta(response.data.mensagem, 'danger').then(function(){
                    window.location.href = $scope.rotaService.path('site_material');
                });
            }
        );
    };

    $scope.editarMaterialDicionario = function() {
        var url = $scope.rotaService.path('api_app_material_dicionario', {'id': $scope.rotaService.get('id')});
        $http.get(url)
        .then(
            function successCallback(response) {
                $scope.materialDicionario = response.data.dados.materialDicionario;
            },
            function errorCallback(response) {
                $scope.modalService.alerta(response.data.mensagem, 'danger').then(function(){
                    window.location.href = $scope.rotaService.path('site_material');
                });
            }
        );
    };

    $scope.removerMaterial = function(material, index) {
        $scope.modalService.confirmar('Tem certeza que deseja remover o Material?', 'warning').then(function(resultado) {
            if(resultado) {
                if (material.fsTipoMaterial == 'livro') {
                    var url = $scope.rotaService.path('api_app_material_livro_remover', {'id': material.fnMaterialId});
                } else if(material.fsTipoMaterial == 'dicionario') {
                    var url = $scope.rotaService.path('api_app_material_dicionario_remover', {'id': material.fnMaterialId});
                } else {
                    $scope.modalService.alerta('Tipo de material não encontrado', 'danger').then(function(){
                        window.location.href = $scope.rotaService.path('site_material');
                    });
                }

                $http.delete(url)
                .success(function(dados, status) {
                    if (status == 200) {
                        $scope.materiais.splice(index, 1);
                        $scope.modalService.alerta(dados.mensagem, 'success');
                    }
                })
                .error(function(dados, status) {
                    $scope.modalService.alerta(dados.mensagem, 'danger');
                });
            }
        });
    };

    $scope.editarMaterial = function(material) {

        if (material.fsTipoMaterial == 'livro') {
            var url = $scope.rotaService.path('site_material_livro_editar', {'id': material.fnMaterialId});
        } else if(material.fsTipoMaterial == 'dicionario') {
            var url = $scope.rotaService.path('site_material_dicionario_editar', {'id': material.fnMaterialId});
        } else {
            $scope.modalService.alerta('Tipo de material não encontrado', 'danger').then(function(){
                window.location.href = $scope.rotaService.path('site_material');
            });
        }

        window.location.href = url;
    }
}]);

angular.module('siteApp').requires.push('site.app.controllers');