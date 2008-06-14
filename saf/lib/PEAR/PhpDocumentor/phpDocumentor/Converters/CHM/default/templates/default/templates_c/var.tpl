<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:41
         compiled from var.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'replace', 'var.tpl', 9, false),)); ?><?php if ($this->_tpl_vars['show'] == 'summary'): ?>
<!-- =========== VAR SUMMARY =========== -->
<A NAME='var_summary'><!-- --></A>
<H3>Class Variable Summary</H3>

<UL>
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
	<!-- =========== Summary =========== -->
		<LI><CODE><a href="<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
</a></CODE> = <CODE class="varsummarydefault"><?php echo $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_default'], "\n", "<br>\n"), ' ', "&nbsp;"), "\t", "&nbsp;&nbsp;&nbsp;"); ?>
</CODE>
		<BR>
		<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
</UL>
<?php else: ?>
<!-- ============ VARIABLE DETAIL =========== -->

<A NAME='variable_detail'></A>

<H3>Variable Detail</H3>

<UL>
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
<A NAME="<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_dest']; ?>
"><!-- --></A>
<LI><SPAN class="code"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
</SPAN> = <CODE class="varsummarydefault"><?php echo $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_default'], "\n", "<br>\n"), ' ', "&nbsp;"), "\t", "&nbsp;&nbsp;&nbsp;"); ?>
</CODE> [line <span class="linenumber"><?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']): ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['line_number']; ?>
<?php endif; ?></span>]</LI>
<LI><b>Data type:</b> <CODE class="varsummarydefault"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_type']; ?>
</CODE><?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_overrides']): ?><b>Overrides:</b> <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_overrides']; ?>
<br><?php endif; ?></LI>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc'],'desc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['desc'],'tags' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<BR>
<?php endfor; endif; ?>
</UL>
<?php endif; ?>