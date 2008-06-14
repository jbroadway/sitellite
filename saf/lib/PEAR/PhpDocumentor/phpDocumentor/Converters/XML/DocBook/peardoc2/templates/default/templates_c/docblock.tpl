<?php /* Smarty version 2.5.0, created on 2003-07-25 22:04:28
         compiled from docblock.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'docblock.tpl', 2, false),
array('modifier', 'default', 'docblock.tpl', 15, false),)); ?><?php if ($this->_tpl_vars['var']): ?>
<?php echo smarty_function_assign(array('var' => 'num','value' => 'refsect3'), $this) ; ?>

<?php else: ?>
<?php echo smarty_function_assign(array('var' => 'num','value' => 'refsect1'), $this) ; ?>

<?php endif; ?>
    <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.desc">
     &title.desc;
<?php if ($this->_tpl_vars['line_number']): ?>
     <simpara>
      Source on line #: <?php if ($this->_tpl_vars['class_slink']): ?><?php echo $this->_tpl_vars['class_slink']; ?>
<?php else: ?><?php echo $this->_tpl_vars['line_number']; ?>
<?php endif; ?>
     </simpara>
<?php endif; ?>
<?php if ($this->_tpl_vars['var']): ?>
     <simpara>
      <?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['sdesc'], "&notdocumented;"); ?>

     </simpara>
<?php endif; ?>
<?php if ($this->_tpl_vars['desc']): ?>
     <?php echo $this->_tpl_vars['desc']; ?>

<?php else: ?>
<?php if ($this->_tpl_vars['var'] && $this->_tpl_vars['sdesc']): ?>
<?php else: ?>
     &notdocumented;
<?php endif; ?>
<?php endif; ?>
    </<?php echo $this->_tpl_vars['num']; ?>
>
<?php if ($this->_tpl_vars['params']): ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.param">
    &title.param;
    <para>
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
     <variablelist>
      <varlistentry>
       <term>
        <?php echo smarty_function_assign(array('var' => 'temp','value' => $this->_tpl_vars['params'][$this->_sections['params']['index']]['name']), $this) ; ?>

        <?php if (strpos ( $this->_tpl_vars['params'][$this->_sections['params']['index']]['type'] , '|' ) || strpos ( $this->_tpl_vars['cparams'][$this->_tpl_vars['temp']]['cdatatype'] , '>' )): ?>
        <type><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['type']; ?>
</type>
        <?php else: ?>
        <?php if ($this->_tpl_vars['params'][$this->_sections['params']['index']]['type'] == 'integer'): ?>
        <?php echo smarty_function_assign(array('var' => 'paramtype','value' => 'int'), $this) ; ?>

        <?php elseif ($this->_tpl_vars['params'][$this->_sections['params']['index']]['type'] == 'boolean'): ?>
        <?php echo smarty_function_assign(array('var' => 'paramtype','value' => 'bool'), $this) ; ?>

        <?php else: ?>
        <?php echo smarty_function_assign(array('var' => 'paramtype','value' => $this->_tpl_vars['params'][$this->_sections['params']['index']]['type']), $this) ; ?>

        <?php endif; ?>
        &type.<?php echo $this->_tpl_vars['paramtype']; ?>
;
        <?php endif; ?>
         <parameter><?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['name']; ?>
</parameter>
       </term>
       <listitem>
        <para>
         <?php echo $this->_tpl_vars['params'][$this->_sections['params']['index']]['description']; ?>

        </para>
       </listitem>
      </varlistentry>     
     </variablelist>
<?php endfor; endif; ?>
    </para>
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php endif; ?>
<?php if (count((array)$this->_tpl_vars['tags'])):
    foreach ((array)$this->_tpl_vars['tags'] as $this->_tpl_vars['tagname'] => $this->_tpl_vars['tag']):
?>
<?php if ($this->_tpl_vars['tagname'] != 'static' && $this->_tpl_vars['tagname'] != 'author' && $this->_tpl_vars['tagname'] != 'version' && $this->_tpl_vars['tagname'] != 'copyright' && $this->_tpl_vars['tagname'] != 'package' && $this->_tpl_vars['tagname'] != 'subpackage'): ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.<?php echo $this->_tpl_vars['tagname']; ?>
">
    &title.<?php echo $this->_tpl_vars['tagname']; ?>
;
    <?php if (isset($this->_sections['t'])) unset($this->_sections['t']);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['loop'] = is_array($this->_tpl_vars['tag']) ? count($this->_tpl_vars['tag']) : max(0, (int)$this->_tpl_vars['tag']);
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
    <para>
      <emphasis><?php echo $this->_tpl_vars['tag'][$this->_sections['t']['index']]['keyword']; ?>
</emphasis> <?php echo $this->_tpl_vars['tag'][$this->_sections['t']['index']]['data']; ?>

    </para>
    <?php endfor; endif; ?>
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php elseif ($this->_tpl_vars['tagname'] == 'deprecated'): ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.<?php echo $this->_tpl_vars['tagname']; ?>
">
    &title.note;
    &note.deprecated;
    <?php if (isset($this->_sections['t'])) unset($this->_sections['t']);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['loop'] = is_array($this->_tpl_vars['tag']) ? count($this->_tpl_vars['tag']) : max(0, (int)$this->_tpl_vars['tag']);
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
    <para>
      <?php echo $this->_tpl_vars['tag'][$this->_sections['t']['index']]['data']; ?>

    </para>
    <?php endfor; endif; ?>
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php elseif ($this->_tpl_vars['tags'][$this->_sections['tags']['index']]['keyword'] == 'static'): ?>
<?php echo smarty_function_assign(array('var' => 'canstatic','value' => true), $this) ; ?>

<?php elseif ($this->_tpl_vars['tagname'] != 'package' && $this->_tpl_vars['tagname'] != 'subpackage'): ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.<?php echo $this->_tpl_vars['tagname']; ?>
">
    <title><?php echo $this->_tpl_vars['tagname']; ?>
</title>
    <?php if (isset($this->_sections['t'])) unset($this->_sections['t']);
$this->_sections['t']['name'] = 't';
$this->_sections['t']['loop'] = is_array($this->_tpl_vars['tag']) ? count($this->_tpl_vars['tag']) : max(0, (int)$this->_tpl_vars['tag']);
$this->_sections['t']['show'] = true;
$this->_sections['t']['max'] = $this->_sections['t']['loop'];
$this->_sections['t']['step'] = 1;
$this->_sections['t']['start'] = $this->_sections['t']['step'] > 0 ? 0 : $this->_sections['t']['loop']-1;
if ($this->_sections['t']['show']) {
    $this->_sections['t']['total'] = $this->_sections['t']['loop'];
    if ($this->_sections['t']['total'] == 0)
        $this->_sections['t']['show'] = false;
} else
    $this->_sections['t']['total'] = 0;
if ($this->_sections['t']['show']):

            for ($this->_sections['t']['index'] = $this->_sections['t']['start'], $this->_sections['t']['iteration'] = 1;
                 $this->_sections['t']['iteration'] <= $this->_sections['t']['total'];
                 $this->_sections['t']['index'] += $this->_sections['t']['step'], $this->_sections['t']['iteration']++):
$this->_sections['t']['rownum'] = $this->_sections['t']['iteration'];
$this->_sections['t']['index_prev'] = $this->_sections['t']['index'] - $this->_sections['t']['step'];
$this->_sections['t']['index_next'] = $this->_sections['t']['index'] + $this->_sections['t']['step'];
$this->_sections['t']['first']      = ($this->_sections['t']['iteration'] == 1);
$this->_sections['t']['last']       = ($this->_sections['t']['iteration'] == $this->_sections['t']['total']);
?>
    <para>
      <emphasis><?php echo $this->_tpl_vars['tagname']; ?>
</emphasis> <?php echo $this->_tpl_vars['tag'][$this->_sections['t']['index']]['data']; ?>

    </para>
    <?php endfor; endif; ?>
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php endif; ?>
<?php endforeach; endif; ?>
<?php if ($this->_tpl_vars['canstatic']): ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.note">
    &title.note;
    &note.canstatic;    
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php else: ?>
   <<?php echo $this->_tpl_vars['num']; ?>
 id="<?php echo $this->_tpl_vars['id']; ?>
.note">
    &title.note;
    &note.notstatic;
   </<?php echo $this->_tpl_vars['num']; ?>
>
<?php endif; ?>