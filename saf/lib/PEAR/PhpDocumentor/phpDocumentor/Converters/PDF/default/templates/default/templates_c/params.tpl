<?php /* Smarty version 2.5.0, created on 2003-04-23 18:10:12
         compiled from params.tpl */ ?>
<?php if (count ( $this->_tpl_vars['params'] )): ?><text size="10" left="15"><b><i>Function Parameters:</i></b>
</text><text size="11" left="20"><ul><?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
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
<li><i><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['type']; ?>
</i> <b><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['name']; ?>
</b> <?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['description']; ?>
</li>
<?php endfor; endif; ?></ul></text><?php endif; ?>