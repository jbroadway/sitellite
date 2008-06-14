<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:42
         compiled from include.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'cycle', 'include.tpl', 9, false),)); ?><!-- ============ Includes DETAIL =========== -->

<h2 class="tab">Include/Require Statements</h2>
<script type="text/javascript">tp1.addTabPage( document.getElementById( "tabPage1" ) );</script>


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
<a name="<?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_file']; ?>
"><!-- --></a>
<div style="background='<?php echo smarty_function_cycle(array('values' => "#ffffff,#eeeeee"), $this) ; ?>
'">
<h4>
  <img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/file.png" border="0" /> <strong class="Property"><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_value']; ?>
</strong> (line <span class="linenumber"><?php if ($this->_tpl_vars['includes'][$this->_sections['includes']['index']]['slink']): ?><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['line_number']; ?>
<?php endif; ?></span>)
 </h4> 
<h4><?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_name']; ?>
 : <?php echo $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['include_value']; ?>
</h4>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['sdesc'],'desc' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['desc'],'tags' => $this->_tpl_vars['includes'][$this->_sections['includes']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endfor; endif; ?>