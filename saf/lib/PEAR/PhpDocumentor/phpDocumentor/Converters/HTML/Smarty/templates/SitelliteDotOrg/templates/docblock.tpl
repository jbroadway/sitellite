{if $sdesc != ''}{$sdesc|default:''}<br /><br />{/if}
{if $desc != ''}{$desc|default:''}<br />{/if}
{if count($tags) > 0}
<br /><br />
<h3>Tags:</h3>
<div class="tags">
<table border="0" cellspacing="0" cellpadding="0">
{section name=tag loop=$tags}
  <tr>
    <td><b>{$tags[tag].keyword}:</b>&nbsp;&nbsp;</td><td>{$tags[tag].data}</td>
  </tr>
{/section}
</table>
</div>
{/if}