
<script>
function modificaDomanda(anno, codice_domanda, sezione) {
    modal_page_new(
        'modifica_{$codice_domanda}_{$tab_name}',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/form/{$tab_name}/{$codice_domanda}/'+sezione+'?anno='+anno,
        'large', 
        loadConfigPage, 
        loadConfigPage
    );
}
</script>

<div class="field" style="font-size:1em;">

    <label>Anno</label>
    <p>{$domanda.anno}</p>

</div>

<div class="field" style="font-size:2em;">

    <label>Testo della domanda</label>
    <p>{$domanda.descrizione}</p>

</div>

<div class="field" style="font-size:2em;">

    <label>Help breve (inline)</label>
    <p>{$domanda.help_breve|default:"n.d."}</p>

</div>


<div class="ui button" onclick="modificaDomanda({$anno}, '{$codice_domanda}', '{$sezione}');">Modifica</div>
