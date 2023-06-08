
<table class="ui celled table">
  <tdead>
  </tdead>
  <tbody>
    <tr>
        <td class="active collapsing">UID</td>
        <td>{$info.uid}</td>
    </tr>
    <tr>
        <td class="active">Matricola</td>
        <td>{$info.matricola}</td>
    </tr>
    <tr>
        <td class="active">Cognome</td>
        <td>{$info.cognome}</td>
    </tr>
    <tr>
        <td class="active">Nome</td>
        <td>{$info.nome}</td>
    </tr>
    <tr>
        <td class="active">Email</td>
        <td>{$info.email}</td>
    </tr>
    <tr>
        <td class="active">Struttura</td>
        <td>
            {$info.decostruttura}
            {if $info.servizio|count_characters}<br>{$info.servizio}{/if}
            {if $info.settore|count_characters}<br>{$info.settore}{/if}
        </td>
    </tr>
  </tbody>
</table>