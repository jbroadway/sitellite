<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:42
         compiled from docblock.tpl */ ?>
<!-- ========== Info from phpDoc block ========= -->
<?php if ($this->_tpl_vars['sdesc']): ?>
<h5><?php echo $this->_tpl_vars['sdesc']; ?>
</h5>
<?php endif; ?>
<?php if ($this->_tpl_vars['desc']): ?>
<div class="desc"><?php echo $this->_tpl_vars['desc']; ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['function']): ?>
	<?php if ($this->_tpl_vars['params']): ?>
	<h4>Parameters</h4>
	<ul>
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
		<li><strong><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['datatype']; ?>
 <?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['var']; ?>
</strong>: <?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['data']; ?>
</li>
	<?php endfor; endif; ?>
	</ul>
	<?php endif; ?>
	
	<h4>Info</h4>
	<ul>
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
		<li><strong><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword']; ?>
</strong> - <?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['data']; ?>
</li>
	<?php endfor; endif; ?>
	</ul>
<?php else: ?>
<ul>
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
	<li><strong><?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword']; ?>
:</strong> - <?php echo $this->_tpl_vars['tags'][$this->_sections['tags']['index']]['data']; ?>
</li>
	<?php endfor; endif; ?>
</ul>
<?php endif; ?>