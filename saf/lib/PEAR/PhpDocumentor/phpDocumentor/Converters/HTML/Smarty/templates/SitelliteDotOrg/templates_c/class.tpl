<?php /* Smarty version 2.5.0, created on 2004-05-28 14:03:31
         compiled from class.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'class.tpl', 16, false),
array('function', 'assign', 'class.tpl', 34, false),)); ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('eltype' => 'class','hasel' => true,'contents' => $this->_tpl_vars['classcontents']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>

<?php if ($this->_tpl_vars['conflicts']['conflict_type']): ?><div class="warning">Conflicts with classes:<br />
	<?php if (isset($this->_sections['me'])) unset($this->_sections['me']);
$this->_sections['me']['name'] = 'me';
$this->_sections['me']['loop'] = is_array($this->_tpl_vars['conflicts']['conflicts']) ? count($this->_tpl_vars['conflicts']['conflicts']) : max(0, (int)$this->_tpl_vars['conflicts']['conflicts']);
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
	<?php echo $this->_tpl_vars['conflicts']['conflicts'][$this->_sections['me']['index']]; ?>
<br />
	<?php endfor; endif; ?>
</div>
	<?php endif; ?>


<table width="100%" border="0">
<tr><td valign="top" width="50%">

<h2><a href="#class_details">Class Overview</a></h2>
<pre><?php if (isset($this->_sections['tree'])) unset($this->_sections['tree']);
$this->_sections['tree']['name'] = 'tree';
$this->_sections['tree']['loop'] = is_array($this->_tpl_vars['class_tree']['classes']) ? count($this->_tpl_vars['class_tree']['classes']) : max(0, (int)$this->_tpl_vars['class_tree']['classes']);
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
?><?php echo $this->_tpl_vars['class_tree']['classes'][$this->_sections['tree']['index']]; ?>
<?php echo $this->_tpl_vars['class_tree']['distance'][$this->_sections['tree']['index']]; ?>
<?php endfor; endif; ?></pre><br />
<div class="description"><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['sdesc'], ''); ?>
</div><br /><br />
<?php if ($this->_tpl_vars['tutorial']): ?>
<h4 class="classtutorial">Class Tutorial</h3>
<ul>
	<li><?php echo $this->_tpl_vars['tutorial']; ?>
</li>
</ul>
<?php endif; ?>
<?php if (count ( $this->_tpl_vars['tags'] ) > 0): ?>
<h3>Author(s)</h3>
<ul>
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
    <?php if ($this->_tpl_vars['tags'][$this->_sections['tag']['index']]['keyword'] == 'author'): ?>
    <li><?php echo $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['data']; ?>
</li>
    <?php endif; ?>
  <?php endfor; endif; ?>
</ul>
<?php endif; ?>

<?php echo smarty_function_assign(array('var' => 'version','value' => ""), $this) ; ?>

<?php echo smarty_function_assign(array('var' => 'copyright','value' => ""), $this) ; ?>


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
  <?php if ($this->_tpl_vars['tags'][$this->_sections['tag']['index']]['keyword'] == 'version'): ?>
  <?php echo smarty_function_assign(array('var' => 'version','value' => $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['data']), $this) ; ?>

  <?php endif; ?>
  <?php if ($this->_tpl_vars['tags'][$this->_sections['tag']['index']]['keyword'] == 'copyright'): ?>
  <?php echo smarty_function_assign(array('var' => 'copyright','value' => $this->_tpl_vars['tags'][$this->_sections['tag']['index']]['data']), $this) ; ?>

  <?php endif; ?>
<?php endfor; endif; ?>

<?php if ($this->_tpl_vars['version'] != ""): ?>
<h3>Version</h3>
<ul>
  <li><?php echo $this->_tpl_vars['version']; ?>
</li>
</ul>
<?php endif; ?>

<?php if ($this->_tpl_vars['copyright'] != ""): ?>
<h3>Copyright</h3>
<ul>
  <li><?php echo $this->_tpl_vars['copyright']; ?>
</li>
</ul>
<?php endif; ?>

</td>

<?php if (count ( $this->_tpl_vars['contents']['var'] ) > 0): ?>
<td valign="top" width="25%">
<h2><a href="#class_vars">Variables</a></h2>
<ul>
  <?php if (isset($this->_sections['contents'])) unset($this->_sections['contents']);
$this->_sections['contents']['name'] = 'contents';
$this->_sections['contents']['loop'] = is_array($this->_tpl_vars['contents']['var']) ? count($this->_tpl_vars['contents']['var']) : max(0, (int)$this->_tpl_vars['contents']['var']);
$this->_sections['contents']['show'] = true;
$this->_sections['contents']['max'] = $this->_sections['contents']['loop'];
$this->_sections['contents']['step'] = 1;
$this->_sections['contents']['start'] = $this->_sections['contents']['step'] > 0 ? 0 : $this->_sections['contents']['loop']-1;
if ($this->_sections['contents']['show']) {
    $this->_sections['contents']['total'] = $this->_sections['contents']['loop'];
    if ($this->_sections['contents']['total'] == 0)
        $this->_sections['contents']['show'] = false;
} else
    $this->_sections['contents']['total'] = 0;
if ($this->_sections['contents']['show']):

            for ($this->_sections['contents']['index'] = $this->_sections['contents']['start'], $this->_sections['contents']['iteration'] = 1;
                 $this->_sections['contents']['iteration'] <= $this->_sections['contents']['total'];
                 $this->_sections['contents']['index'] += $this->_sections['contents']['step'], $this->_sections['contents']['iteration']++):
$this->_sections['contents']['rownum'] = $this->_sections['contents']['iteration'];
$this->_sections['contents']['index_prev'] = $this->_sections['contents']['index'] - $this->_sections['contents']['step'];
$this->_sections['contents']['index_next'] = $this->_sections['contents']['index'] + $this->_sections['contents']['step'];
$this->_sections['contents']['first']      = ($this->_sections['contents']['iteration'] == 1);
$this->_sections['contents']['last']       = ($this->_sections['contents']['iteration'] == $this->_sections['contents']['total']);
?>
  <li><?php echo $this->_tpl_vars['contents']['var'][$this->_sections['contents']['index']]; ?>
</li>
  <?php endfor; endif; ?>
</ul>
</td>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['contents']['method'] ) > 0): ?>
<td valign="top" width="25%">
<h2><a href="#class_methods">Methods</a></h2>
<ul>
  <?php if (isset($this->_sections['contents'])) unset($this->_sections['contents']);
$this->_sections['contents']['name'] = 'contents';
$this->_sections['contents']['loop'] = is_array($this->_tpl_vars['contents']['method']) ? count($this->_tpl_vars['contents']['method']) : max(0, (int)$this->_tpl_vars['contents']['method']);
$this->_sections['contents']['show'] = true;
$this->_sections['contents']['max'] = $this->_sections['contents']['loop'];
$this->_sections['contents']['step'] = 1;
$this->_sections['contents']['start'] = $this->_sections['contents']['step'] > 0 ? 0 : $this->_sections['contents']['loop']-1;
if ($this->_sections['contents']['show']) {
    $this->_sections['contents']['total'] = $this->_sections['contents']['loop'];
    if ($this->_sections['contents']['total'] == 0)
        $this->_sections['contents']['show'] = false;
} else
    $this->_sections['contents']['total'] = 0;
if ($this->_sections['contents']['show']):

            for ($this->_sections['contents']['index'] = $this->_sections['contents']['start'], $this->_sections['contents']['iteration'] = 1;
                 $this->_sections['contents']['iteration'] <= $this->_sections['contents']['total'];
                 $this->_sections['contents']['index'] += $this->_sections['contents']['step'], $this->_sections['contents']['iteration']++):
$this->_sections['contents']['rownum'] = $this->_sections['contents']['iteration'];
$this->_sections['contents']['index_prev'] = $this->_sections['contents']['index'] - $this->_sections['contents']['step'];
$this->_sections['contents']['index_next'] = $this->_sections['contents']['index'] + $this->_sections['contents']['step'];
$this->_sections['contents']['first']      = ($this->_sections['contents']['iteration'] == 1);
$this->_sections['contents']['last']       = ($this->_sections['contents']['iteration'] == $this->_sections['contents']['total']);
?>
  <li><?php echo $this->_tpl_vars['contents']['method'][$this->_sections['contents']['index']]; ?>
</li>
  <?php endfor; endif; ?>
</ul>
</td>
<?php endif; ?>

</tr></table>
<hr />

<table width="100%" border="0"><tr>



<?php if ($this->_tpl_vars['children']): ?>
<td valign="top">
<h2>Child Classes</h2>
<div class="tags">
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
<dl>
<dt><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['link']; ?>
</dt>
	<dd><?php echo $this->_tpl_vars['children'][$this->_sections['kids']['index']]['sdesc']; ?>
</dd>
</dl>
<?php endfor; endif; ?>
</div>
</td>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['ivars'] ) > 0): ?>
<td valign="top">
<h2>Inherited Variables</h2>
<?php if (isset($this->_sections['ivars'])) unset($this->_sections['ivars']);
$this->_sections['ivars']['name'] = 'ivars';
$this->_sections['ivars']['loop'] = is_array($this->_tpl_vars['ivars']) ? count($this->_tpl_vars['ivars']) : max(0, (int)$this->_tpl_vars['ivars']);
$this->_sections['ivars']['show'] = true;
$this->_sections['ivars']['max'] = $this->_sections['ivars']['loop'];
$this->_sections['ivars']['step'] = 1;
$this->_sections['ivars']['start'] = $this->_sections['ivars']['step'] > 0 ? 0 : $this->_sections['ivars']['loop']-1;
if ($this->_sections['ivars']['show']) {
    $this->_sections['ivars']['total'] = $this->_sections['ivars']['loop'];
    if ($this->_sections['ivars']['total'] == 0)
        $this->_sections['ivars']['show'] = false;
} else
    $this->_sections['ivars']['total'] = 0;
if ($this->_sections['ivars']['show']):

            for ($this->_sections['ivars']['index'] = $this->_sections['ivars']['start'], $this->_sections['ivars']['iteration'] = 1;
                 $this->_sections['ivars']['iteration'] <= $this->_sections['ivars']['total'];
                 $this->_sections['ivars']['index'] += $this->_sections['ivars']['step'], $this->_sections['ivars']['iteration']++):
$this->_sections['ivars']['rownum'] = $this->_sections['ivars']['iteration'];
$this->_sections['ivars']['index_prev'] = $this->_sections['ivars']['index'] - $this->_sections['ivars']['step'];
$this->_sections['ivars']['index_next'] = $this->_sections['ivars']['index'] + $this->_sections['ivars']['step'];
$this->_sections['ivars']['first']      = ($this->_sections['ivars']['iteration'] == 1);
$this->_sections['ivars']['last']       = ($this->_sections['ivars']['iteration'] == $this->_sections['ivars']['total']);
?>
<div class="tags">
<h3>Class: <?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['parent_class']; ?>
</h3>
<dl>
<?php if (isset($this->_sections['ivars2'])) unset($this->_sections['ivars2']);
$this->_sections['ivars2']['name'] = 'ivars2';
$this->_sections['ivars2']['loop'] = is_array($this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']) ? count($this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']) : max(0, (int)$this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars']);
$this->_sections['ivars2']['show'] = true;
$this->_sections['ivars2']['max'] = $this->_sections['ivars2']['loop'];
$this->_sections['ivars2']['step'] = 1;
$this->_sections['ivars2']['start'] = $this->_sections['ivars2']['step'] > 0 ? 0 : $this->_sections['ivars2']['loop']-1;
if ($this->_sections['ivars2']['show']) {
    $this->_sections['ivars2']['total'] = $this->_sections['ivars2']['loop'];
    if ($this->_sections['ivars2']['total'] == 0)
        $this->_sections['ivars2']['show'] = false;
} else
    $this->_sections['ivars2']['total'] = 0;
if ($this->_sections['ivars2']['show']):

            for ($this->_sections['ivars2']['index'] = $this->_sections['ivars2']['start'], $this->_sections['ivars2']['iteration'] = 1;
                 $this->_sections['ivars2']['iteration'] <= $this->_sections['ivars2']['total'];
                 $this->_sections['ivars2']['index'] += $this->_sections['ivars2']['step'], $this->_sections['ivars2']['iteration']++):
$this->_sections['ivars2']['rownum'] = $this->_sections['ivars2']['iteration'];
$this->_sections['ivars2']['index_prev'] = $this->_sections['ivars2']['index'] - $this->_sections['ivars2']['step'];
$this->_sections['ivars2']['index_next'] = $this->_sections['ivars2']['index'] + $this->_sections['ivars2']['step'];
$this->_sections['ivars2']['first']      = ($this->_sections['ivars2']['iteration'] == 1);
$this->_sections['ivars2']['last']       = ($this->_sections['ivars2']['iteration'] == $this->_sections['ivars2']['total']);
?>
<dt>
  <?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['link']; ?>

  
</dt>
<dd>
  <?php echo $this->_tpl_vars['ivars'][$this->_sections['ivars']['index']]['ivars'][$this->_sections['ivars2']['index']]['ivars_sdesc']; ?>
 
</dd>
<?php endfor; endif; ?>
</dl>
</div>
<?php endfor; endif; ?>
</td>
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['imethods'] ) > 0): ?>
<td valign="top">
<h2>Inherited Methods</h2>
<div class="tags">
<?php if (isset($this->_sections['imethods'])) unset($this->_sections['imethods']);
$this->_sections['imethods']['name'] = 'imethods';
$this->_sections['imethods']['loop'] = is_array($this->_tpl_vars['imethods']) ? count($this->_tpl_vars['imethods']) : max(0, (int)$this->_tpl_vars['imethods']);
$this->_sections['imethods']['show'] = true;
$this->_sections['imethods']['max'] = $this->_sections['imethods']['loop'];
$this->_sections['imethods']['step'] = 1;
$this->_sections['imethods']['start'] = $this->_sections['imethods']['step'] > 0 ? 0 : $this->_sections['imethods']['loop']-1;
if ($this->_sections['imethods']['show']) {
    $this->_sections['imethods']['total'] = $this->_sections['imethods']['loop'];
    if ($this->_sections['imethods']['total'] == 0)
        $this->_sections['imethods']['show'] = false;
} else
    $this->_sections['imethods']['total'] = 0;
if ($this->_sections['imethods']['show']):

            for ($this->_sections['imethods']['index'] = $this->_sections['imethods']['start'], $this->_sections['imethods']['iteration'] = 1;
                 $this->_sections['imethods']['iteration'] <= $this->_sections['imethods']['total'];
                 $this->_sections['imethods']['index'] += $this->_sections['imethods']['step'], $this->_sections['imethods']['iteration']++):
$this->_sections['imethods']['rownum'] = $this->_sections['imethods']['iteration'];
$this->_sections['imethods']['index_prev'] = $this->_sections['imethods']['index'] - $this->_sections['imethods']['step'];
$this->_sections['imethods']['index_next'] = $this->_sections['imethods']['index'] + $this->_sections['imethods']['step'];
$this->_sections['imethods']['first']      = ($this->_sections['imethods']['iteration'] == 1);
$this->_sections['imethods']['last']       = ($this->_sections['imethods']['iteration'] == $this->_sections['imethods']['total']);
?>
<h3>Class: <?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['parent_class']; ?>
</h3>
<dl>
  <?php if (isset($this->_sections['im2'])) unset($this->_sections['im2']);
$this->_sections['im2']['name'] = 'im2';
$this->_sections['im2']['loop'] = is_array($this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']) ? count($this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']) : max(0, (int)$this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods']);
$this->_sections['im2']['show'] = true;
$this->_sections['im2']['max'] = $this->_sections['im2']['loop'];
$this->_sections['im2']['step'] = 1;
$this->_sections['im2']['start'] = $this->_sections['im2']['step'] > 0 ? 0 : $this->_sections['im2']['loop']-1;
if ($this->_sections['im2']['show']) {
    $this->_sections['im2']['total'] = $this->_sections['im2']['loop'];
    if ($this->_sections['im2']['total'] == 0)
        $this->_sections['im2']['show'] = false;
} else
    $this->_sections['im2']['total'] = 0;
if ($this->_sections['im2']['show']):

            for ($this->_sections['im2']['index'] = $this->_sections['im2']['start'], $this->_sections['im2']['iteration'] = 1;
                 $this->_sections['im2']['iteration'] <= $this->_sections['im2']['total'];
                 $this->_sections['im2']['index'] += $this->_sections['im2']['step'], $this->_sections['im2']['iteration']++):
$this->_sections['im2']['rownum'] = $this->_sections['im2']['iteration'];
$this->_sections['im2']['index_prev'] = $this->_sections['im2']['index'] - $this->_sections['im2']['step'];
$this->_sections['im2']['index_next'] = $this->_sections['im2']['index'] + $this->_sections['im2']['step'];
$this->_sections['im2']['first']      = ($this->_sections['im2']['iteration'] == 1);
$this->_sections['im2']['last']       = ($this->_sections['im2']['iteration'] == $this->_sections['im2']['total']);
?>
  <dt>
    <?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['link']; ?>


  </dt>
  <dd>
    <?php echo $this->_tpl_vars['imethods'][$this->_sections['imethods']['index']]['imethods'][$this->_sections['im2']['index']]['sdesc']; ?>

  </dd>
  <?php endfor; endif; ?>
</dl>
<?php endfor; endif; ?>
</div>
</td>
<?php endif; ?>

</tr></table>
<hr />

<a name="class_details"></a>
<h2>Class Details</h2>
<div class="tags">
[line <?php if ($this->_tpl_vars['class_slink']): ?><?php echo $this->_tpl_vars['class_slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['line_number']; ?>
<?php endif; ?>]<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('type' => 'class','sdesc' => $this->_tpl_vars['sdesc'],'desc' => $this->_tpl_vars['desc']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div><br /><br />
<div class="top">[ <a href="#top">Top</a> ]</div><br />

<?php if (count ( $this->_tpl_vars['vars'] ) > 0): ?>
<hr />
<a name="class_vars"></a>
<h2>Class Variables</h2>
<div class="tags">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("var.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div><br />
<?php endif; ?>

<?php if (count ( $this->_tpl_vars['methods'] ) > 0): ?>
<hr />
<a name="class_methods"></a>
<h2>Class Methods</h2>
<div class="tags">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("method.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div><br />
<?php endif; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>