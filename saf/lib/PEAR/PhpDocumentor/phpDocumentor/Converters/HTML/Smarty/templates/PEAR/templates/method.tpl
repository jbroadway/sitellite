{section name=methods loop=$methods}
{if $show == 'summary'}
	<p>{if $methods[methods].ifunction_call.constructor}constructor {elseif $methods[methods].ifunction_call.destructor}destructor {else}method {/if}{$methods[methods].function_call}, {$methods[methods].sdesc}</p>
{else}
	<a name="{$methods[methods].method_dest}"></a>
	<p></p>
	<h3>{$methods[methods].function_name}</h3>
	<div class="indent">
		<p>
		<code>{$methods[methods].function_return} {if $methods[methods].ifunction_call.returnsref}&amp;{/if}{$methods[methods].function_name}(
{if count($methods[methods].ifunction_call.params)}
{section name=params loop=$methods[methods].ifunction_call.params}
{if $smarty.section.params.iteration != 1}, {/if}
{if $methods[methods].ifunction_call.params[params].default != ''}[{/if}{$methods[methods].ifunction_call.params[params].type}
{$methods[methods].ifunction_call.params[params].name}{if $methods[methods].ifunction_call.params[params].default != ''} = {$methods[methods].ifunction_call.params[params].default}]{/if}
{/section}
{/if})</code>
		</p>
	
		<p class="linenumber">[line {if $methods[methods].slink}{$methods[methods].slink}{else}{$methods[methods].line_number}{/if}]</p>
		{include file="docblock.tpl" sdesc=$methods[methods].sdesc desc=$methods[methods].desc tags=$methods[methods].tags}
		
{if $methods[methods].descmethod}
	<p>Overridden in child classes as:<br />
	{section name=dm loop=$methods[methods].descmethod}
	<dl>
	<dt>{$methods[methods].descmethod[dm].link}</dt>
		<dd>{$methods[methods].descmethod[dm].sdesc}</dd>
	</dl>
	{/section}</p>
{/if}
{if $methods[methods].method_overrides}<p>Overrides {$methods[methods].method_overrides.link} ({$methods[methods].method_overrides.sdesc|default:"parent method not documented"})</p>{/if}

	<h4>Parameters:</h4>
	<ul>
	{section name=params loop=$methods[methods].params}
		<li>
		<span class="type">{$methods[methods].params[params].datatype}</span>
		<b>{$methods[methods].params[params].var}</b> 
		- 
		{$methods[methods].params[params].data}</li>
	{/section}
	</ul>
	</div>
	<p class="top">[ <a href="#top">Top</a> ]</p>
{/if}
{/section}
