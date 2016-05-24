'use strict';

/* Filters */
// Formata a primeira letra de cada palavra pra maiúscula
angular.module('site.app.filters', [])
.filter('primeiraMaiuscula', function() {
    return function(str) {

        if (str == 'undefined' || typeof(str) != 'string')
            return '';

        str = str.toLowerCase();
        return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,function($1){return $1.toUpperCase();});
    };
})
.filter('formataData', function() {
    return function (data, formato, newFormato) {

        if(!formato)
            formato = 'DD/MM/YYYY';
        if(!newFormato)
            newFormato = 'DD/MM/YYYY';

        data = moment(data, formato);

        if (!data.isValid()) {
            return '';
        }

        return data.format(newFormato)
    }
})
.filter('formataCpfCnpj', function() {
    return function (cpfCnpj) {
        if (angular.isDefined(cpfCnpj)) {
            if (cpfCnpj.length == 11) {
                return cpfCnpj.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4");
            } else if (cpfCnpj.length == 14) {
                return cpfCnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5");
            } else {
                return 'CPF/CNPJ Inválido';
            }
        }
    }
})
.filter('formataHora', function() {
    return function (data, formato, newFormato) {
        if(!angular.isDefined(formato)){
            formato = 'HH:mm:ss';
        }
        if(!angular.isDefined(newFormato)){
            newFormato = 'HH:mm';
        }

        data = moment(data, formato);

        if (!data.isValid()) {
            return '';
        }

        return data.format(newFormato)
    }
})
.filter('formataDataHora', function() {
    return function (data, formato, newFormato) {
        if(!angular.isDefined(formato)){
            formato = 'DD/MM/YYYY HH:mm:ss';
        }
        if(!angular.isDefined(newFormato)){
            newFormato = 'DD/MM/YYYY HH:mm';
        }

        data = moment(data, formato);

        if (!data.isValid()) {
            return '';
        }

        return data.format(newFormato)
    }
})
.filter('Hora', function() {
    return function (data, formato, newFormato) {
        if(!angular.isDefined(formato)){
            formato = 'DD/MM/YYYY HH:mm:ss';
        }
        if(!angular.isDefined(newFormato)){
            newFormato = 'HH:mm';
        }

        data = moment(data, formato);

        if (!data.isValid()) {
            return '';
        }

        return data.format(newFormato)
    }
})
.filter('telefone', function() {
    return function(telefone) {

        if (angular.isDefined(telefone)) {

            var str = telefone+ '';
                str = str.replace(/\D/g,'');
                if(str.length === 11 ){
                    str=str.replace(/^(\d{2})(\d{5})(\d{4})/,'($1) $2-$3');
                }else{
                    str=str.replace(/^(\d{2})(\d{4})(\d{4})/,'($1) $2-$3');
                }
            return str;
        }
    };
})
.filter('cep', function() {
    return function(cep) {
        if (angular.isDefined(cep)) {
            var str = cep+ '';
                str = str.replace(/\D/g,'');
                str=str.replace(/^(\d{2})(\d{3})(\d)/,"$1.$2-$3");
            return str;
        };
    }
})
.filter('limite', ['$filter', function($filter) {
    return function(value, limite) {
        if (angular.isDefined(value)) {
            var str = (value.length > limite)?'...':'';
            return $filter('limitTo')(value, limite)+str;
        };
    }
}])
.filter('comparaMaiorDataHora', ['$filter', function($filter) {
    return function (dataInicial, dataFinal) {

        if (angular.isDefined(dataInicial)) {

            // Se a data final não for definida ela será a data atual now()
            if(!angular.isDefined(dataFinal)) {
                dataFinal = moment();
                dataFinal = $filter('formataData')(dataFinal, 'YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm:ss');
            } else {
                dataFinal = $filter('formataData')(dataFinal, 'DD/MM/YYYY HH:mm:ss','YYYY-MM-DD HH:mm:ss');
            }
            dataInicial = $filter('formataData')(dataInicial, 'DD/MM/YYYY HH:mm:ss','YYYY-MM-DD HH:mm:ss');
            return moment(dataInicial).isBefore(dataFinal);
        }

        return 'Parâmetros inválidos';
    }
}])
.filter('comparaMaiorData', ['$filter', function($filter) {
    return function (dataInicial, dataFinal) {

        if (angular.isDefined(dataInicial)) {

            // Se a data final não for definida ela será a data atual now()
            if(!angular.isDefined(dataFinal)) {
                dataFinal = moment();
                dataFinal = $filter('formataData')(dataFinal, 'YYYY-MM-DD','YYYY-MM-DD');
            } else {
                dataFinal = $filter('formataData')(dataFinal, 'DD/MM/YYYY','YYYY-MM-DD');
            }
            dataInicial = $filter('formataData')(dataInicial, 'DD/MM/YYYY','YYYY-MM-DD');
            return moment(dataInicial).isBefore(dataFinal);
        }

        return 'Parâmetros inválidos';
    }
}])
.filter('comparaDatasIguais', ['$filter', function($filter) {
    return function (primeiraData, segundaData) {

        if (angular.isDefined(primeiraData)) {

            // Se a data final não for definida ela será a data atual now()
            if(!angular.isDefined(segundaData)) {
                segundaData = moment();
                segundaData = $filter('formataData')(segundaData, 'YYYY-MM-DD','YYYY-MM-DD');
            } else {
                segundaData = $filter('formataData')(segundaData, 'DD/MM/YYYY','YYYY-MM-DD');
            }

            primeiraData = $filter('formataData')(primeiraData, 'DD/MM/YYYY','YYYY-MM-DD');
            return moment(primeiraData).isSame(segundaData);
        }

        return 'Parâmetros inválidos';
    }
}])
.filter('comparaMaiorHora', ['$filter', function($filter) {
    return function (horaInicial, horaFinal) {

        // Se a hora inicial não for definida ela será a hora atual now()
        if(!angular.isDefined(horaInicial)) {
            horaInicial = moment('HH:mm:ss');
        } else {
            horaInicial = moment(horaInicial, 'HH:mm:ss');
        }

        // Se a hora final não for definida ela será a hora atual now()
        if(!angular.isDefined(horaFinal)) {
            horaFinal = moment();
            // horaFinal = $filter('formataHora')(horaFinal, 'YYYY-MM-DD HH:mm:ss', 'HH:mm:ss');
        } else {
            horaFinal = horaFinal = moment(horaFinal, 'HH:mm:ss', 'HH:mm:ss');
        }

        return moment(horaInicial).isBefore(horaFinal);
    }
}])
.filter('stringData', function() {
    return function(ftTempo) {
        if (!ftTempo) {
            return;
        }

        var tempo = ftTempo.split(':');

        var now = moment().format('YYYY-MM-DD 00:00:00');
        var date = moment({hour: tempo[0], minute: tempo[1]});
        var diferenca = date.diff(now, 'minutes');
        var d = moment.duration(diferenca, "minutes");

        var string = '';

        if (d.get('days')) {
            string = string+d.get('days')+' dia(s) ';
        }
        if (d.get('hours')) {
            string = string+d.get('hours')+' hr(s) ';
        }
        if (d.get('minutes')) {
            string = string+d.get('minutes')+' min(s) ';
        }
        return string;
    };
})
.filter('calculaData', function() {
    return function(data) {
        if (!data) {
            return;
        }

        var tempo = data.split(':');

        var diasUteis = Math.round(tempo[0] / 8);
        var dataAtual = new Date();
        var previsao = new Date();

        previsao.setDate(dataAtual.getDate() + diasUteis);

        var dataBR = previsao.getDate() + "/" + (previsao.getMonth()+1) + "/" + previsao.getFullYear();
        return dataBR;
    };
})
.filter('unique', function() {
   return function(collection, keyname) {
        var output = [],
        keys = [];

        angular.forEach(collection, function(item) {
            var obj = keyname.split('.');
            var key = item;
            for (var i = 0; i < obj.length; i++) {
                key = key[obj[i]];
            }

            if(keys.indexOf(key) === -1) {
                keys.push(key);
                output.push(item);
            }
        });

      return output;
   };
});