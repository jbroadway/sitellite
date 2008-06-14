<?php /* Smarty version 2.5.0, created on 2003-07-21 03:28:07
         compiled from method.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'cycle', 'method.tpl', 4, false),)); ?><a name='method_detail'></a>
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
<a name="method<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
" id="<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
"><!-- --></a>
<div class="<?php echo smarty_function_cycle(array('values' => "evenrow,oddrow"), $this) ; ?>
">

<div class="method-header">
	<span class="method-title"><?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['constructor']): ?>Constructor <?php elseif ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['destructor']): ?>Destructor <?php endif; ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
</span>&nbsp;&nbsp;<span class="smalllinenumber">[line <?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['slink']): ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['line_number']; ?>
<?php endif; ?>]</span>
</div>
<br />

	<div class="function">
    <table width="90%" border="0" cellspacing="0" cellpadding="1"><tr><td class="code-border">
    <table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><td class="code">&nbsp;
		<code><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_return']; ?>
 <?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['returnsref']): ?>&amp;<?php endif; ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['function_name']; ?>
(
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
<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?>[<?php endif; ?><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['type']; ?>

<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['name']; ?>
<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?> = <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default']; ?>
]<?php endif; ?>
<?php endfor; endif; ?>
&nbsp;
<?php endif; ?>)</code>
    </td></tr></table>
    </td></tr></table><br /></div>

	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['sdesc'],'desc' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['desc']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params']): ?>
		<strong>Parameters:</strong><br />
			<table border="0" cellspacing="0" cellpadding="0">
		<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params']) ? count($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params']) : max(0, (int)$this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params']);
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
			<tr><td class="indent">
				<span class="var-type"><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params'][$this->_sections['params']['index']]['datatype']; ?>
</span>&nbsp;&nbsp;</td>
				<td>
				<span class="var-name"><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params'][$this->_sections['params']['index']]['var']; ?>
:&nbsp;</span></td>
				<td>
				<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params'][$this->_sections['params']['index']]['data']): ?><span class="var-description"> <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['params'][$this->_sections['params']['index']]['data']; ?>
</span><?php endif; ?>
			</td></tr>
		<?php endfor; endif; ?>
		</table>

	<?php endif; ?>
<br />
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("tags.tpl", array('api_tags' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['api_tags'],'info_tags' => $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['info_tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']): ?>
		<hr class="separator" />
		<div class="notes">Redefinition of:</div>
		<dl>
			<dt><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']['link']; ?>
</dt>
			<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']['sdesc']): ?>
			<dd><?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['method_overrides']['sdesc']; ?>
</dd>
			<?php endif; ?>
		</dl>
	<?php endif; ?>

	<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod']): ?>
		<hr class="separator" />
		<div class="notes">Redefined in descendants as:</div>
		<ul class="redefinitions">
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
			<li>
				<?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod'][$this->_sections['dm']['index']]['link']; ?>

				<?php if ($this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod'][$this->_sections['dm']['index']]['sdesc']): ?>
				: <?php echo $this->_tpl_vars['methods'][$this->_sections['methods']['index']]['descmethod'][$this->_sections['dm']['index']]['sdesc']; ?>

				<?php endif; ?>
			</li>
		<?php endfor; endif; ?>
		</ul>
	<?php endif; ?>
	<br />
	<div class="top">[ <a href="#top">Top</a> ]</div>
</div>
<?php endfor; endif; ?>