<?php /* Smarty version 2.5.0, created on 2003-04-23 22:06:39
         compiled from docblock.tpl */ ?>
<!-- ========== Info from phpDoc block ========= -->
<?php if ($this->_tpl_vars['function']): ?>
	<?php if ($this->_tpl_vars['params']): ?>
	<p class="label"><b>Parameters</b></p>
	<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['params']) ? count($this->_tpl_vars['params']) : max(0, (int)$this->_tpl_vars['params']);
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
		<p class=dt><i><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['var']; ?>
</i></p>
		<p class=indent><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['data']; ?>
</p>
	<?php endfor; endif; ?>
	<?php endif; ?>
<?php endif; ?>
<?php if (isset($this->_sections['tags'])) unset($this->_sections['tags']);
$this->_sections['tags']['name'] = 'tags';
$this->_sections['tags']['loop'] = is_array($this->_tpl_vars['tags']) ? count($this->_tpl_vars['tags']) : max(0, (int)$this->_tpl_vars['tags']);
$this->_sections['tags']['show'] = true;
$this->_sections['tags']['max'] = $this->_sections['tags']['loop'];
$this->_sections['tags']['step'] = 1;
$this->_sections['tags']['start'] = $this->_sections['tags']['step'] > 0 ? 0 : $this->_sections['tags']['loop']-1;
if ($this->_sections['tags']['show']) {
    $this->_sections['tags']['total'] = $this->_sections['tags']['loop'];
    if ($this->_sections['tags']['total'] == 0)
        $this->_sections['tags']['show'] = false;
} else
    $this->_sections['tags']['total'] = 0;
if ($this->_sections['tags']['show']):

            for ($this->_sections['tags']['index'] = $this->_sections['tags']['start'], $this->_sections['tags']['iteration'] = 1;
                 $this->_sections['tags']['iteration'] <= $this->_sections['tags']['total'];
                 $this->_sections['tags']['index'] += $this->_sections['tags']['step'], $this->_sections['tags']['iteration']++):
$this->_sections['tags']['rownum'] = $this->_sections['tags']['iteration'];
$this->_sections['tags']['index_prev'] = $this->_sections['tags']['index'] - $this->_sections['tags']['step'];
$this->_sections['tags']['index_next'] = $this->_sections['tags']['index'] + $this->_sections['tags']['step'];
$this->_sections['tags']['first']      = ($this->_sections['tags']['iteration'] == 1);
$this->_sections['tags']['last']       = ($this->_sections['tags']['iteration'] == $this->_sections['tags']['total']);
?>
<?php if ($this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword'] == 'return'): ?>
	<p class="label"><b>Returns</b></p>
		<p class=indent><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['data']; ?>
</p>
<?php endif; ?>
<?php endfor; endif; ?>
<?php if ($this->_tpl_vars['sdesc'] || $this->_tpl_vars['desc']): ?>
<p class="label"><b>Remarks</b></p>
<?php endif; ?>
<?php if ($this->_tpl_vars['sdesc']): ?>
<p><?php echo $this->_tpl_vars['sdesc']; ?>
</p>
<?php endif; ?>
<?php if ($this->_tpl_vars['desc']): ?>
<p><?php echo $this->_tpl_vars['desc']; ?>
</p>
<?php endif; ?>
<?php if (isset($this->_sections['tags'])) unset($this->_sections['tags']);
$this->_sections['tags']['name'] = 'tags';
$this->_sections['tags']['loop'] = is_array($this->_tpl_vars['tags']) ? count($this->_tpl_vars['tags']) : max(0, (int)$this->_tpl_vars['tags']);
$this->_sections['tags']['show'] = true;
$this->_sections['tags']['max'] = $this->_sections['tags']['loop'];
$this->_sections['tags']['step'] = 1;
$this->_sections['tags']['start'] = $this->_sections['tags']['step'] > 0 ? 0 : $this->_sections['tags']['loop']-1;
if ($this->_sections['tags']['show']) {
    $this->_sections['tags']['total'] = $this->_sections['tags']['loop'];
    if ($this->_sections['tags']['total'] == 0)
        $this->_sections['tags']['show'] = false;
} else
    $this->_sections['tags']['total'] = 0;
if ($this->_sections['tags']['show']):

            for ($this->_sections['tags']['index'] = $this->_sections['tags']['start'], $this->_sections['tags']['iteration'] = 1;
                 $this->_sections['tags']['iteration'] <= $this->_sections['tags']['total'];
                 $this->_sections['tags']['index'] += $this->_sections['tags']['step'], $this->_sections['tags']['iteration']++):
$this->_sections['tags']['rownum'] = $this->_sections['tags']['iteration'];
$this->_sections['tags']['index_prev'] = $this->_sections['tags']['index'] - $this->_sections['tags']['step'];
$this->_sections['tags']['index_next'] = $this->_sections['tags']['index'] + $this->_sections['tags']['step'];
$this->_sections['tags']['first']      = ($this->_sections['tags']['iteration'] == 1);
$this->_sections['tags']['last']       = ($this->_sections['tags']['iteration'] == $this->_sections['tags']['total']);
?>
<?php if ($this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword'] != 'return'): ?>
	<p class="label"><b><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword']; ?>
</b></p>
		<p class=indent><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['data']; ?>
</p>
<?php endif; ?>
<?php endfor; endif; ?>