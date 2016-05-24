'use strict';

/* Controllers */
angular.module('site.app.controllers', [])
.controller('AutorController', ['$scope', '$http', '$timeout', 'formFactory', function ($scope, $http, $timeout, formFactory) {

    $scope.index = function() {
        var url = $scope.rotaService.path('api_app_autor_buscar_todos');
        $http.get(url)
        .then(
            function successCallback(response) {
                $scope.autores = response.data.dados.autores;
            },
            function errorCallback(response) {
                $scope.modalService.alerta('Não foi possível listar os autores.', 'danger');
            }
        );
    };

    $scope.cadastrar = function() {
    };

    $scope.editar = function() {
        var url = $scope.rotaService.path('api_app_autor', {'id': $scope.rotaService.get('id')});
        $http.get(url)
        .then(
            function successCallback(response) {
                $scope.autor = response.data.dados.autor;
            },
            function errorCallback(response) {
                $scope.modalService.alerta(response.data.mensagem, 'danger').then(function(){
                    window.location.href = $scope.rotaService.path('site_autor');
                });
            }
        );
    };

    $scope.removerAutor = function(id, index) {
        $scope.modalService.confirmar('Tem certeza que deseja remover o Autor?', 'warning').then(function(resultado) {
            if(resultado) {
                var url = $scope.rotaService.path('api_app_autor_remover', {'id': id});
                $http.delete(url)
                .success(function(dados, status) {
                    if (status == 200) {
                        $scope.autores.splice(index, 1);
                        $scope.modalService.alerta(dados.mensagem, 'success');
                    }
                })
                .error(function(dados, status) {
                    $scope.modalService.alerta(dados.mensagem, 'danger');
                });
            }
        });
    }
}]);

angular.module('siteApp').requires.push('site.app.controllers');