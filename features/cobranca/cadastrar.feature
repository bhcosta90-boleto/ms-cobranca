Feature: Geração de cobranças

    Background: Cadastrando nova conta
        Then Eu estou criando uma nova credencial

    Scenario: Geração de uma cobrança simples
        Given Eu estou criando uma nova cobrança
            | key   | value |
            | valor | 150   |

    Scenario: Cobrança com o valor minimo definido como padrão que no caso é 7.50
        Given Eu estou criando uma nova cobrança com status '422'
            | key   | value |
            | valor | 2.0   |
        And I am validating a request
            | key                    | value                           |
            | [0][status]            | 422                             |
            | [0][message][valor][0] | The valor must be at least 7.5. |

    Scenario: Geração de uma cobrança com split com valor
        Given Eu estou criando uma nova cobrança
            | key                    | value              |
            | [splits][0][nome]      | henrique juliano 1 |
            | [splits][0][documento] | 46699875523        |
            | [splits][0][banco]     | 033                |
            | [splits][0][agencia]   | 1234               |
            | [splits][0][conta]     | 4567               |
            | [splits][0][valor]     | 20                 |
            | [splits][1][nome]      | henrique juliano 2 |
            | [splits][1][documento] | 46699875524        |
            | [splits][1][banco]     | 033                |
            | [splits][1][agencia]   | 1234               |
            | [splits][1][conta]     | 4567               |
            | [splits][1][valor]     | 20                 |

    Scenario: Geração de uma cobrança com split com porcentagem
        Given Eu estou criando uma nova cobrança
            | key                      | value              |
            | [splits][0][nome]        | henrique juliano 1 |
            | [splits][0][documento]   | 46699875523        |
            | [splits][0][banco]       | 033                |
            | [splits][0][agencia]     | 1234               |
            | [splits][0][conta]       | 4567               |
            | [splits][0][porcentagem] | 5                  |
            | [splits][1][nome]        | henrique juliano 2 |
            | [splits][1][documento]   | 46699875524        |
            | [splits][1][banco]       | 033                |
            | [splits][1][agencia]     | 1234               |
            | [splits][1][conta]       | 4567               |
            | [splits][1][porcentagem] | 5                  |
