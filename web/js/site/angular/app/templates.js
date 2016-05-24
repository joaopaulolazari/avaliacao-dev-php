'use strict';
angular.module('site.app.templates', ["modal-formulario.html", "modal-confirmacao.html", "modal-alerta.html"]);

angular.module("modal-formulario.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modal-formulario.html",
        "<div ng-if=\"modalOptions.titulo\" class=\"modal-header\">\n" +
            "<h3><% modalOptions.titulo %></h3>\n" +
            "<button type=\"button\" class=\"close md-close\" ng-click=\"modalOptions.fecharModal()\" aria-label=\"Fechar\" aria-hidden=\"true\">×</button>\n" +
        "</div>\n" +
        "<div class=\"modal-body form\" ng-include src=\"modalOptions.templateFormulario\"></div>\n"
    );
}]);

angular.module("modal-confirmacao.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modal-confirmacao.html",
        "<div class=\"modal-body\">\n" +
            "<div class=\"text-center\">\n" +
                "<div class=\"i-circle <%modalOptions.tipo%>\"><i class=\"fa fa-<%modalOptions.simbolo%>\"></i></div>\n" +
                "<h4 ng-bind-html=\"modalOptions.mensagem\"></h4>\n" +
            "</div>\n" +
        "</div>\n" +
        "<div class=\"modal-footer\">\n" +
            "<button class=\"btn btn-default btn-flat\" ng-click=\"modalOptions.sim(true)\">\n" +
                "<% modalOptions.btnSim %>\n" +
            "</button>\n" +
            "<button class=\"btn btn-danger btn-flat\" ng-click=\"modalOptions.nao(false)\">\n" +
                "<% modalOptions.btnNao %>\n" +
            "</button>\n" +
        "</div>\n"
    );
}]);

angular.module("modal-alerta.html", []).run(["$templateCache", function($templateCache) {
    $templateCache.put("modal-alerta.html",
        "<div class=\"modal-body\">\n" +
            "<div class=\"text-center\">\n" +
                "<div class=\"i-circle <%modalOptions.tipo%>\"><i class=\"fa fa-<%modalOptions.simbolo%>\"></i></div>\n" +
                "<h4 ng-bind-html=\"modalOptions.mensagem\"></h4>\n" +
            "</div>\n" +
        "</div>\n" +
        "<div class=\"modal-footer\">\n" +
            "<button class=\"btn btn-<%modalOptions.tipo%> btn-flat\" ng-click=\"modalOptions.ok(true)\">\n" +
                "<% modalOptions.btnOk %>\n" +
            "</button>\n" +
        "</div>\n"
    );
}]);