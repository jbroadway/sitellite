<?php /* Smarty version 2.5.0, created on 2003-07-25 20:35:40
         compiled from class_summary.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'class_summary.tpl', 8, false),)); ?><refentry id="<?php echo $this->_tpl_vars['id']; ?>
">
 <refnamediv>
 <refname>Class Summary <?php echo $this->_tpl_vars['class_name']; ?>
</refname>
 <refpurpose><?php echo $this->_tpl_vars['sdesc']; ?>
</refpurpose>
 </refnamediv>
<refsect1>
 <title><?php echo $this->_tpl_vars['sdesc']; ?>
</title>
 <?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['desc'], "&notdocumented;"); ?>

</refsect1>
<refsect1>
<title>Class Trees for <?php echo $this->_tpl_vars['class_name']; ?>
</title>
 <para>
  <?php if (isset($this->_sections['tree'])) unset($this->_sections['tree']);
$this->_sections['tree']['name'] = 'tree';
$this->_sections['tree']['loop'] = is_array($this->_tpl_vars['class_tree']) ? count($this->_tpl_vars['class_tree']) : max(0, (int)$this->_tpl_vars['class_tree']);
$this->_sections['tree']['show'] = true;
$this->_sections['tree']['max'] = $this->_sections['tree']['loop'];
$this->_sections['tree']['step'] = 1;
$this->_sections['tree']['start'] = $this->_sections['tree']['step'] > 0 ? 0 : $this->_sections['tree']['loop']-1;
if ($this->_sections['tree']['show']) {
    $this->_sections['tree']['total'] = $this->_sections['tree']['loop'];
    if ($this->_sections['tree']['total'] == 0)
        $this->_sections['tree']['show'] = false;
} else
    $this->_sections['tree']['total'] = 0;
if ($this->_sections['tree']['show']):

            for ($this->_sections['tree']['index'] = $this->_sections['tree']['start'], $this->_sections['tree']['iteration'] = 1;
                 $this->_sections['tree']['iteration'] <= $this->_sections['tree']['total'];
                 $this->_sections['tree']['index'] += $this->_sections['tree']['step'], $this->_sections['tree']['iteration']++):
$this->_sections['tree']['rownum'] = $this->_sections['tree']['iteration'];
$this->_sections['tree']['index_prev'] = $this->_sections['tree']['index'] - $this->_sections['tree']['step'];
$this->_sections['tree']['index_next'] = $this->_sections['tree']['index'] + $this->_sections['tree']['step'];
$this->_sections['tree']['first']      = ($this->_sections['tree']['iteration'] == 1);
$this->_sections['tree']['last']       = ($this->_sections['tree']['iteration'] == $this->_sections['tree']['total']);
?>
  <?php if (isset($this->_sections['mine'])) unset($this->_sections['mine']);
$this->_sections['mine']['name'] = 'mine';
$this->_sections['mine']['loop'] = is_array($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) ? count($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) : max(0, (int)$this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]);
$this->_sections['mine']['show'] = true;
$this->_sections['mine']['max'] = $this->_sections['mine']['loop'];
$this->_sections['mine']['step'] = 1;
$this->_sections['mine']['start'] = $this->_sections['mine']['step'] > 0 ? 0 : $this->_sections['mine']['loop']-1;
if ($this->_sections['mine']['show']) {
    $this->_sections['mine']['total'] = $this->_sections['mine']['loop'];
    if ($this->_sections['mine']['total'] == 0)
        $this->_sections['mine']['show'] = false;
} else
    $this->_sections['mine']['total'] = 0;
if ($this->_sections['mine']['show']):

            for ($this->_sections['mine']['index'] = $this->_sections['mine']['start'], $this->_sections['mine']['iteration'] = 1;
                 $this->_sections['mine']['iteration'] <= $this->_sections['mine']['total'];
                 $this->_sections['mine']['index'] += $this->_sections['mine']['step'], $this->_sections['mine']['iteration']++):
$this->_sections['mine']['rownum'] = $this->_sections['mine']['iteration'];
$this->_sections['mine']['index_prev'] = $this->_sections['mine']['index'] - $this->_sections['mine']['step'];
$this->_sections['mine']['index_next'] = $this->_sections['mine']['index'] + $this->_sections['mine']['step'];
$this->_sections['mine']['first']      = ($this->_sections['mine']['iteration'] == 1);
$this->_sections['mine']['last']       = ($this->_sections['mine']['iteration'] == $this->_sections['mine']['total']);
?> <?php endfor; endif; ?><itemizedlist>
  <?php if (isset($this->_sections['mine'])) unset($this->_sections['mine']);
$this->_sections['mine']['name'] = 'mine';
$this->_sections['mine']['loop'] = is_array($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) ? count($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) : max(0, (int)$this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]);
$this->_sections['mine']['show'] = true;
$this->_sections['mine']['max'] = $this->_sections['mine']['loop'];
$this->_sections['mine']['step'] = 1;
$this->_sections['mine']['start'] = $this->_sections['mine']['step'] > 0 ? 0 : $this->_sections['mine']['loop']-1;
if ($this->_sections['mine']['show']) {
    $this->_sections['mine']['total'] = $this->_sections['mine']['loop'];
    if ($this->_sections['mine']['total'] == 0)
        $this->_sections['mine']['show'] = false;
} else
    $this->_sections['mine']['total'] = 0;
if ($this->_sections['mine']['show']):

            for ($this->_sections['mine']['index'] = $this->_sections['mine']['start'], $this->_sections['mine']['iteration'] = 1;
                 $this->_sections['mine']['iteration'] <= $this->_sections['mine']['total'];
                 $this->_sections['mine']['index'] += $this->_sections['mine']['step'], $this->_sections['mine']['iteration']++):
$this->_sections['mine']['rownum'] = $this->_sections['mine']['iteration'];
$this->_sections['mine']['index_prev'] = $this->_sections['mine']['index'] - $this->_sections['mine']['step'];
$this->_sections['mine']['index_next'] = $this->_sections['mine']['index'] + $this->_sections['mine']['step'];
$this->_sections['mine']['first']      = ($this->_sections['mine']['iteration'] == 1);
$this->_sections['mine']['last']       = ($this->_sections['mine']['iteration'] == $this->_sections['mine']['total']);
?> <?php endfor; endif; ?> <listitem><para>
  <?php if (isset($this->_sections['mine'])) unset($this->_sections['mine']);
$this->_sections['mine']['name'] = 'mine';
$this->_sections['mine']['loop'] = is_array($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) ? count($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) : max(0, (int)$this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]);
$this->_sections['mine']['show'] = true;
$this->_sections['mine']['max'] = $this->_sections['mine']['loop'];
$this->_sections['mine']['step'] = 1;
$this->_sections['mine']['start'] = $this->_sections['mine']['step'] > 0 ? 0 : $this->_sections['mine']['loop']-1;
if ($this->_sections['mine']['show']) {
    $this->_sections['mine']['total'] = $this->_sections['mine']['loop'];
    if ($this->_sections['mine']['total'] == 0)
        $this->_sections['mine']['show'] = false;
} else
    $this->_sections['mine']['total'] = 0;
if ($this->_sections['mine']['show']):

            for ($this->_sections['mine']['index'] = $this->_sections['mine']['start'], $this->_sections['mine']['iteration'] = 1;
                 $this->_sections['mine']['iteration'] <= $this->_sections['mine']['total'];
                 $this->_sections['mine']['index'] += $this->_sections['mine']['step'], $this->_sections['mine']['iteration']++):
$this->_sections['mine']['rownum'] = $this->_sections['mine']['iteration'];
$this->_sections['mine']['index_prev'] = $this->_sections['mine']['index'] - $this->_sections['mine']['step'];
$this->_sections['mine']['index_next'] = $this->_sections['mine']['index'] + $this->_sections['mine']['step'];
$this->_sections['mine']['first']      = ($this->_sections['mine']['iteration'] == 1);
$this->_sections['mine']['last']       = ($this->_sections['mine']['iteration'] == $this->_sections['mine']['total']);
?> <?php endfor; endif; ?> <?php echo $this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]; ?>

  <?php endfor; endif; ?>
  <?php if (isset($this->_sections['tree'])) unset($this->_sections['tree']);
$this->_sections['tree']['name'] = 'tree';
$this->_sections['tree']['loop'] = is_array($this->_tpl_vars['class_tree']) ? count($this->_tpl_vars['class_tree']) : max(0, (int)$this->_tpl_vars['class_tree']);
$this->_sections['tree']['show'] = true;
$this->_sections['tree']['max'] = $this->_sections['tree']['loop'];
$this->_sections['tree']['step'] = 1;
$this->_sections['tree']['start'] = $this->_sections['tree']['step'] > 0 ? 0 : $this->_sections['tree']['loop']-1;
if ($this->_sections['tree']['show']) {
    $this->_sections['tree']['total'] = $this->_sections['tree']['loop'];
    if ($this->_sections['tree']['total'] == 0)
        $this->_sections['tree']['show'] = false;
} else
    $this->_sections['tree']['total'] = 0;
if ($this->_sections['tree']['show']):

            for ($this->_sections['tree']['index'] = $this->_sections['tree']['start'], $this->_sections['tree']['iteration'] = 1;
                 $this->_sections['tree']['iteration'] <= $this->_sections['tree']['total'];
                 $this->_sections['tree']['index'] += $this->_sections['tree']['step'], $this->_sections['tree']['iteration']++):
$this->_sections['tree']['rownum'] = $this->_sections['tree']['iteration'];
$this->_sections['tree']['index_prev'] = $this->_sections['tree']['index'] - $this->_sections['tree']['step'];
$this->_sections['tree']['index_next'] = $this->_sections['tree']['index'] + $this->_sections['tree']['step'];
$this->_sections['tree']['first']      = ($this->_sections['tree']['iteration'] == 1);
$this->_sections['tree']['last']       = ($this->_sections['tree']['iteration'] == $this->_sections['tree']['total']);
?>
  <?php if (isset($this->_sections['mine'])) unset($this->_sections['mine']);
$this->_sections['mine']['name'] = 'mine';
$this->_sections['mine']['loop'] = is_array($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) ? count($this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]) : max(0, (int)$this->_tpl_vars['class_tree'][$this->_sections['tree']['index']]);
$this->_sections['mine']['show'] = true;
$this->_sections['mine']['max'] = $this->_sections['mine']['loop'];
$this->_sections['mine']['step'] = 1;
$this->_sections['mine']['start'] = $this->_sections['mine']['step'] > 0 ? 0 : $this->_sections['mine']['loop']-1;
if ($this->_sections['mine']['show']) {
    $this->_sections['mine']['total'] = $this->_sections['mine']['loop'];
    if ($this->_sections['mine']['total'] == 0)
        $this->_sections['mine']['show'] = false;
} else
    $this->_sections['mine']['total'] = 0;
if ($this->_sections['mine']['show']):

            for ($this->_sections['mine']['index'] = $this->_sections['mine']['start'], $this->_sections['mine']['iteration'] = 1;
                 $this->_sections['mine']['iteration'] <= $this->_sections['mine']['total'];
                 $this->_sections['mine']['index'] += $this->_sections['mine']['step'], $this->_sections['mine']['iteration']++):
$this->_sections['mine']['rownum'] = $this->_sections['mine']['iteration'];
$this->_sections['mine']['index_prev'] = $this->_sections['mine']['index'] - $this->_sections['mine']['step'];
$this->_sections['mine']['index_next'] = $this->_sections['mine']['index'] + $this->_sections['mine']['step'];
$this->_sections['mine']['first']      = ($this->_sections['mine']['iteration'] == 1);
$this->_sections['mine']['last']       = ($this->_sections['mine']['iteration'] == $this->_sections['mine']['total']);
?> <?php endfor; endif; ?></para></listitem>
  </itemizedlist>
  <?php endfor; endif; ?>
 </para>
<?php if ($this->_tpl_vars['children']): ?>
 <para>
  <table>
   <title>Classes that extend <?php echo $this->_tpl_vars['class_name']; ?>
</title>
   <tgroup cols="2">
    <thead>
     <row>
      <entry>Class</entry>
      <entry>Summary</entry>
     </row>
    </thead>
    <tbody>
<?php if (isset($this->_sections['kids'])) unset($this->_sections['kids']);
$this->_sections['kids']['name'] = 'kids';
$this->_sections['kids']['loop'] = is_array($this->_tpl_vars['children']) ? count($this->_tpl_vars['children']) : max(0, (int)$this->_tpl_vars['children']);
$this->_sections['kids']['show'] = true;
$this->_sections['kids']['max'] = $this->_sections['kids']['loop'];
$this->_sections['kids']['step'] = 1;
$this->_sections['kids']['start'] = $this->_sections['kids']['step'] > 0 ? 0 : $this->_sections['kids']['loop']-1;
if ($this->_sections['kids']['show']) {
    $this->_sections['kids']['total'] = $this->_sections['kids']['loop'];
    if ($this->_sections['kids']['total'] == 0)
        $this->_sections['kids']['show'] = false;
} else
    $this->_sections['kids']['total'] = 0;
if ($this->_sections['kids']['show']):

            for ($this->_sections['kids']['index'] = $this->_sections['kids']['start'], $this->_sections['kids']['iteration'] = 1;
                 $this->_sections['kids']['iteration'] <= $this->_sections['kids']['total'];
                 $this->_sections['kids']['index'] += $this->_sections['kids']['step'], $this->_sections['kids']['iteration']++):
$this->_sections['kids']['rownum'] = $this->_sections['kids']['iteration'];
$this->_sections['kids']['index_prev'] = $this->_sections['kids']['index'] - $this->_sections['kids']['step'];
$this->_sections['kids']['index_next'] = $this->_sections['kids']['index'] + $this->_sections['kids']['step'];
$this->_sections['kids']['first']      = ($this->_sections['kids']['iteration'] == 1);
$this->_sections['kids']['last']       = ($this->_sections['kids']['iteration'] == $this->_sections['kids']['total']);
?>
     <row>
   <entry><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['link']; ?>
</entry>
   <entry><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['sdesc']; ?>
</entry>
     </row>
<?php endfor; endif; ?>
    </tbody>
   </tgroup>
  </table>
 </para>
<?php endif; ?>
<?php if ($this->_tpl_vars['imethods']): ?>
 <para>
  <?php echo $this->_tpl_vars['class_name']; ?>
 Inherited Methods
 </para>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("imethods.tpl", array('ivars' => $this->_tpl_vars['ivars']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
</refsect1>
</refentry>