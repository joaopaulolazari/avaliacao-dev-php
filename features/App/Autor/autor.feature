#language: pt

Funcionalidade: CRUD de autor
    Devo ser capaz de listar os autores
    Devo ser capaz de cadastrar um autor
    Devo ser capaz de editar um autor
    Devo ser capaz de remover um autor

@javascript
Cenário: Cadastrar
Dado que eu acesso "/"
    E eu clico no link "Autores"
    E eu espero 1 segundos
    # E eu clico no link "autor/"
    Então eu deveria ver "Autores"
    E eu espero 1 segundos
    E eu clico no link "autor/cadastrar"
    E eu espero 1 segundos
    Então eu deveria ver "Cadastrar Autor"
    Então limpo todos os campos do formulário

    Entao eu deveria ver "Nome"
    Entao eu deveria ver "Notação autor"

    Quando eu preencho "autor_fsNome" com o valor "João Paulo"
    Então limpo todos os campos do formulário
    E eu clico no botão "Salvar"
    E eu espero 1 segundos
    Então eu deveria ver alertify de erro com a mensagem "Verifique o(s) erro(s) no formulário"
    Então no campo "autor[fsNome]" eu deveria ver a mensagem de erro "Esse campo é obrigatório"
    Então no campo "autor[fsNotacaoAutor]" eu deveria ver a mensagem de erro "Esse campo é obrigatório"

    Quando eu preencho "autor_fsNome" com o valor "João Paulo"
    Quando eu preencho "autor_fsNotacaoAutor" com o valor "JPL"

    E eu clico no botão "Salvar"
    Então eu deveria ver "Registro salvo com sucesso!"
    E eu clico no botão "Ok"

@javascript
Cenário: Listagem
Dado que eu acesso "autor/"
    Então eu deveria ver "Autores"
    E eu espero 1 segundos

    Então na tabela "table table-striped table-bordered" deve conter:
    | Nome     |
    | João Paulo  |

















  Então limpo todos os campos do formulário
  E eu clico no botão "Buscar"
  Então eu deveria ver alertify de erro com a mensagem "Você deve preencher pelo menos um campo de pesquisa"
  E eu espero 2 segundos
  Quando eu preencho o campo "buscaColeta_fdDataColeta" com o valor "<hoje>"
  E eu clico no botão "Buscar"
  E eu espero a mensagem de Carregando
  Então eu espero tooltip com texto "Editar Coleta"
  Então eu espero tooltip com texto "Cancelar Coleta"
