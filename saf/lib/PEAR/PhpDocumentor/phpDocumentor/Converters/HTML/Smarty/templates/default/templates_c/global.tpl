<?php /* Smarty version 2.5.0, created on 2003-04-23 16:33:42
         compiled from global.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'replace', 'global.tpl', 12, false),)); ?><div id="global<?php if ($this->_tpl_vars['show'] == 'summary'): ?>_summary<?php endif; ?>">
<?php if (isset($this->_sections['glob'])) unset($this->_sections['glob']);
$this->_sections['glob']['name'] = 'glob';
$this->_sections['glob']['loop'] = is_array($this->_tpl_vars['globals']) ? count($this->_tpl_vars['globals']) : max(0, (int)$this->_tpl_vars['globals']);
$this->_sections['glob']['show'] = true;
$this->_sections['glob']['max'] = $this->_sections['glob']['loop'];
$this->_sections['glob']['step'] = 1;
$this->_sections['glob']['start'] = $this->_sections['glob']['step'] > 0 ? 0 : $this->_sections['glob']['loop']-1;
if ($this->_sections['glob']['show']) {
    $this->_sections['glob']['total'] = $this->_sections['glob']['loop'];
    if ($this->_sections['glob']['total'] == 0)
        $this->_sections['glob']['show'] = false;
} else
    $this->_sections['glob']['total'] = 0;
if ($this->_sections['glob']['show']):

            for ($this->_sections['glob']['index'] = $this->_sections['glob']['start'], $this->_sections['glob']['iteration'] = 1;
                 $this->_sections['glob']['iteration'] <= $this->_sections['glob']['total'];
                 $this->_sections['glob']['index'] += $this->_sections['glob']['step'], $this->_sections['glob']['iteration']++):
$this->_sections['glob']['rownum'] = $this->_sections['glob']['iteration'];
$this->_sections['glob']['index_prev'] = $this->_sections['glob']['index'] - $this->_sections['glob']['step'];
$this->_sections['glob']['index_next'] = $this->_sections['glob']['index'] + $this->_sections['glob']['step'];
$this->_sections['glob']['first']      = ($this->_sections['glob']['iteration'] == 1);
$this->_sections['glob']['last']       = ($this->_sections['glob']['iteration'] == $this->_sections['glob']['total']);
?>
<?php if ($this->_tpl_vars['show'] == 'summary'): ?>
global variable <a href="<?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['id']; ?>
"><?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_name']; ?>
</a> = <?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_value']; ?>
, <?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['sdesc']; ?>
<br>
<?php else: ?>
	<a name="<?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_link']; ?>
"></a>
	<h3><i><?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_type']; ?>
</i> <?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_name']; ?>
</h3>
	<div class="indent">
	<p class="linenumber">[line <?php if ($this->_tpl_vars['globals'][$this->_sections['glob']['index']]['slink']): ?><?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['line_number']; ?>
<?php endif; ?>]</p>
	<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['sdesc'],'desc' => $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['desc'],'tags' => $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

	<p><b>Default Value:</b><?php echo $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_run_mod_handler('replace', true, $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_value'], "\n", "<br>\n"), ' ', "&nbsp;"), "\t", "&nbsp;&nbsp;&nbsp;"); ?>
</p>
	<?php if ($this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_conflicts']['conflict_type']): ?>
	<p><b>Conflicts with globals:</b> 
	<?php if (isset($this->_sections['me'])) unset($this->_sections['me']);
$this->_sections['me']['name'] = 'me';
$this->_sections['me']['loop'] = is_array($this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_conflicts']['conflicts']) ? count($this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_conflicts']['conflicts']) : max(0, (int)$this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_conflicts']['conflicts']);
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
	<?php echo $this->_tpl_vars['globals'][$this->_sections['glob']['index']]['global_conflicts']['conflicts'][$this->_sections['me']['index']]; ?>
<br />
	<?php endfor; endif; ?>
	</p>
	<?php endif; ?>
	</div>
	<p class="top">[ <a href="#top">Top</a> ]</p>
<?php endif; ?>
<?php endfor; endif; ?>
</div>