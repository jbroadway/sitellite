{include file="header.tpl" noleftindex=true}
<h1>{$title}</h1>
{section name=classtrees loop=$classtrees}
<hr class="separator" />
<div class="classtree">Root class {$classtrees[classtrees].class}</div>
{$classtrees[classtrees].class_tree}
{/section}
{include file="footer.tpl"}
