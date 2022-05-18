# Anpad-SistemaEventos

##### Nosso projeto está usando Docker e Docker-composer, contem uma estrutura simples
##### O docker, está separado em containers para cada serviço, PHP-FPM, NGINX, MySQL
##### O utilitário "makefile", ajuda a interação com containers e processos de instalação
* OBS: em Ambiente DEV composer, nodejs e yarn ficam no mesmo container para facilitar

##### Versões: Symfony 5 Flex, Boostrap4, Jquery, PHP 7.4.5



### Setup do projeto
* Requerimentos
   *  Docker e Docker-compose instalados
   *  No momento está automatizado somente para ambientes Linux/BSD e macOS (Makefile) 
   
#### 1) Rodar `make build` vai construir os containers
#### 2) Rodar `make install` executa composer, yarn install e yarn dev
#### 3) Rodar `make start` vai executar os containers, logo já acessando `http://localhost`
#### 4) Rodando `make shell` acessa o shell do container
#### 5) Rodando `make webserver-debug` logs do container webserver
#### 6) Rodando `make php-debug` logs do container php-fpm
#### 7) Rodando `make stop` para com todos containers
#### 8) Rodando `make hmr` Cria servidor local de HMR
#### 9) Rodando `make release-build` Cria servidor local de HMR
#### 10) Rodando `make test` Cria servidor local de HMR
#### 11) Rodando `make help` mostra todos comandos que pode ser executado