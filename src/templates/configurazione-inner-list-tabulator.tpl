

<script language="JavaScript" type="text/javascript">
var tabulator;
$(function(){
    {$tabulator->display("tabulator", "false", null, "rowClick")}
});
function rowClick(e, row) {
    var record_code = row.getData()['code'];
    modal_page_new(
        'modificaAttivita',
        //'/admin/form/{$context_name}/{$object_code}/update?{$pks}='+record_code,
        //'{$BASE_URL}/admin/objects/{$object_code}/update/'+record_code,
        '{$APP.url}/wizard/update?record_code='+record_code,
        'overlay fullscreen', 
        refreshTabulatorList, 
        refreshTabulatorList
    );
}{*/*
function onHideTemplateField() {
    var url = "{$APP.url}/{$template->code()}/list";
    redirect(url);
}*/*}
function refreshTabulatorList() {
    tabulator.setData();
}

</script>

<div id="tabulator"></div>
