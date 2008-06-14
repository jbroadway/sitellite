<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:39
         compiled from include.tpl */ ?>
<?php if ($this->_tpl_vars['summary']): ?>
<!-- =========== INCLUDE SUMMARY =========== -->
<A NAME='include_summary'><!-- --></A>
<H3>Include Statements Summary</H3>

<UL>
	<?php if (isset($this->_sections['includes'])) unset($this->_sections['includes']);
$this->_sections['includes']['name'] = 'includes';
$this->_sections['includes']['loop'] = is_array($this->_tpl_vars['includes']) ? count($this->_tpl_vars['includes']) : max(0, (int)$this->_tpl_vars['includes']);
$this->_sections['includes']['show'] = true;
$this->_sections['includes']['max'] = $this->_sections['includes']['loop'];
$this->_sections['includes']['step'] = 1;
$this->_sections['includes']['start'] = $this->_sections['includes']['step'] > 0 ? 0 : $this->_sections['includes']['loop']-1;
if ($this->_sections['includes']['show']) {
    $this->_sections['includes']['total'] = $this->_sections['includes']['loop'];
    if ($this->_sections['includes']['total'] == 0)
        $this->_sections['includes']['show'] = false;
} else
    $this->_sections['includes']['total'] = 0;
if ($this->_sections['includes']['show']):

            for ($this->_sections['includes']['index'] = $this->_sections['includes']['start'], $this->_sections['includes']['iteration'] = 1;
                 $this->_sections['includes']['iteration'] <= $this->_sections['includes']['total'];
                 $this->_sections['includes']['index'] += $this->_sections['includes']['step'], $this->_sections['includes']['iteration']++):
$this->_sections['includes']['rownum'] = $this->_sections['includes']['iteration'];
$this->_sections['includes']['index_prev'] = $this->_sections['includes']['index'] - $this->_sections['includes']['step'];
$this->_sections['includes']['index_next'] = $this->_sections['includes']['index'] + $this->_sections['includes']['step'];
$this->_sections['includes']['first']      = ($this->_sections['includes']['iteration'] == 1);
$this->_sections['includes']['last']       = ($this->_sections['includes']['iteration'] == $this->_sections['includes']['total']);
?>
		<LI><CODE><A HREF="#<?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_file']; ?>
"><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_name']; ?>
</A></CODE> = <CODE class="varsummarydefault"><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_value']; ?>
</CODE>
		<BR><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
</UL>
<?php else: ?>
<!-- ============ INCLUDE DETAIL =========== -->

<A NAME='include_detail'></A>
<H3>Include Statements Detail</H3>

<UL>
	<?php if (isset($this->_sections['includes'])) unset($this->_sections['includes']);
$this->_sections['includes']['name'] = 'includes';
$this->_sections['includes']['loop'] = is_array($this->_tpl_vars['includes']) ? count($this->_tpl_vars['includes']) : max(0, (int)$this->_tpl_vars['includes']);
$this->_sections['includes']['show'] = true;
$this->_sections['includes']['max'] = $this->_sections['includes']['loop'];
$this->_sections['includes']['step'] = 1;
$this->_sections['includes']['start'] = $this->_sections['includes']['step'] > 0 ? 0 : $this->_sections['includes']['loop']-1;
if ($this->_sections['includes']['show']) {
    $this->_sections['includes']['total'] = $this->_sections['includes']['loop'];
    if ($this->_sections['includes']['total'] == 0)
        $this->_sections['includes']['show'] = false;
} else
    $this->_sections['includes']['total'] = 0;
if ($this->_sections['includes']['show']):

            for ($this->_sections['includes']['index'] = $this->_sections['includes']['start'], $this->_sections['includes']['iteration'] = 1;
                 $this->_sections['includes']['iteration'] <= $this->_sections['includes']['total'];
                 $this->_sections['includes']['index'] += $this->_sections['includes']['step'], $this->_sections['includes']['iteration']++):
$this->_sections['includes']['rownum'] = $this->_sections['includes']['iteration'];
$this->_sections['includes']['index_prev'] = $this->_sections['includes']['index'] - $this->_sections['includes']['step'];
$this->_sections['includes']['index_next'] = $this->_sections['includes']['index'] + $this->_sections['includes']['step'];
$this->_sections['includes']['first']      = ($this->_sections['includes']['iteration'] == 1);
$this->_sections['includes']['last']       = ($this->_sections['includes']['iteration'] == $this->_sections['includes']['total']);
?>
		<A NAME="<?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_file']; ?>
"><!-- --></A>
		<LI><SPAN class="code"><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_name']; ?>
 file:</SPAN> = <CODE class="varsummarydefault"><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_value']; ?>
</CODE> [line <span class="linenumber"><?php if ($this->_tpl_vars['includes'][$this->_sections['includes']['index']]['slink']): ?><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['line_number']; ?>
<?php endif; ?></span>]<br />
		<BR><BR>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['sdesc'],'desc' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['desc'],'tags' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endfor; endif; ?>
</UL>
<?php endif; ?>