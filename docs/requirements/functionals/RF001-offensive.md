# RF-001: Gerenciamento de Dias de Estudos e Ofensivas

## Descrição

A funcionalidade "Dias de Estudos e Ofensivas" permite que os alunos rastreiem seus dias de estudos e mantenham uma contagem de ofensivas para medir sua consistência no estudo.

## Requisitos

1. **Registro Diário de Estudos**
   - O sistema deve registrar automaticamente após o aluno terminar o estudo do dia.
   - O sistema deve guardar a data do último dia de estudo, para poder validar a ofensiva.
   - Cada dia em que o aluno estuda deve ser marcado no calendário da semana.

2. **Redefinição da Ofensiva**
   - A ofensiva é uma contagem de quantos dias seguidos o aluno estudou.
   - Se o aluno ficar um ou mais dias sem estudar, a ofensiva deve ser redefinida para zero.

3. **Exibição de Ofensiva Atual**
   - O sistema deve exibir a ofensiva atual do aluno de forma visível, na página inicial após o login.

4. **Histórico de Estudos**
   - O sistema deve manter um histórico semanal dos dias de estudo concluído pelo aluno.
   - Caso o aluno não estude em algum dia, o mesmo fica sem marcar.

## Casos de Uso

1. **Registro Diário de Estudos**
   - O aluno acessa a plataforma e conclui o estudo do dia.
   - O sistema marca o dia no calendário da semana.
   - O sistema aumenta em 1 o número de ofensivas.

2. **Redefinição da Ofensiva**
   - Ao entrar no sistema verificar qual foi o último dia estudado. Se a data for inferior a do dia anterior, então a ofensiva será resetada.
   - Se o aluno não estudar em um dia, a ofensiva atual é redefinida para zero.

3. **Exibição de Ofensiva Atual**
   - A ofensiva atual do aluno é exibida na interface principal do sistema.
   - O aluno pode verificar quantos dias seguidos estudou.

4. **Histórico de Estudos**
   - O histórico semanal que mostra todos os dias de estudo registrados deve ficar na página principal.
   - O histórico inclui dias da semana atual com os dias estudados e não estudados.

## Critérios de Aceitação

- O sistema deve ser capaz de rastrear os dias de estudo do aluno de forma precisa.
- A ofensiva deve ser redefinida para zero se o aluno não estudar em um dia.
- A ofensiva deve ser exibida de forma clara para o aluno.
- O histórico semanal de estudos deve ser acessível e conter informações certas.

Voltar para [Requisitos](../README.md)
