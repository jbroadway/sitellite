{section name=vars loop=$vars}
{if $show == 'summary'}
	var {$vars[vars].var_name}, {$vars[vars].sdesc}<br>
{else}
	<a name="{$vars[vars].var_dest}"></a>
	<p></p>
	<h4>{$vars[vars].var_name} = <span class="value">{$vars[vars].var_default|replace:"\n":"<br>\n"|replace:" ":"&nbsp;"|replace:"\t":"&nbsp;&nbsp;&nbsp;"}</span></h4>
	<div class="indent">
		<p class="linenumber">[line {if $vars[vars].slink}{$vars[vars].slink}{else}{$vars[vars].line_number}{/if}]</p>
		{include file="docblock.tpl" sdesc=$vars[vars].sdesc desc=$vars[vars].desc tags=$vars[vars].tags}
		<p><b>Type:</b> {$vars[vars].var_type}</p>
		<p><b>Overrides:</b> {$vars[vars].var_overrides}</p>
	</div>
	<p class="top">[ <a href="#top">Top</a> ]</p>
{/if}
{/section}
