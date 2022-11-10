  <img src="https://drive.google.com/uc?id=1XPWLjUo2-j8iGw07ALcxu7oqJ3nkl2Ho" alt="Rocketseat+"/>

# PHP via Web e Cli

Este repositório exemplifica uma aplicação com mesma funcionalidade, mas que pode ser executado tanto via terminal (CLI) como via navegador (WEB).

A proposta é consultar o resultado da loteria da MegaSena no site da Caixa Econômica Federal.

![Slide 1](./_assets/loto-mega-sena.png)

E exibir de maneira personalizada conforme o objetivo da aplicação.
Veja abaixo os recortes de tela.

## Utilização via CLI

Passe o arquivo do código-fonte, `loto.php`, como argumento do interpretador do PHP: `php ./loto.php`.

![Slide 1](./_assets/app-cli.png)

Em um ambiente Unix (Linux ou macOS), usando hashbang na primeira linha do arquivo e definindo permissão de execução `+x`, se torna possível chamar diretamente pelo arquivo: `./loto.php`

## Utilização via WEB

Basta servir o arquivo do código-fonte, `loto.php`, através de um serviço Web, como Nginx, Apache ou outro.

![Slide 1](./_assets/app-web.png)

Uma forma mais simples pode ser executar o próprio interpretador do PHP como servidor _standalone_ usando a chamada `php -S 0.0.0.0:80`, onde nesse caso a porta será a padrão 80.

O arquivo `index.php` foi criado como um facilitador para que o serviço Web carregue por padrão a aplicação, sem precisar informar o nome do arquivo com a aplicação de fato.

## Endereços Web

- Endereço da página oficial dos sorteios da Mega-Sena:
  - https://loterias.caixa.gov.br/Paginas/Mega-Sena.aspx
- Endereço da API:
  - https://servicebus2.caixa.gov.br/portaldeloterias/api/megasena/2537

## Slides da aula

![Slide 1](./_assets/Slide1.png)

![Slide 2](./_assets/Slide2.png)

![Slide 3](./_assets/Slide3.png)

![Slide 4](./_assets/Slide4.png)

![Slide 5](./_assets/Slide5.png)

![Slide 6](./_assets/Slide6.png)

![Slide 6](./_assets/Slide7.png)

![Slide 6](./_assets/Slide8.png)

## Rocketseat+

| [<img src="https://avatars.githubusercontent.com/u/665373?v=4" width="75px;"/>](https://github.com/sergiocabral) |
| :-: |
|[sergiocabral.com](https://sergiocabral.com)|