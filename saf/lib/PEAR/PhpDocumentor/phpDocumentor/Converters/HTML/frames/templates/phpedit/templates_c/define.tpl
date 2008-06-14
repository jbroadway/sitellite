<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:43
         compiled from define.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'cycle', 'define.tpl', 10, false),
array('modifier', 'replace', 'define.tpl', 14, false),)); ?><!-- ============ CONSTANT DETAIL =========== -->

<A NAME='constant_detail'></A>
<h2 class="tab">Constants</h2>

<script type="text/javascript">tp1.addTabPage( document.getElementById( "tabPage3" ) );</script>

<?php if (isset($this->_sections['def'])) unset($this->_sections['def']);
$this->_sections['def']['name'] = 'def';
$this->_sections['def']['loop'] = is_array($this->_tpl_vars['defines']) ? count($this->_tpl_vars['defines']) : max(0, (int)$this->_tpl_vars['defines']);
$this->_sections['def']['show'] = true;
$this->_sections['def']['max'] = $this->_sections['def']['loop'];
$this->_sections['def']['step'] = 1;
$this->_sections['def']['start'] = $this->_sections['def']['step'] > 0 ? 0 : $this->_sections['def']['loop']-1;
if ($this->_sections['def']['show']) {
    $this->_sections['def']['total'] = $this->_sections['def']['loop'];
    if ($this->_sections['def']['total'] == 0)
        $this->_sections['def']['show'] = false;
} else
    $this->_sections['def']['total'] = 0;
if ($this->_sections['def']['show']):

            for ($this->_sections['def']['index'] = $this->_sections['def']['start'], $this->_sections['def']['iteration'] = 1;
                 $this->_sections['def']['iteration'] <= $this->_sections['def']['total'];
                 $this->_sections['def']['index'] += $this->_sections['def']['step'], $this->_sections['def']['iteration']++):
$this->_sections['def']['rownum'] = $this->_sections['def']['iteration'];
$this->_sections['def']['index_prev'] = $this->_sections['def']['index'] - $this->_sections['def']['step'];
$this->_sections['def']['index_next'] = $this->_sections['def']['index'] + $this->_sections['def']['step'];
$this->_sections['def']['first']      = ($this->_sections['def']['iteration'] == 1);
$this->_sections['def']['last']       = ($this->_sections['def']['iteration'] == $this->_sections['def']['total']);
?>
<a name="<?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_link']; ?>
"><!-- --></a>
<div style="background='<?php echo smarty_function_cycle(array('values' => "#ffffff,#eeeeee"), $this) ; ?>
'">
<h4>
  <img src="<?php echo $this->_tpl_vars['subdir']; ?>
media/images/Constant.gif" border="0" /> <strong class="property"><?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_name']; ?>
</strong> (line <span class="linenumber"><?php if ($this->_tpl_vars['defines'][$this->_sections['def']['index']]['slink']): ?><?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['line_number']; ?>
<?php endif; ?></span>)
 </h4> 
<h4><?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_name']; ?>
 : <?php echo $this->_run_mod_handler('replace', true, $this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_value'], "\n", "<br />"); ?>
</h4>
<?php if ($this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_conflicts']['conflict_type']): ?>
	<p><span class="warning">Warning:</span> Conflicts with constants:<br />
	<?php if (isset($this->_sections['me'])) unset($this->_sections['me']);
$this->_sections['me']['name'] = 'me';
$this->_sections['me']['loop'] = is_array($this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_conflicts']['conflicts']) ? count($this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_conflicts']['conflicts']) : max(0, (int)$this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_conflicts']['conflicts']);
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
		<?php echo $this->_tpl_vars['defines'][$this->_sections['def']['index']]['define_conflicts']['conflicts'][$this->_sections['me']['index']]; ?>
<br />
	<?php endfor; endif; ?>
	</p>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('sdesc' => $this->_tpl_vars['defines'][$this->_sections['def']['index']]['sdesc'],'desc' => $this->_tpl_vars['defines'][$this->_sections['def']['index']]['desc'],'tags' => $this->_tpl_vars['defines'][$this->_sections['def']['index']]['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endfor; endif; ?>