/*
* Funções globais JavaScript
*/

window.getGlobalScope = function() {
    return angular.element($("[ng-app=siteApp]")).scope();
};

/*
 * AplicaParamsRota - Aplica  parâmetros a string da rota
 *
 * @param String - string com a rota
 * @param Object - objeto associativo com parâmetros para substituir na rota
 *
 * @return String - rota formatada
*/
window.aplicaParamsRota = function(rota, parametros) {
    for (var key in parametros) {
        rota = rota.replace('{' + key + '}', parametros[key]);
    }
    //Aplica formato padrão 'json' caso exista o parâmetro
    return rota.replace('{_format}', 'json');
}

/**
 * Define um cookie
 *
 * @param cname nome da chave do cookie
 * @param cvalue valor do cookie
 * @param exdays numero de dias para expirar
 */
function setCookie(cname, cvalue, exdays) {

    deleteCookie(cname);

    // valor padrao de 1 dia para expirar o cookie
    if ((exdays == null) || (typeof exdays == "undefined")) {
        exdays = 1;
    }

    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));

    // obtem a data de expiracao do cookie
    var expires = "expires="+d.toUTCString();

    // grava o cookie
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}

/**
 * Verifica se um cookie existe
 *
 * @param cname
 * @returns {boolean}
 */
function checkCookie(cname) {
    return getCookie(cname) ? true : false;
}

/**
 * Retorna o valor de um cookie de acordo com a chave
 *
 * @param cname
 * @returns {string, boolean}
 */
function getCookie(cname) {

    var name = cname + "=";
    var ca = document.cookie.split(';');

    // pega o valor do cookie
    for (var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }

    return false;
}

/**
 * Deleta um cookie
 *
 * @param cname
 * @returns {boolean}
 */
function deleteCookie(cname){

    // W3C: Deleting a cookie is very simple. Just set the expires parameter to a passed date
    document.cookie = cname +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';

    return true;
}