<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:39
         compiled from function.tpl */ ?>
<?php if ($this->_tpl_vars['summary']): ?>
<!-- =========== FUNCTION SUMMARY =========== -->
<A NAME='function_summary'><!-- --></A>
<H3>Function Summary</H3> 

<UL>
	<?php if (isset($this->_sections['func'])) unset($this->_sections['func']);
$this->_sections['func']['name'] = 'func';
$this->_sections['func']['loop'] = is_array($this->_tpl_vars['functions']) ? count($this->_tpl_vars['functions']) : max(0, (int)$this->_tpl_vars['functions']);
$this->_sections['func']['show'] = true;
$this->_sections['func']['max'] = $this->_sections['func']['loop'];
$this->_sections['func']['step'] = 1;
$this->_sections['func']['start'] = $this->_sections['func']['step'] > 0 ? 0 : $this->_sections['func']['loop']-1;
if ($this->_sections['func']['show']) {
    $this->_sections['func']['total'] = $this->_sections['func']['loop'];
    if ($this->_sections['func']['total'] == 0)
        $this->_sections['func']['show'] = false;
} else
    $this->_sections['func']['total'] = 0;
if ($this->_sections['func']['show']):

            for ($this->_sections['func']['index'] = $this->_sections['func']['start'], $this->_sections['func']['iteration'] = 1;
                 $this->_sections['func']['iteration'] <= $this->_sections['func']['total'];
                 $this->_sections['func']['index'] += $this->_sections['func']['step'], $this->_sections['func']['iteration']++):
$this->_sections['func']['rownum'] = $this->_sections['func']['iteration'];
$this->_sections['func']['index_prev'] = $this->_sections['func']['index'] - $this->_sections['func']['step'];
$this->_sections['func']['index_next'] = $this->_sections['func']['index'] + $this->_sections['func']['step'];
$this->_sections['func']['first']      = ($this->_sections['func']['iteration'] == 1);
$this->_sections['func']['last']       = ($this->_sections['func']['iteration'] == $this->_sections['func']['total']);
?>
	<!-- =========== Summary =========== -->
		<LI><CODE><A HREF="<?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_return']; ?>
 <?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_name']; ?>
()</A></CODE>
		<BR><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['sdesc']; ?>

	<?php endfor; endif; ?>
</UL>
<?php else: ?>
<!-- ============ FUNCTION DETAIL =========== -->

<A NAME='function_detail'></A>
<H3>Function Detail</H3>

<UL>
<?php if (isset($this->_sections['func'])) unset($this->_sections['func']);
$this->_sections['func']['name'] = 'func';
$this->_sections['func']['loop'] = is_array($this->_tpl_vars['functions']) ? count($this->_tpl_vars['functions']) : max(0, (int)$this->_tpl_vars['functions']);
$this->_sections['func']['show'] = true;
$this->_sections['func']['max'] = $this->_sections['func']['loop'];
$this->_sections['func']['step'] = 1;
$this->_sections['func']['start'] = $this->_sections['func']['step'] > 0 ? 0 : $this->_sections['func']['loop']-1;
if ($this->_sections['func']['show']) {
    $this->_sections['func']['total'] = $this->_sections['func']['loop'];
    if ($this->_sections['func']['total'] == 0)
        $this->_sections['func']['show'] = false;
} else
    $this->_sections['func']['total'] = 0;
if ($this->_sections['func']['show']):

            for ($this->_sections['func']['index'] = $this->_sections['func']['start'], $this->_sections['func']['iteration'] = 1;
                 $this->_sections['func']['iteration'] <= $this->_sections['func']['total'];
                 $this->_sections['func']['index'] += $this->_sections['func']['step'], $this->_sections['func']['iteration']++):
$this->_sections['func']['rownum'] = $this->_sections['func']['iteration'];
$this->_sections['func']['index_prev'] = $this->_sections['func']['index'] - $this->_sections['func']['step'];
$this->_sections['func']['index_next'] = $this->_sections['func']['index'] + $this->_sections['func']['step'];
$this->_sections['func']['first']      = ($this->_sections['func']['iteration'] == 1);
$this->_sections['func']['last']       = ($this->_sections['func']['iteration'] == $this->_sections['func']['total']);
?>
<A NAME="<?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_dest']; ?>
"><!-- --></A>

<LI><SPAN class="code"><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_return']; ?>
 <?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_name']; ?>
()</SPAN> [line <span class="linenumber"><?php if ($this->_tpl_vars['functions'][$this->_sections['func']['index']]['slink']): ?><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['line_number']; ?>
<?php endif; ?></span>]<br />
<BR><BR>
<SPAN class="type">Usage:</SPAN> <SPAN class="code"><?php if ($this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['returnsref']): ?>&amp;<?php endif; ?><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_name']; ?>
(
<?php if (count ( $this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'] )): ?>
<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params']) ? count($this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params']) : max(0, (int)$this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params']);
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
<?php if ($this->_sections['params']['iteration'] != 1): ?>, <?php endif; ?><?php if ($this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?>[<?php endif; ?><?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['type']; ?>
 <?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['name']; ?>
<?php if ($this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?> = <?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['ifunction_call']['params'][$this->_sections['params']['index']]['default']; ?>
]<?php endif; ?>
<?php endfor; endif; ?>
<?php endif; ?>)</SPAN>
<BR><BR>
<?php if ($this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_conflicts']['conflict_type']): ?>
<p><b>Conflicts with functions:</b> 
<?php if (isset($this->_sections['me'])) unset($this->_sections['me']);
$this->_sections['me']['name'] = 'me';
$this->_sections['me']['loop'] = is_array($this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_conflicts']['conflicts']) ? count($this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_conflicts']['conflicts']) : max(0, (int)$this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_conflicts']['conflicts']);
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
<?php echo $this->_tpl_vars['functions'][$this->_sections['func']['index']]['function_conflicts']['conflicts'][$this->_sections['me']['index']]; ?>
<br />
<?php endfor; endif; ?>
</p>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['functions'][$this->_sections['func']['index']]['sdesc'],'desc' => $this->_tpl_vars['functions'][$this->_sections['func']['index']]['desc'],'tags' => $this->_tpl_vars['functions'][$this->_sections['func']['index']]['tags'],'params' => $this->_tpl_vars['functions'][$this->_sections['func']['index']]['params'],'function' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<BR>
<p class="top">[ <a href="#top">Top</a> ]</p>
<?php endfor; endif; ?>
</UL>
<?php endif; ?>