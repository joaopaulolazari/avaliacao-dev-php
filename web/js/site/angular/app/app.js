'use strict';

var moduloApp = angular.module('siteApp', [
    // Sistema
    'site.app.controllers',
    'site.app.directives',
    'site.app.services',
    'site.app.filters',
    'site.app.factories',
    'site.app.templates',
    // Biblioteca de terceiros
    'ui.bootstrap'

]).config(['$interpolateProvider', function($interpolateProvider) {

    //Altera simbolo Angular
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');

}]).run(function ($rootScope, rotaService, modalService, errosService) {
    //Adiciona os serviços de rota e modal global
    $rootScope.rotaService = rotaService;
    $rootScope.modalService = modalService;
    $rootScope.errosService = errosService;

    /**
     * @description
     * Determines the number of elements in an array, the number of properties an object has, or
     * the length of a string.
     *
     * @param {Object|Array|string} obj Object, array, or string to inspect.
     * @param {boolean} [ownPropsOnly=false] Count only "own" properties in an object
     * @returns {number} The size of `obj` or `0` if `obj` is neither an object nor an array.
     */
    $rootScope.size = function size(obj, ownPropsOnly) {
        var count = 0, key;
        if (angular.isArray(obj) || angular.isString(obj)) {
            return obj.length;
        } else if (angular.isObject(obj)) {
            for (key in obj)
                if (!ownPropsOnly || obj.hasOwnProperty(key))
                    count++;
        }
        return count;
    };
});