# Desafio desenvolvedor PHP - Onfly
 * Autor: Wasgton Rodrigues Junior

Teste para avaliar tecnicamente candidato para a vaga de desenvolvedor PHP

## Projeto Travel Order Microservice

## Introdução
O **Travel Order Microservice** é um serviço desenvolvido em PHP utilizando o framework **Laravel (v11.40.0)**. Este sistema gerencia ordens de viagem (travel orders), suportando fluxos de aprovação, solicitação e cancelamento. O projeto é modular e extensível, com suporte para integração com outros microsserviços.

---

## Como rodar localmente
### Pré-requisitos
Certifique-se de ter as dependências abaixo instaladas:
- [x] Docker;
- [x] Docker compose;

### Executando
- Clone o repositório.
- Inicie os containers via docker compose :
```shell script
docker compose up -d
```

###

- Entre no container usando o comando
```shell script
docker compose exec app bash
```
- Para facilitar a execução eu criei um comando composer para fazer a instalação das dependencias, copiar o .env e executar as migrations

```shell script
composer run install
```
######  _Apenas por se tratar de um teste e para facilitar a execução deixei o .env.exemple configurado com a configuração de banco conforme o docker-compose .yml._


---

## Fluxo de Estados da Ordem de Viagem
O microsserviço gerencia o fluxo de ordens de viagem através do padrão State. Os estados disponíveis são:
1. **Solicitado (RequestedState)**: Indica a criação de uma nova ordem.
2. **Aprovado (ApprovedState)**: Marca a ordem como aprovada.
3. **Cancelado (CancelledState)**: Marca a ordem como cancelada.

O estado é gerenciado através de transições implementadas em classes específicas localizadas no diretório `app/States/`.

---

## Testes
Os testes são escritos utilizando **PHPUnit**. Para executar os testes, utilize o comando:

```shell script
php artisan test
```
Caso queira o relatorio de cobertura dos testes utilize o comando:

```shell script
php artisan test --coverage
```
---
> De ambas as formas serão executados todos os testes da aplicação. É necessário estar com banco de dados configurado.

### Endpoints
Caso queira executar testes manuais utilize os endpoints abaixo a partir da URL base http://localhost

- POST .................. api/v1/login 
- POST .................. api/v1/register
- POST .................. api/v1/travel-orders 
- PUT .................... api/v1/travel-orders/approve/{travelOrder} 
- PUT .................... api/v1/travel-orders/cancel/{travelOrder} 
- GET .................... api/v1/travel-orders/{travelOrder}
- GET .................... api/v1/travel-orders
    
    - create_period_start - date
    - create_period_end - date
    - departure_period_start - date
    - departure_period_end - date
    - destination - string
    - applicant_name - string
    - status - REQUESTED = 1, APPROVED = 2, CANCELLED = 3
    

---
## Conceitos utilizados:
Foram utilizados nesse projeto os conceitos:
- `Desenvolvimento orientado a testes`, que facilita o entendimento das funcionalidade que estão sendo desenvolvidas, tendo assim etapas de criação e validação formando um ciclo que garnado uma melhor qualidade de entrega de código.
- `SOLID`, alguns princípios para facilitar a manutenção, criação e entendimento do software.
