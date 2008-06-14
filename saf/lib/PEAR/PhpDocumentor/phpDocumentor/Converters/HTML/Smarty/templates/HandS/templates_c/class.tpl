<?php /* Smarty version 2.5.0, created on 2003-06-02 22:34:31
         compiled from class.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('eltype' => 'class','hasel' => true,'contents' => $this->_tpl_vars['classcontents']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<h2 class="class-name">Class <?php echo $this->_tpl_vars['class_name']; ?>
</h2>

<a name="sec-description"></a>
<div class="info-box">
	<div class="info-box-title">Class Overview</div>
	<div class="nav-bar">
		<?php if ($this->_tpl_vars['children'] || $this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
			<span class="disabled">Class Overview</span> |
		<?php endif; ?>
		<?php if ($this->_tpl_vars['children']): ?>
			<a href="#sec-descendents">Descendents</a>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
			<a href="#sec-inherited">Inherited Properties and Methods</a>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
			<?php if ($this->_tpl_vars['vars']): ?>
				<a href="#sec-var-summary">Propertys Summary</a> | <a href="#sec-vars">Properties Detail</a>
			<?php else: ?>
				<a href="#sec-vars">Properties</a>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
			<?php if ($this->_tpl_vars['methods']): ?>
				<a href="#sec-method-summary">Method Summary</a> | <a href="#sec-methods">Methods Detail</a>
			<?php else: ?>
				<a href="#sec-methods">Methods</a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<div class="info-box-body">
		<table width="100%" border="0">
		<tr><td valign="top" width="60%" class="class-overview">

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('type' => 'class','sdesc' => $this->_tpl_vars['sdesc'],'desc' => $this->_tpl_vars['desc']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

		<p class="notes">
			Located in <a class="field" href="<?php echo $this->_tpl_vars['page_link']; ?>
"><?php echo $this->_tpl_vars['source_location']; ?>
</a> [<span class="field">line <?php if ($this->_tpl_vars['class_slink']): ?><?php echo $this->_tpl_vars['class_slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['line_number']; ?>
<?php endif; ?></span>]
		</p>

		<?php if ($this->_tpl_vars['tutorial']): ?>
			<hr class="separator" />
			<div class="notes">Tutorial: <span class="tutorial"><?php echo $this->_tpl_vars['tutorial']; ?>
</div>
		<?php endif; ?>

		<pre><?php if (isset($this->_sections['tree'])) unset($this->_sections['tree']);
$this->_sections['tree']['name'] = 'tree';
$this->_sections['tree']['loop'] = is_array($this->_tpl_vars['class_tree']['classes']) ? count($this->_tpl_vars['class_tree']['classes']) : max(0, (int)$this->_tpl_vars['class_tree']['classes']);
$this->_sections['tree']['show'] = true;
$this->_sections['tree']['max'] = $this->_sections['tree']['loop'];
$this->_sections['tree']['step'] = 1;
$this->_sections['tree']['start'] = $this->_sections['tree']['step'] > 0 ? 0 : $this->_sections['tree']['loop']-1;
if ($this->_sections['tree']['show']) {
    $this->_sections['tree']['total'] = $this->_sections['tree']['loop'];
    if ($this->_sections['tree']['total'] == 0)
        $this->_sections['tree']['show'] = false;
} else
    $this->_sections['tree']['total'] = 0;
if ($this->_sections['tree']['show']):

            for ($this->_sections['tree']['index'] = $this->_sections['tree']['start'], $this->_sections['tree']['iteration'] = 1;
                 $this->_sections['tree']['iteration'] <= $this->_sections['tree']['total'];
                 $this->_sections['tree']['index'] += $this->_sections['tree']['step'], $this->_sections['tree']['iteration']++):
$this->_sections['tree']['rownum'] = $this->_sections['tree']['iteration'];
$this->_sections['tree']['index_prev'] = $this->_sections['tree']['index'] - $this->_sections['tree']['step'];
$this->_sections['tree']['index_next'] = $this->_sections['tree']['index'] + $this->_sections['tree']['step'];
$this->_sections['tree']['first']      = ($this->_sections['tree']['iteration'] == 1);
$this->_sections['tree']['last']       = ($this->_sections['tree']['iteration'] == $this->_sections['tree']['total']);
?><?php echo $this->_tpl_vars['class_tree']['classes'][$this->_sections['tree']['index']]; ?>
<?php echo $this->_tpl_vars['class_tree']['distance'][$this->_sections['tree']['index']]; ?>
<?php endfor; endif; ?></pre>

		<?php if ($this->_tpl_vars['conflicts']['conflict_type']): ?>
			<hr class="separator" />
			<div><span class="warning">Conflicts with classes:</span><br />
			<?php if (isset($this->_sections['me'])) unset($this->_sections['me']);
$this->_sections['me']['name'] = 'me';
$this->_sections['me']['loop'] = is_array($this->_tpl_vars['conflicts']['conflicts']) ? count($this->_tpl_vars['conflicts']['conflicts']) : max(0, (int)$this->_tpl_vars['conflicts']['conflicts']);
$this->_sections['me']['show'] = true;
$this->_sections['me']['max'] = $this->_sections['me']['loop'];
$this->_sections['me']['step'] = 1;
$this->_sections['me']['start'] = $this->_sections['me']['step'] > 0 ? 0 : $this->_sections['me']['loop']-1;
if ($this->_sections['me']['show']) {
    $this->_sections['me']['total'] = $this->_sections['me']['loop'];
    if ($this->_sections['me']['total'] == 0)
        $this->_sections['me']['show'] = false;
} else
    $this->_sections['me']['total'] = 0;
if ($this->_sections['me']['show']):

            for ($this->_sections['me']['index'] = $this->_sections['me']['start'], $this->_sections['me']['iteration'] = 1;
                 $this->_sections['me']['iteration'] <= $this->_sections['me']['total'];
                 $this->_sections['me']['index'] += $this->_sections['me']['step'], $this->_sections['me']['iteration']++):
$this->_sections['me']['rownum'] = $this->_sections['me']['iteration'];
$this->_sections['me']['index_prev'] = $this->_sections['me']['index'] - $this->_sections['me']['step'];
$this->_sections['me']['index_next'] = $this->_sections['me']['index'] + $this->_sections['me']['step'];
$this->_sections['me']['first']      = ($this->_sections['me']['iteration'] == 1);
$this->_sections['me']['last']       = ($this->_sections['me']['iteration'] == $this->_sections['me']['total']);
?>
				<?php echo $this->_tpl_vars['conflicts']['conflicts'][$this->_sections['me']['index']]; ?>
<br />
			<?php endfor; endif; ?>
			</div>
		<?php endif; ?>

		<?php if (count ( $this->_tpl_vars['tags'] ) > 0): ?>
		<strong>Author(s):</strong>
		<ul>
		  <?php if (isset($this->_sections['tag'])) unset($this->_sections['tag']);
$this->_sections['tag']['name'] = 'tag';
$this->_sections['tag']['loop'] = is_array($this->_tpl_vars['tags']) ? count($this->_tpl_vars['tags']) : max(0, (int)$this->_tpl_vars['tags']);
$this->_sections['tag']['show'] = true;
$this->_sections['tag']['max'] = $this->_sections['tag']['loop'];
$this->_sections['tag']['step'] = 1;
$this->_sections['tag']['start'] = $this->_sections['tag']['step'] > 0 ? 0 : $this->_sections['tag']['loop']-1;
if ($this->_sections['tag']['show']) {
    $this->_sections['tag']['total'] = $this->_sections['tag']['loop'];
    if ($this->_sections['tag']['total'] == 0)
        $this->_sections['tag']['show'] = false;
} else
    $this->_sections['tag']['total'] = 0;
if ($this->_sections['tag']['show']):

            for ($this->_sections['tag']['index'] = $this->_sections['tag']['start'], $this->_sections['tag']['iteration'] = 1;
                 $this->_sections['tag']['iteration'] <= $this->_sections['tag']['total'];
                 $this->_sections['tag']['index'] += $this->_sections['tag']['step'], $this->_sections['tag']['iteration']++):
$this->_sections['tag']['rownum'] = $this->_sections['tag']['iteration'];
$this->_sections['tag']['index_prev'] = $this->_sections['tag']['index'] - $this->_sections['tag']['step'];
$this->_sections['tag']['index_next'] = $this->_sections['tag']['index'] + $this->_sections['tag']['step'];
$this->_sections['tag']['first']      = ($this->_sections['tag']['iteration'] == 1);
$this->_sections['tag']['last']       = ($this->_sections['tag']['iteration'] == $this->_sections['tag']['total']);
?>
			 <?php if ($this->_tpl_vars['tags'][$this->_sections['tag']['index']]['keyword'] == 'author'): ?>
			 <li><?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['data']; ?>
</li>
			 <?php endif; ?>
		  <?php endfor; endif; ?>
		</ul>
		<?php endif; ?>

		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("classtags.tpl", array('tags' => $this->_tpl_vars['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</td>

		<?php if (count ( $this->_tpl_vars['contents']['var'] ) > 0): ?>
		<td valign="top" width="20%" class="class-overview">
		<p align="center" class="short-description"><strong><a href="#sec_vars">Properties</a></strong></p>
		<ul>
		  <?php if (isset($this->_sections['contents'])) unset($this->_sections['contents']);
$this->_sections['contents']['name'] = 'contents';
$this->_sections['contents']['loop'] = is_array($this->_tpl_vars['contents']['var']) ? count($this->_tpl_vars['contents']['var']) : max(0, (int)$this->_tpl_vars['contents']['var']);
$this->_sections['contents']['show'] = true;
$this->_sections['contents']['max'] = $this->_sections['contents']['loop'];
$this->_sections['contents']['step'] = 1;
$this->_sections['contents']['start'] = $this->_sections['contents']['step'] > 0 ? 0 : $this->_sections['contents']['loop']-1;
if ($this->_sections['contents']['show']) {
    $this->_sections['contents']['total'] = $this->_sections['contents']['loop'];
    if ($this->_sections['contents']['total'] == 0)
        $this->_sections['contents']['show'] = false;
} else
    $this->_sections['contents']['total'] = 0;
if ($this->_sections['contents']['show']):

            for ($this->_sections['contents']['index'] = $this->_sections['contents']['start'], $this->_sections['contents']['iteration'] = 1;
                 $this->_sections['contents']['iteration'] <= $this->_sections['contents']['total'];
                 $this->_sections['contents']['index'] += $this->_sections['contents']['step'], $this->_sections['contents']['iteration']++):
$this->_sections['contents']['rownum'] = $this->_sections['contents']['iteration'];
$this->_sections['contents']['index_prev'] = $this->_sections['contents']['index'] - $this->_sections['contents']['step'];
$this->_sections['contents']['index_next'] = $this->_sections['contents']['index'] + $this->_sections['contents']['step'];
$this->_sections['contents']['first']      = ($this->_sections['contents']['iteration'] == 1);
$this->_sections['contents']['last']       = ($this->_sections['contents']['iteration'] == $this->_sections['contents']['total']);
?>
		  <li><?php echo $this->_tpl_vars['contents']['var'][$this->_sections['contents']['index']]; ?>
</li>
		  <?php endfor; endif; ?>
		</ul>
		</td>
		<?php endif; ?>

		<?php if (count ( $this->_tpl_vars['contents']['method'] ) > 0): ?>
		<td valign="top" width="20%" class="class-overview">
		<p align="center" class="short-description"><strong><a href="#sec_methods">Methods</a></strong></p>
		<ul>
		  <?php if (isset($this->_sections['contents'])) unset($this->_sections['contents']);
$this->_sections['contents']['name'] = 'contents';
$this->_sections['contents']['loop'] = is_array($this->_tpl_vars['contents']['method']) ? count($this->_tpl_vars['contents']['method']) : max(0, (int)$this->_tpl_vars['contents']['method']);
$this->_sections['contents']['show'] = true;
$this->_sections['contents']['max'] = $this->_sections['contents']['loop'];
$this->_sections['contents']['step'] = 1;
$this->_sections['contents']['start'] = $this->_sections['contents']['step'] > 0 ? 0 : $this->_sections['contents']['loop']-1;
if ($this->_sections['contents']['show']) {
    $this->_sections['contents']['total'] = $this->_sections['contents']['loop'];
    if ($this->_sections['contents']['total'] == 0)
        $this->_sections['contents']['show'] = false;
} else
    $this->_sections['contents']['total'] = 0;
if ($this->_sections['contents']['show']):

            for ($this->_sections['contents']['index'] = $this->_sections['contents']['start'], $this->_sections['contents']['iteration'] = 1;
                 $this->_sections['contents']['iteration'] <= $this->_sections['contents']['total'];
                 $this->_sections['contents']['index'] += $this->_sections['contents']['step'], $this->_sections['contents']['iteration']++):
$this->_sections['contents']['rownum'] = $this->_sections['contents']['iteration'];
$this->_sections['contents']['index_prev'] = $this->_sections['contents']['index'] - $this->_sections['contents']['step'];
$this->_sections['contents']['index_next'] = $this->_sections['contents']['index'] + $this->_sections['contents']['step'];
$this->_sections['contents']['first']      = ($this->_sections['contents']['iteration'] == 1);
$this->_sections['contents']['last']       = ($this->_sections['contents']['iteration'] == $this->_sections['contents']['total']);
?>
		  <li><?php echo $this->_tpl_vars['contents']['method'][$this->_sections['contents']['index']]; ?>
</li>
		  <?php endfor; endif; ?>
		</ul>
		</td>
		<?php endif; ?>

		</tr></table>
		<div class="top">[ <a href="#top">Top</a> ]</div>
	</div>
</div>

<?php if ($this->_tpl_vars['children']): ?>
	<a name="sec-descendents"></a>
	<div class="info-box">
		<div class="info-box-title">Direct descendents</div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
				<a href="#sec-inherited">Inherited Properties and Methods</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
		<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
			<?php if ($this->_tpl_vars['vars']): ?>
				<a href="#sec-var-summary">Propertys Summary</a> | <a href="#sec-vars">Properties Detail</a>
			<?php else: ?>
				<a href="#sec-vars">Properties</a>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
			<?php if ($this->_tpl_vars['methods']): ?>
				<a href="#sec-method-summary">Method Summary</a> | <a href="#sec-methods">Methods Detail</a>
			<?php else: ?>
				<a href="#sec-methods">Methods</a>
			<?php endif; ?>
		<?php endif; ?>
		</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				<tr>
					<th class="class-table-header">Child Class</th>
					<th class="class-table-header">Description</th>
				</tr>
				<?php if (isset($this->_sections['kids'])) unset($this->_sections['kids']);
$this->_sections['kids']['name'] = 'kids';
$this->_sections['kids']['loop'] = is_array($this->_tpl_vars['children']) ? count($this->_tpl_vars['children']) : max(0, (int)$this->_tpl_vars['children']);
$this->_sections['kids']['show'] = true;
$this->_sections['kids']['max'] = $this->_sections['kids']['loop'];
$this->_sections['kids']['step'] = 1;
$this->_sections['kids']['start'] = $this->_sections['kids']['step'] > 0 ? 0 : $this->_sections['kids']['loop']-1;
if ($this->_sections['kids']['show']) {
    $this->_sections['kids']['total'] = $this->_sections['kids']['loop'];
    if ($this->_sections['kids']['total'] == 0)
        $this->_sections['kids']['show'] = false;
} else
    $this->_sections['kids']['total'] = 0;
if ($this->_sections['kids']['show']):

            for ($this->_sections['kids']['index'] = $this->_sections['kids']['start'], $this->_sections['kids']['iteration'] = 1;
                 $this->_sections['kids']['iteration'] <= $this->_sections['kids']['total'];
                 $this->_sections['kids']['index'] += $this->_sections['kids']['step'], $this->_sections['kids']['iteration']++):
$this->_sections['kids']['rownum'] = $this->_sections['kids']['iteration'];
$this->_sections['kids']['index_prev'] = $this->_sections['kids']['index'] - $this->_sections['kids']['step'];
$this->_sections['kids']['index_next'] = $this->_sections['kids']['index'] + $this->_sections['kids']['step'];
$this->_sections['kids']['first']      = ($this->_sections['kids']['iteration'] == 1);
$this->_sections['kids']['last']       = ($this->_sections['kids']['iteration'] == $this->_sections['kids']['total']);
?>
				<tr>
					<td style="padding-right: 2em"><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['link']; ?>
</td>
					<td>
					<?php if ($this->_tpl_vars['children'][$this->_sections['kids']['index']]['sdesc']): ?>
						<?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['sdesc']; ?>

					<?php else: ?>
						<?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['desc']; ?>

					<?php endif; ?>
					</td>
				</tr>
				<?php endfor; endif; ?>
			</table>
			<br /><div class="top">[ <a href="#top">Top</a> ]</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
	<a name="sec-inherited"></a>
	<div class="info-box">
		<div class="info-box-title">Inherited Properties and Methods</div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a> |
			<?php endif; ?>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<span class="disabled">Inherited Properties and Methods</span>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
				<?php if ($this->_tpl_vars['vars']): ?>
					<a href="#sec-var-summary">Propertys Summary</a> | <a href="#sec-vars">Properties Detail</a>
				<?php else: ?>
					<a href="#sec-vars">Properties</a>
				<?php endif; ?>
				<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
				<?php if ($this->_tpl_vars['methods']): ?>
					<a href="#sec-method-summary">Method Summary</a> | <a href="#sec-methods">Methods Detail</a>
				<?php else: ?>
					<a href="#sec-methods">Methods</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="info-box-body">
			<table cellpadding="2" cellspacing="0" class="class-table">
				<tr>
					<th class="class-table-header" width="30%">Inherited Properties</th>
					<th class="class-table-header" width="70%">Inherited Methods</th>
				</tr>
				<tr>
					<td width="30%">
						<?php if (isset($this->_sections['ivars'])) unset($this->_sections['ivars']);
$this->_sections['ivars']['name'] = 'ivars';
$this->_sections['ivars']['loop'] = is_array($this->_tpl_vars['ivars']) ? count($this->_tpl_vars['ivars']) : max(0, (int)$this->_tpl_vars['ivars']);
$this->_sections['ivars']['show'] = true;
$this->_sections['ivars']['max'] = $this->_sections['ivars']['loop'];
$this->_sections['ivars']['step'] = 1;
$this->_sections['ivars']['start'] = $this->_sections['ivars']['step'] > 0 ? 0 : $this->_sections['ivars']['loop']-1;
if ($this->_sections['ivars']['show']) {
    $this->_sections['ivars']['total'] = $this->_sections['ivars']['loop'];
    if ($this->_sections['ivars']['total'] == 0)
        $this->_sections['ivars']['show'] = false;
} else
    $this->_sections['ivars']['total'] = 0;
if ($this->_sections['ivars']['show']):

            for ($this->_sections['ivars']['index'] = $this->_sections['ivars']['start'], $this->_sections['ivars']['iteration'] = 1;
                 $this->_sections['ivars']['iteration'] <= $this->_sections['ivars']['total'];
                 $this->_sections['ivars']['index'] += $this->_sections['ivars']['step'], $this->_sections['ivars']['iteration']++):
$this->_sections['ivars']['rownum'] = $this->_sections['ivars']['iteration'];
$this->_sections['ivars']['index_prev'] = $this->_sections['ivars']['index'] - $this->_sections['ivars']['step'];
$this->_sections['ivars']['index_next'] = $this->_sections['ivars']['index'] + $this->_sections['ivars']['step'];
$this->_sections['ivars']['first']      = ($this->_sections['ivars']['iteration'] == 1);
$this->_sections['ivars']['last']       = ($this->_sections['ivars']['iteration'] == $this->_sections['ivars']['total']);
?>
							<p>Inherited From <span class="classname"><?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['parent_class']; ?>
</span></p>
							<blockquote>
								<dl>
									<?php if (isset($this->_sections['ivars2'])) unset($this->_sections['ivars2']);
$this->_sections['ivars2']['name'] = 'ivars2';
$this->_sections['ivars2']['loop'] = is_array($this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']) ? count($this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']) : max(0, (int)$this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']);
$this->_sections['ivars2']['show'] = true;
$this->_sections['ivars2']['max'] = $this->_sections['ivars2']['loop'];
$this->_sections['ivars2']['step'] = 1;
$this->_sections['ivars2']['start'] = $this->_sections['ivars2']['step'] > 0 ? 0 : $this->_sections['ivars2']['loop']-1;
if ($this->_sections['ivars2']['show']) {
    $this->_sections['ivars2']['total'] = $this->_sections['ivars2']['loop'];
    if ($this->_sections['ivars2']['total'] == 0)
        $this->_sections['ivars2']['show'] = false;
} else
    $this->_sections['ivars2']['total'] = 0;
if ($this->_sections['ivars2']['show']):

            for ($this->_sections['ivars2']['index'] = $this->_sections['ivars2']['start'], $this->_sections['ivars2']['iteration'] = 1;
                 $this->_sections['ivars2']['iteration'] <= $this->_sections['ivars2']['total'];
                 $this->_sections['ivars2']['index'] += $this->_sections['ivars2']['step'], $this->_sections['ivars2']['iteration']++):
$this->_sections['ivars2']['rownum'] = $this->_sections['ivars2']['iteration'];
$this->_sections['ivars2']['index_prev'] = $this->_sections['ivars2']['index'] - $this->_sections['ivars2']['step'];
$this->_sections['ivars2']['index_next'] = $this->_sections['ivars2']['index'] + $this->_sections['ivars2']['step'];
$this->_sections['ivars2']['first']      = ($this->_sections['ivars2']['iteration'] == 1);
$this->_sections['ivars2']['last']       = ($this->_sections['ivars2']['iteration'] == $this->_sections['ivars2']['total']);
?>
										<dt>
											<span class="method-definition"><?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['link']; ?>
</span>
										</dt>
										<dd>
											<span class="method-definition"><?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['ivars_sdesc']; ?>
</span>
										</dd>
									<?php endfor; endif; ?>
								</dl>
							</blockquote>
						<?php endfor; endif; ?>
					</td>
					<td width="70%">
						<?php if (isset($this->_sections['imethods'])) unset($this->_sections['imethods']);
$this->_sections['imethods']['name'] = 'imethods';
$this->_sections['imethods']['loop'] = is_array($this->_tpl_vars['imethods']) ? count($this->_tpl_vars['imethods']) : max(0, (int)$this->_tpl_vars['imethods']);
$this->_sections['imethods']['show'] = true;
$this->_sections['imethods']['max'] = $this->_sections['imethods']['loop'];
$this->_sections['imethods']['step'] = 1;
$this->_sections['imethods']['start'] = $this->_sections['imethods']['step'] > 0 ? 0 : $this->_sections['imethods']['loop']-1;
if ($this->_sections['imethods']['show']) {
    $this->_sections['imethods']['total'] = $this->_sections['imethods']['loop'];
    if ($this->_sections['imethods']['total'] == 0)
        $this->_sections['imethods']['show'] = false;
} else
    $this->_sections['imethods']['total'] = 0;
if ($this->_sections['imethods']['show']):

            for ($this->_sections['imethods']['index'] = $this->_sections['imethods']['start'], $this->_sections['imethods']['iteration'] = 1;
                 $this->_sections['imethods']['iteration'] <= $this->_sections['imethods']['total'];
                 $this->_sections['imethods']['index'] += $this->_sections['imethods']['step'], $this->_sections['imethods']['iteration']++):
$this->_sections['imethods']['rownum'] = $this->_sections['imethods']['iteration'];
$this->_sections['imethods']['index_prev'] = $this->_sections['imethods']['index'] - $this->_sections['imethods']['step'];
$this->_sections['imethods']['index_next'] = $this->_sections['imethods']['index'] + $this->_sections['imethods']['step'];
$this->_sections['imethods']['first']      = ($this->_sections['imethods']['iteration'] == 1);
$this->_sections['imethods']['last']       = ($this->_sections['imethods']['iteration'] == $this->_sections['imethods']['total']);
?>
							<p>Inherited From <span class="classname"><?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['parent_class']; ?>
</span></p>
							<blockquote>
								<dl>
									<?php if (isset($this->_sections['im2'])) unset($this->_sections['im2']);
$this->_sections['im2']['name'] = 'im2';
$this->_sections['im2']['loop'] = is_array($this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']) ? count($this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']) : max(0, (int)$this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']);
$this->_sections['im2']['show'] = true;
$this->_sections['im2']['max'] = $this->_sections['im2']['loop'];
$this->_sections['im2']['step'] = 1;
$this->_sections['im2']['start'] = $this->_sections['im2']['step'] > 0 ? 0 : $this->_sections['im2']['loop']-1;
if ($this->_sections['im2']['show']) {
    $this->_sections['im2']['total'] = $this->_sections['im2']['loop'];
    if ($this->_sections['im2']['total'] == 0)
        $this->_sections['im2']['show'] = false;
} else
    $this->_sections['im2']['total'] = 0;
if ($this->_sections['im2']['show']):

            for ($this->_sections['im2']['index'] = $this->_sections['im2']['start'], $this->_sections['im2']['iteration'] = 1;
                 $this->_sections['im2']['iteration'] <= $this->_sections['im2']['total'];
                 $this->_sections['im2']['index'] += $this->_sections['im2']['step'], $this->_sections['im2']['iteration']++):
$this->_sections['im2']['rownum'] = $this->_sections['im2']['iteration'];
$this->_sections['im2']['index_prev'] = $this->_sections['im2']['index'] - $this->_sections['im2']['step'];
$this->_sections['im2']['index_next'] = $this->_sections['im2']['index'] + $this->_sections['im2']['step'];
$this->_sections['im2']['first']      = ($this->_sections['im2']['iteration'] == 1);
$this->_sections['im2']['last']       = ($this->_sections['im2']['iteration'] == $this->_sections['im2']['total']);
?>
										<dt>
											<span class="method-definition"><?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['link']; ?>
</span>
										</dt>
										<dd>
											<span class="method-definition"><?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['sdesc']; ?>
</span>
										</dd>
									<?php endfor; endif; ?>
								</dl>
							</blockquote>
						<?php endfor; endif; ?>
					</td>
				</tr>
			</table>
			<br /><div class="top">[ <a href="#top">Top</a> ]</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['vars']): ?>
	<a name="sec-var-summary"></a>
	<div class="info-box">
		<div class="info-box-title">Property Summary</span></div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a> |
			<?php endif; ?>
			<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
				<a href="#sec-inherited">Inherited Properties and Methods</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<span class="disabled">Property Summary</span> | <a href="#sec-vars">Properties Detail</a>
			<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
				|
				<?php if ($this->_tpl_vars['methods']): ?>
					<a href="#sec-method-summary">Method Summary</a> | <a href="#sec-methods">Methods Detail</a>
				<?php else: ?>
					<a href="#sec-methods">Methods</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="info-box-body">
			<div class="var-summary">
			<table border="0" cellspacing="0" cellpadding="0" class="var-summary">
			<?php if (isset($this->_sections['vars'])) unset($this->_sections['vars']);
$this->_sections['vars']['name'] = 'vars';
$this->_sections['vars']['loop'] = is_array($this->_tpl_vars['vars']) ? count($this->_tpl_vars['vars']) : max(0, (int)$this->_tpl_vars['vars']);
$this->_sections['vars']['show'] = true;
$this->_sections['vars']['max'] = $this->_sections['vars']['loop'];
$this->_sections['vars']['step'] = 1;
$this->_sections['vars']['start'] = $this->_sections['vars']['step'] > 0 ? 0 : $this->_sections['vars']['loop']-1;
if ($this->_sections['vars']['show']) {
    $this->_sections['vars']['total'] = $this->_sections['vars']['loop'];
    if ($this->_sections['vars']['total'] == 0)
        $this->_sections['vars']['show'] = false;
} else
    $this->_sections['vars']['total'] = 0;
if ($this->_sections['vars']['show']):

            for ($this->_sections['vars']['index'] = $this->_sections['vars']['start'], $this->_sections['vars']['iteration'] = 1;
                 $this->_sections['vars']['iteration'] <= $this->_sections['vars']['total'];
                 $this->_sections['vars']['index'] += $this->_sections['vars']['step'], $this->_sections['vars']['iteration']++):
$this->_sections['vars']['rownum'] = $this->_sections['vars']['iteration'];
$this->_sections['vars']['index_prev'] = $this->_sections['vars']['index'] - $this->_sections['vars']['step'];
$this->_sections['vars']['index_next'] = $this->_sections['vars']['index'] + $this->_sections['vars']['step'];
$this->_sections['vars']['first']      = ($this->_sections['vars']['iteration'] == 1);
$this->_sections['vars']['last']       = ($this->_sections['vars']['iteration'] == $this->_sections['vars']['total']);
?>
				<div class="var-title">
					<tr><td class="var-title"><span class="var-type-summary"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_type']; ?>
</span>&nbsp;&nbsp;</td>
					<td class="var-title"><a href="#<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
" title="details" class="var-name-summary"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
</a>&nbsp;&nbsp;</td>
					<td class="var-summary-description"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc']; ?>
</td></tr>
				</div>
				<?php endfor; endif; ?>
				</table>
			</div>
			<br /><div class="top">[ <a href="#top">Top</a> ]</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['methods']): ?>
	<a name="sec-method-summary"></a>
	<div class="info-box">
		<div class="info-box-title">Method Summary</span></div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a> |
			<?php endif; ?>
			<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
				<a href="#sec-inherited">Inherited Properties and Methods</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
				<?php if ($this->_tpl_vars['vars']): ?>
					<a href="#sec-var-summary">Property Summary</a> | <a href="#sec-vars">Properties Detail</a>
				<?php else: ?>
					<a href="#sec-vars">Properties</a>
				<?php endif; ?>
				|
			<?php endif; ?>
			<span class="disabled">Method Summary</span> | <a href="#sec-methods">Methods Detail</a>
		</div>
		<div class="info-box-body">
			<div class="method-summary">
				<table border="0" cellspacing="0" cellpadding="0" class="method-summary">
				<?php if (isset($this->_sections['methods'])) unset($this->_sections['methods']);
$this->_sections['methods']['name'] = 'methods';
$this->_sections['methods']['loop'] = is_array($this->_tpl_vars['methods']) ? count($this->_tpl_vars['methods']) : max(0, (int)$this->_tpl_vars['methods']);
$this->_sections['methods']['show'] = true;
$this->_sections['methods']['max'] = $this->_sections['methods']['loop'];
$this->_sections['methods']['step'] = 1;
$this->_sections['methods']['start'] = $this->_sections['methods']['step'] > 0 ? 0 : $this->_sections['methods']['loop']-1;
if ($this->_sections['methods']['show']) {
    $this->_sections['methods']['total'] = $this->_sections['methods']['loop'];
    if ($this->_sections['methods']['total'] == 0)
        $this->_sections['methods']['show'] = false;
} else
    $this->_sections['methods']['total'] = 0;
if ($this->_sections['methods']['show']):

            for ($this->_sections['methods']['index'] = $this->_sections['methods']['start'], $this->_sections['methods']['iteration'] = 1;
                 $this->_sections['methods']['iteration'] <= $this->_sections['methods']['total'];
                 $this->_sections['methods']['index'] += $this->_sections['methods']['step'], $this->_sections['methods']['iteration']++):
$this->_sections['methods']['rownum'] = $this->_sections['methods']['iteration'];
$this->_sections['methods']['index_prev'] = $this->_sections['methods']['index'] - $this->_sections['methods']['step'];
$this->_sections['methods']['index_next'] = $this->_sections['methods']['index'] + $this->_sections['methods']['step'];
$this->_sections['methods']['first']      = ($this->_sections['methods']['iteration'] == 1);
$this->_sections['methods']['last']       = ($this->_sections['methods']['iteration'] == $this->_sections['methods']['total']);
?>
				<div class="method-definition">
					<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_return']): ?>
						<tr><td class="method-definition"><span class="method-result"><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_return']; ?>
</span>&nbsp;&nbsp;</td>
					<?php endif; ?>
					<td class="method-definition"><a href="#<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
" title="details" class="method-name"><?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['returnsref']): ?>&amp;<?php endif; ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
</a>()&nbsp;&nbsp;</td>
					<td class="method-definition"><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['sdesc']; ?>
</td></tr>
				</div>
				<?php endfor; endif; ?>
				</table>
			</div>
			<br /><div class="top">[ <a href="#top">Top</a> ]</div>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
	<a name="sec-vars"></a>
	<div class="info-box">
		<div class="info-box-title">Properties</div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a> |
			<?php endif; ?>
			<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
				<a href="#sec-inherited">Inherited Properties and Methods</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['methods']): ?>
				<a href="#sec-var-summary">Property Summary</a> | <a href="#sec-vars">Properties Detail</a>
			<?php else: ?>
				<span class="disabled">Properties</span>
			<?php endif; ?>

			<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
				|
				<?php if ($this->_tpl_vars['methods']): ?>
					<a href="#sec-method-summary">Method Summary</a> | <a href="#sec-methods">Methods Detail</a>
				<?php else: ?>
					<a href="#sec-methods">Methods</a>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<div class="info-box-body">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("var.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>
	<a name="sec-methods"></a>
	<div class="info-box">
		<div class="info-box-title">Methods</div>
		<div class="nav-bar">
			<a href="#sec-description">Class Overview</a> |
			<?php if ($this->_tpl_vars['children']): ?>
				<a href="#sec-descendents">Descendents</a> |
			<?php endif; ?>
			<?php if ($this->_tpl_vars['ivars'] || $this->_tpl_vars['imethods']): ?>
				<a href="#sec-inherited">Inherited Properties and Methods</a>
				<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars'] || $this->_tpl_vars['methods'] || $this->_tpl_vars['imethods']): ?>|<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['vars'] || $this->_tpl_vars['ivars']): ?>
				<?php if ($this->_tpl_vars['vars']): ?>
					<a href="#sec-var-summary">Property Summary</a> | <a href="#sec-vars">Properties Detail</a>
				<?php else: ?>
					<a href="#sec-vars">Properties</a>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->_tpl_vars['methods']): ?>
				<a href="#sec-method-summary">Method Summary</a> | <span class="disabled">Methods Detail</span>
			<?php else: ?>
				<span class="disabled">Methods</span>
			<?php endif; ?>
		</div>
		<div class="info-box-body">
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("method.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		</div>
	</div>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>