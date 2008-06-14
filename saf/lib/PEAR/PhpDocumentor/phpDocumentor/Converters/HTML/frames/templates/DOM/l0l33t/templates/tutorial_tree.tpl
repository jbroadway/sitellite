	var {$name|replace:"-":"_"}node = new WebFXTreeItem('{$main.title|strip_tags}','{$main.link}', parent_node);

{if $haskids}
  var {$name|replace:"-":"_"}_old_parent_node = parent_node;
	parent_node = {$name|replace:"-":"_"}node;
	{$kids}
	parent_node = {$name|replace:"-":"_"}_old_parent_node;
{/if}
