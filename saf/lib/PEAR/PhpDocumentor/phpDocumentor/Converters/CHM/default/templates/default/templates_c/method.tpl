<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:41
         compiled from method.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'method.tpl', 47, false),)); ?><?php if ($this->_tpl_vars['show'] == 'summary'): ?>
<!-- =========== METHOD SUMMARY =========== -->
<A NAME='method_summary'><!-- --></A>
<H3>Method Summary</H3> 

<UL>
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
	<!-- =========== Summary =========== -->
		<LI><CODE><A HREF='<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['id']; ?>
'><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_return']; ?>
 <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
()</A></CODE>
		<BR><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
</UL>

<?php else: ?>
<!-- ============ METHOD DETAIL =========== -->

<A NAME='method_detail'></A>
<H3>Method Detail</H3>

<UL>
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
<A NAME='<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_dest']; ?>
'><!-- --></A>

<h1><A name="<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
"></A><?php echo $this->_tpl_vars['class_name']; ?>
::<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
</h1>

<p class=method>
<b><?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['returnsref']): ?>&amp;<?php endif; ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
(</b>
<?php if (count ( $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'] )): ?>
<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params']) ? count($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params']) : max(0, (int)$this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params']);
$this->_sections['params']['show'] = true;
$this->_sections['params']['max'] = $this->_sections['params']['loop'];
$this->_sections['params']['step'] = 1;
$this->_sections['params']['start'] = $this->_sections['params']['step'] > 0 ? 0 : $this->_sections['params']['loop']-1;
if ($this->_sections['params']['show']) {
    $this->_sections['params']['total'] = $this->_sections['params']['loop'];
    if ($this->_sections['params']['total'] == 0)
        $this->_sections['params']['show'] = false;
} else
    $this->_sections['params']['total'] = 0;
if ($this->_sections['params']['show']):

            for ($this->_sections['params']['index'] = $this->_sections['params']['start'], $this->_sections['params']['iteration'] = 1;
                 $this->_sections['params']['iteration'] <= $this->_sections['params']['total'];
                 $this->_sections['params']['index'] += $this->_sections['params']['step'], $this->_sections['params']['iteration']++):
$this->_sections['params']['rownum'] = $this->_sections['params']['iteration'];
$this->_sections['params']['index_prev'] = $this->_sections['params']['index'] - $this->_sections['params']['step'];
$this->_sections['params']['index_next'] = $this->_sections['params']['index'] + $this->_sections['params']['step'];
$this->_sections['params']['first']      = ($this->_sections['params']['iteration'] == 1);
$this->_sections['params']['last']       = ($this->_sections['params']['iteration'] == $this->_sections['params']['total']);
?>
<?php if ($this->_sections['params']['iteration'] != 1): ?>, <?php endif; ?>
<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?>[<?php endif; ?><b><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['type']; ?>
</b>
<i><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['name']; ?>
</i><?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?> = <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default']; ?>
]<?php endif; ?>
<?php endfor; endif; ?>
<?php endif; ?><b> );</b>
</p>

<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod']): ?>
	<p>Overridden in child classes as:<br />
	<?php if (isset($this->_sections['dm'])) unset($this->_sections['dm']);
$this->_sections['dm']['name'] = 'dm';
$this->_sections['dm']['loop'] = is_array($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod']) ? count($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod']) : max(0, (int)$this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod']);
$this->_sections['dm']['show'] = true;
$this->_sections['dm']['max'] = $this->_sections['dm']['loop'];
$this->_sections['dm']['step'] = 1;
$this->_sections['dm']['start'] = $this->_sections['dm']['step'] > 0 ? 0 : $this->_sections['dm']['loop']-1;
if ($this->_sections['dm']['show']) {
    $this->_sections['dm']['total'] = $this->_sections['dm']['loop'];
    if ($this->_sections['dm']['total'] == 0)
        $this->_sections['dm']['show'] = false;
} else
    $this->_sections['dm']['total'] = 0;
if ($this->_sections['dm']['show']):

            for ($this->_sections['dm']['index'] = $this->_sections['dm']['start'], $this->_sections['dm']['iteration'] = 1;
                 $this->_sections['dm']['iteration'] <= $this->_sections['dm']['total'];
                 $this->_sections['dm']['index'] += $this->_sections['dm']['step'], $this->_sections['dm']['iteration']++):
$this->_sections['dm']['rownum'] = $this->_sections['dm']['iteration'];
$this->_sections['dm']['index_prev'] = $this->_sections['dm']['index'] - $this->_sections['dm']['step'];
$this->_sections['dm']['index_next'] = $this->_sections['dm']['index'] + $this->_sections['dm']['step'];
$this->_sections['dm']['first']      = ($this->_sections['dm']['iteration'] == 1);
$this->_sections['dm']['last']       = ($this->_sections['dm']['iteration'] == $this->_sections['dm']['total']);
?>
	<dl>
	<dt><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod'][$this->_sections['dm']['index']]['link']; ?>
</dt>
		<dd><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod'][$this->_sections['dm']['index']]['sdesc']; ?>
</dd>
	</dl>
	<?php endfor; endif; ?></p>
<?php endif; ?>
<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']): ?>
<p>Overrides <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']['link']; ?>
 (<?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']['sdesc'], 'parent method not documented'); ?>
)</p>
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['sdesc'],'desc' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['desc'],'tags' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['tags'],'params' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params'],'function' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<p class="top">[ <a href="#top">Top</a> ]</p>
<BR>
<?php endfor; endif; ?>
</UL>
<?php endif; ?>