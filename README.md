# Presenças Moodle

Sistema para verificar a presença dos alunos com base nos logs do Moodle.

Esse sistema foi desenvolvido para auxiliar na marcação das presenças dos alunos do [IFBA](http://www.ifba.edu.br) no SUAP nas aulas realizadas através do AVA-Moodle durante a pandemia.

Consiste de uma interface onde o professor irá informar as datas de início e fim do curso, horário das aulas síncronas, bem como os dias da semana quando ocorriam as aulas síncronas e assíncronas e receber os [arquivos de log](https://youtu.be/sZcebrgLgNo) de acesso dos estudantes às ferramentas usadas nas aulas síncronas (BBB ou Google Meet) exportados no formato CSV.

## Como utilizar

Para executar o sistema é necessário que exista um servidor web funcionando em sua máquina. Podendo ser Apache, NGINX, ou até o servidor integrado do próprio PHP.

Para usar o servidor integrado do PHP basta usar o comando abaixo (alterando local-do-projeto pelo local do projeto em sua máquina).

```console
php -S 0.0.0.0:8000 -t local-do-projeto
```

Com esse comando, o servidor irá responder na porta 8000, logo para acessar o sistema basta abrir o navegador e acessar o endereço localhost:8000.