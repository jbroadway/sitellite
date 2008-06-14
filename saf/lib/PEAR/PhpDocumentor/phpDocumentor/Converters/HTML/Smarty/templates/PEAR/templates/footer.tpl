</div>
<div id="nav">
{if $tutorials}
	<div id="tutorials">
		Tutorials/Manuals:<br />
		{if $tutorials.pkg}
			<strong>Package-level:</strong>
			{section name=ext loop=$tutorials.pkg}
				{$tutorials.pkg[ext]}
			{/section}
		{/if}
		{if $tutorials.cls}
			<strong>Class-level:</strong>
			{section name=ext loop=$tutorials.cls}
				{$tutorials.cls[ext]}
			{/section}
		{/if}
		{if $tutorials.proc}
			<strong>Procedural-level:</strong>
			{section name=ext loop=$tutorials.proc}
				{$tutorials.proc[ext]}
			{/section}
		{/if}
	</div>
{/if}

	{if !$noleftindex}{assign var="noleftindex" value=false}{/if}
	{if !$noleftindex}
		<div id="index">
			<div id="files">
				{if $compiledfileindex}
				Files:<br>
				{eval var=$compiledfileindex}{/if}
			</div>
			<div id="classes">
				{if $compiledclassindex}Classes:<br>
				{eval var=$compiledclassindex}{/if}
			</div>
		</div>
	{/if}
</div>
<div id="footer">Documentation generated on {$date} by <a href="{$phpdocwebsite}">phpDocumentor {$phpdocversion}</a></div>
</body>
</html>
