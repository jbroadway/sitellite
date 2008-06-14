<?php /* Smarty version 2.5.0, created on 2003-07-22 03:22:45
         compiled from imethods.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'imethods.tpl', 16, false),)); ?>      <para>
<?php if (isset($this->_sections['classes'])) unset($this->_sections['classes']);
$this->_sections['classes']['name'] = 'classes';
$this->_sections['classes']['loop'] = is_array($this->_tpl_vars['imethods']) ? count($this->_tpl_vars['imethods']) : max(0, (int)$this->_tpl_vars['imethods']);
$this->_sections['classes']['show'] = true;
$this->_sections['classes']['max'] = $this->_sections['classes']['loop'];
$this->_sections['classes']['step'] = 1;
$this->_sections['classes']['start'] = $this->_sections['classes']['step'] > 0 ? 0 : $this->_sections['classes']['loop']-1;
if ($this->_sections['classes']['show']) {
    $this->_sections['classes']['total'] = $this->_sections['classes']['loop'];
    if ($this->_sections['classes']['total'] == 0)
        $this->_sections['classes']['show'] = false;
} else
    $this->_sections['classes']['total'] = 0;
if ($this->_sections['classes']['show']):

            for ($this->_sections['classes']['index'] = $this->_sections['classes']['start'], $this->_sections['classes']['iteration'] = 1;
                 $this->_sections['classes']['iteration'] <= $this->_sections['classes']['total'];
                 $this->_sections['classes']['index'] += $this->_sections['classes']['step'], $this->_sections['classes']['iteration']++):
$this->_sections['classes']['rownum'] = $this->_sections['classes']['iteration'];
$this->_sections['classes']['index_prev'] = $this->_sections['classes']['index'] - $this->_sections['classes']['step'];
$this->_sections['classes']['index_next'] = $this->_sections['classes']['index'] + $this->_sections['classes']['step'];
$this->_sections['classes']['first']      = ($this->_sections['classes']['iteration'] == 1);
$this->_sections['classes']['last']       = ($this->_sections['classes']['iteration'] == $this->_sections['classes']['total']);
?>
       <table>
        <title>Inherited from <?php echo $this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['parent_class']; ?>
</title>
        <tgroup cols="2">
         <thead>
          <row>
           <entry>Method Name</entry>
           <entry>Summary</entry>
          </row>
         </thead>
         <tbody>
<?php if (isset($this->_sections['m'])) unset($this->_sections['m']);
$this->_sections['m']['name'] = 'm';
$this->_sections['m']['loop'] = is_array($this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods']) ? count($this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods']) : max(0, (int)$this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods']);
$this->_sections['m']['show'] = true;
$this->_sections['m']['max'] = $this->_sections['m']['loop'];
$this->_sections['m']['step'] = 1;
$this->_sections['m']['start'] = $this->_sections['m']['step'] > 0 ? 0 : $this->_sections['m']['loop']-1;
if ($this->_sections['m']['show']) {
    $this->_sections['m']['total'] = $this->_sections['m']['loop'];
    if ($this->_sections['m']['total'] == 0)
        $this->_sections['m']['show'] = false;
} else
    $this->_sections['m']['total'] = 0;
if ($this->_sections['m']['show']):

            for ($this->_sections['m']['index'] = $this->_sections['m']['start'], $this->_sections['m']['iteration'] = 1;
                 $this->_sections['m']['iteration'] <= $this->_sections['m']['total'];
                 $this->_sections['m']['index'] += $this->_sections['m']['step'], $this->_sections['m']['iteration']++):
$this->_sections['m']['rownum'] = $this->_sections['m']['iteration'];
$this->_sections['m']['index_prev'] = $this->_sections['m']['index'] - $this->_sections['m']['step'];
$this->_sections['m']['index_next'] = $this->_sections['m']['index'] + $this->_sections['m']['step'];
$this->_sections['m']['first']      = ($this->_sections['m']['iteration'] == 1);
$this->_sections['m']['last']       = ($this->_sections['m']['iteration'] == $this->_sections['m']['total']);
?>
          <row>
           <entry><?php if ($this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods'][$this->_sections['m']['index']]['constructor']): ?> Constructor<?php endif; ?> <?php echo $this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods'][$this->_sections['m']['index']]['link']; ?>
</entry>
           <entry><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['imethods'][$this->_sections['classes']['index']]['imethods'][$this->_sections['m']['index']]['sdesc'], "&notdocumented;"); ?>
</entry>
          </row>
<?php endfor; endif; ?>
         </tbody>
        </tgroup>
       </table>
<?php endfor; endif; ?>
      </para>
