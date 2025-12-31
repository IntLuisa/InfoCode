<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Contrato - N.: {{ $contract->service_id }}-{{ $contract->id }}</title>
    {!! 
        "<style>" . file_get_contents(public_path() . '/../resources/views/pdf/styles.css') . "</style>"
    !!}
</head>

<body>
    <h2 class="title">CONTRATO N.: {{ $contract->service_id }}-{{ $contract->id }}</h2>

    <h3 class="subtitle center">INSTRUMENTO PARTICULAR DE CONTRATO DE VENDA DE PLAYGROUND DA KRENKE PERNAMBUCO</h3>

    São Partes neste instrumento:<br />

    <p class="justify">
        De um lado,<br />
        a) A <b>REVENDEDORA KRENKE PERNAMBUCO LTDA</b> empresa com sede na Rua Sirinhaém, 336, Boa Vista II, 1º Andar,
        Caruaru,
        Estado de Pernambuco, inscrita no CNPJ sob nº <b>54.294.136/0001-80</b>, neste ato representada na forma
        prevista em
        seu
        contrato social, pelo Sr. Aldenir Tiburtino de Arruda Paes, brasileiro, casado, empresário, RG. 3593166 - SSP/PE
        e
        CPF 782.573.284-72, doravante designada simplesmente <b>KRENKE PERNAMBUCO</b>.<br /><br />

    </p>

    <p class="right">

        Caruaru/PE,
        {{ config('dates')['WEEK_DAYS'][date('N') - 1]}}, {{ date('d') }} de
        {{ config('dates')['MONTH_NAMES'][date('n') - 1]}} de
        {{ date('Y')}}.
    </p>

    <br /><br /><br />
    <table class="no-border">
        <tr>
            <td class="center">_______________________________________________________<br />
                <b>REVENDEDORA KRENKE PERNAMBUCO LTDA</b><br />
                CNPJ 54.294.136/0001-80<br />
                Representada por:<br />
                ALDENIR TIBURTINO DE ARRUDA PAES<br />
                CNAI 7285
            </td>
            
        </tr>
        <tr>
            <td colspan="2"><br /><br /><br />Testemunhas:
            </td>
        </tr>
        <tr>
            <td class="center">_______________________________________________________
            </td>
            <td class="center">_______________________________________________________
            </td>
        </tr>
    </table>
</body>

</html>