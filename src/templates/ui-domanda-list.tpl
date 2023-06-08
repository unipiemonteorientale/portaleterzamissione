<div class="field" style="margin: 2px;">
    {if $style == "horizontal"}
        <div class="ui horizontal basic primary label">{$schema.title|default:$schema.name}</div> {if $schema.format_modifier}{$props.value}{else}{$props.value}{/if}
    {elseif $action_name == "print"}
        <div class="field_print_value {$schema.css_class}">{$props.value|default:"&nbsp;"} {*if $schema.format_modifier}{$props.value}{else}{$props.value}{/if*}</div>
        <div class="field_print_label">{$schema.title|default:$schema.name}</div>
    {elseif $action_name == "read"}
        <div class="field_read_value {$schema.css_class}">{$props.value|default:"&nbsp;"} {*if $schema.format_modifier}{$props.value}{else}{$props.value}{/if*}</div>
        <div class="field_read_label  {$schema.css_class}">{$schema.title|default:$schema.name}</div> {* <label> *}
        <input type="hidden" name="{$schema.name}" value="{$props.value}" />
    {else}
    <label>{$schema.title}</label>
    
    
    {/if}
</div>