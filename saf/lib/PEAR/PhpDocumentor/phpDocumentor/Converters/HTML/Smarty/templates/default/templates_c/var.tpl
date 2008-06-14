<?php /* Smarty version 2.5.0, created on 2003-04-23 16:33:51
         compiled from var.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'replace', 'var.tpl', 7, false),)); ?><?php if (isset($this->_sections['vars'])) unset($this->_sections['vars']);
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
<?php if ($this->_tpl_vars['show'] == 'summary'): ?>
	var <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
, <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc']; ?>
<br>
<?php else: ?>
	<a name="<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_dest']; ?>
"></a>
	<p></p>
	<h4><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
 = <span class="value"><?php echo $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_default'], "\n", "<br>\n"), ' ', "&nbsp;"), "\t", "&nbsp;&nbsp;&nbsp;"); ?>
</span></h4>
	<div class="indent">
		<p class="linenumber">[line <?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']): ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['line_number']; ?>
<?php endif; ?>]</p>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc'],'desc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['desc'],'tags' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<p><b>Type:</b> <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_type']; ?>
</p>
		<p><b>Overrides:</b> <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_overrides']; ?>
</p>
	</div>
	<p class="top">[ <a href="#top">Top</a> ]</p>
<?php endif; ?>
<?php endfor; endif; ?>