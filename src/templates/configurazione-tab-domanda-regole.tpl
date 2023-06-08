
<script>
function modificaRegole(anno, codice_domanda, sezione) {
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


<div style="font-size:1.5em;">

    <h1>Visibilità</h1>
    <div class="field">

        <label>Base</label>
        <p>{if $rules.default_visibile == 'S'}SI{else}NO{/if}</p>

    </div>
    <div class="field">

        <label>Valutazione</label>
        <p>{if $rules.visibile|default:$rules.default_visibile == 'S'}SI{else}NO{/if}</p>

    </div>
    

    <h1>Obbligatorietà</h1>
    <div class="field">

        <label>Base</label>
        <p>{if $rules.default_obbligatoria == 'S'}SI{else}NO{/if}</p>

    </div>
    <div class="field">

        <label>Valutazione</label>
        <p>{if $rules.obbligatoria|default:$rules.default_obbligatoria == 'S'}SI{else}NO{/if}</p>

    </div>

</div>


<div class="ui button" onclick="modificaRegole({$anno}, '{$codice_domanda}', '{$sezione}');">Modifica</div>