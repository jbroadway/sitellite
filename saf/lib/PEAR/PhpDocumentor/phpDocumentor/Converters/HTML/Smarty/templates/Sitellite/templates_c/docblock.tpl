<?php /* Smarty version 2.5.0, created on 2003-06-02 18:00:16
         compiled from docblock.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'docblock.tpl', 1, false),)); ?><?php if ($this->_tpl_vars['sdesc'] != ''): ?><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['sdesc'], ''); ?>
<br /><br /><?php endif; ?>
<?php if ($this->_tpl_vars['desc'] != ''): ?><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['desc'], ''); ?>
<br /><?php endif; ?>
<?php if (count ( $this->_tpl_vars['tags'] ) > 0): ?>
<br /><br />
<h4>Tags:</h4>
<div class="tags">
<table border="0" cellspacing="0" cellpadding="0">
<?php if (isset($this->_sections['tag'])) unset($this->_sections['tag']);
$this->_sections['tag']['name'] = 'tag';
$this->_sections['tag']['loop'] = is_array($this->_tpl_vars['tags']) ? count($this->_tpl_vars['tags']) : max(0, (int)$this->_tpl_vars['tags']);
$this->_sections['tag']['show'] = true;
$this->_sections['tag']['max'] = $this->_sections['tag']['loop'];
$this->_sections['tag']['step'] = 1;
$this->_sections['tag']['start'] = $this->_sections['tag']['step'] > 0 ? 0 : $this->_sections['tag']['loop']-1;
if ($this->_sections['tag']['show']) {
    $this->_sections['tag']['total'] = $this->_sections['tag']['loop'];
    if ($this->_sections['tag']['total'] == 0)
        $this->_sections['tag']['show'] = false;
} else
    $this->_sections['tag']['total'] = 0;
if ($this->_sections['tag']['show']):

            for ($this->_sections['tag']['index'] = $this->_sections['tag']['start'], $this->_sections['tag']['iteration'] = 1;
                 $this->_sections['tag']['iteration'] <= $this->_sections['tag']['total'];
                 $this->_sections['tag']['index'] += $this->_sections['tag']['step'], $this->_sections['tag']['iteration']++):
$this->_sections['tag']['rownum'] = $this->_sections['tag']['iteration'];
$this->_sections['tag']['index_prev'] = $this->_sections['tag']['index'] - $this->_sections['tag']['step'];
$this->_sections['tag']['index_next'] = $this->_sections['tag']['index'] + $this->_sections['tag']['step'];
$this->_sections['tag']['first']      = ($this->_sections['tag']['iteration'] == 1);
$this->_sections['tag']['last']       = ($this->_sections['tag']['iteration'] == $this->_sections['tag']['total']);
?>
  <tr>
    <td><b><?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['keyword']; ?>
:</b>&nbsp;&nbsp;</td><td><?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['data']; ?>
</td>
  </tr>
<?php endfor; endif; ?>
</table>
</div>
<?php endif; ?>