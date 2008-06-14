<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:41
         compiled from class.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('eltype' => 'class','hasel' => true,'contents' => $this->_tpl_vars['classcontents']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- Start of Class Data -->
<H3>
	<SPAN class="type">Class</SPAN> <?php echo $this->_tpl_vars['class_name']; ?>

	<HR>
</H3>
[line <span class="linenumber"><?php if ($this->_tpl_vars['class_slink']): ?><?php echo $this->_tpl_vars['class_slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['line_number']; ?>
<?php endif; ?></span>]<br />
<pre>
<?php if (isset($this->_sections['tree'])) unset($this->_sections['tree']);
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
<?php endfor; endif; ?>
</pre>
<?php if ($this->_tpl_vars['tutorial']): ?>
<div class="maintutorial">Class Tutorial: <?php echo $this->_tpl_vars['tutorial']; ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['children']): ?>
<SPAN class="type">Classes extended from <?php echo $this->_tpl_vars['class_name']; ?>
:</SPAN>
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
	<dl>
	<dt><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['link']; ?>
</dt>
		<dd><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['sdesc']; ?>
</dd>
	</dl>
	<?php endfor; endif; ?></p>
<?php endif; ?>
<?php if ($this->_tpl_vars['conflicts']['conflict_type']): ?><p class="warning">Conflicts with classes:<br />
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
<p>
<?php endif; ?>
<SPAN class="type">Location:</SPAN> <?php echo $this->_tpl_vars['source_location']; ?>

<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('type' => 'class','sdesc' => $this->_tpl_vars['sdesc'],'desc' => $this->_tpl_vars['desc']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("var.tpl", array('show' => 'summary'));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<!-- =========== INHERITED VAR SUMMARY =========== -->
<A NAME='inheritedvar_summary'><!-- --></A>
<H3>Inherited Class Variable Summary</H3>

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
<H4>Inherited From Class <?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['parent_class']; ?>
</H4>
<UL>
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
	<!-- =========== Summary =========== -->
		<LI><CODE><?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['link']; ?>
</CODE> = <CODE class="varsummarydefault"><?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['default']; ?>
</CODE>
		<BR>
		<?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
	</LI>
</UL>
<?php endfor; endif; ?>

<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("method.tpl", array('show' => 'summary'));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- =========== INHERITED METHOD SUMMARY =========== -->
<A NAME='methods_inherited'><!-- --></A>
<H3>Inherited Method Summary</H3> 

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
<H4>Inherited From Class <?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['parent_class']; ?>
</h4>
<UL>
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
	<!-- =========== Summary =========== -->
		<LI><CODE><?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['link']; ?>
</CODE><br>
		<?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
</UL>
<?php endfor; endif; ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("method.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("var.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<hr>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>