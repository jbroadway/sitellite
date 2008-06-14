<?php /* Smarty version 2.5.0, created on 2003-06-02 17:35:15
         compiled from var.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'cycle', 'var.tpl', 14, false),
array('modifier', 'replace', 'var.tpl', 16, false),)); ?>
<h2 class="tab">Class Variables</h2>
<!-- ============ VARIABLE DETAIL =========== -->
<strong>Summary:</strong><br />
<?php if (isset($this->_sections['vars'])) unset($this->_sections['vars']);
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
<div class="var-title">
    <span class="var-type"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_type']; ?>
</span>
    <a href="#<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_dest']; ?>
" title="details" class="property"><strong><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
</strong></a>
</div>
<?php endfor; endif; ?>
<hr />
<?php if (isset($this->_sections['vars'])) unset($this->_sections['vars']);
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
<a name="<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_dest']; ?>
" id="<?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_dest']; ?>
"><!-- --></A>
<div style="background='<?php echo smarty_function_cycle(array('values' => "#ffffff,#eeeeee"), $this) ; ?>
'">
<h4>
<img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/PublicProperty.gif" border="0" /> <strong class="property"><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_name']; ?>
<?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_default']): ?> = <?php echo $this->_run_mod_handler('replace', true, $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_default'], "\n", "<br />"); ?>
<?php endif; ?></strong> (line <span class="linenumber"><?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']): ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['line_number']; ?>
<?php endif; ?></span>)
 </h4>
<h4>Data type : <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_type']; ?>
</h4>
<?php if ($this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_overrides']): ?><p><strong>Overrides:</strong> <?php echo $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['var_overrides']; ?>
<br></p><?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['sdesc'],'desc' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['desc'],'tags' => $this->_tpl_vars['vars'][$this->_sections['vars']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endfor; endif; ?>
<script type="text/javascript">tp1.addTabPage( document.getElementById( "tabPage1" ) );</script>
