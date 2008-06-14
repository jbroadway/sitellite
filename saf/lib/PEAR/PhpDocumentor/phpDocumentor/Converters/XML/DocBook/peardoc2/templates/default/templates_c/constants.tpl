<?php /* Smarty version 2.5.0, created on 2003-08-08 13:01:04
         compiled from constants.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'constants.tpl', 17, false),)); ?><refentry id="<?php echo $this->_tpl_vars['id']; ?>
">
   <refnamediv>
    <refname>Package <?php echo $this->_tpl_vars['package']; ?>
 Constants</refname>
    <refpurpose>Constants defined in and used by <?php echo $this->_tpl_vars['package']; ?>
</refpurpose>
   </refnamediv>
   <refsect1 id="<?php echo $this->_tpl_vars['id']; ?>
.details">
    <title>All Constants</title>
<?php if (isset($this->_sections['files'])) unset($this->_sections['files']);
$this->_sections['files']['name'] = 'files';
$this->_sections['files']['loop'] = is_array($this->_tpl_vars['defines']) ? count($this->_tpl_vars['defines']) : max(0, (int)$this->_tpl_vars['defines']);
$this->_sections['files']['show'] = true;
$this->_sections['files']['max'] = $this->_sections['files']['loop'];
$this->_sections['files']['step'] = 1;
$this->_sections['files']['start'] = $this->_sections['files']['step'] > 0 ? 0 : $this->_sections['files']['loop']-1;
if ($this->_sections['files']['show']) {
    $this->_sections['files']['total'] = $this->_sections['files']['loop'];
    if ($this->_sections['files']['total'] == 0)
        $this->_sections['files']['show'] = false;
} else
    $this->_sections['files']['total'] = 0;
if ($this->_sections['files']['show']):

            for ($this->_sections['files']['index'] = $this->_sections['files']['start'], $this->_sections['files']['iteration'] = 1;
                 $this->_sections['files']['iteration'] <= $this->_sections['files']['total'];
                 $this->_sections['files']['index'] += $this->_sections['files']['step'], $this->_sections['files']['iteration']++):
$this->_sections['files']['rownum'] = $this->_sections['files']['iteration'];
$this->_sections['files']['index_prev'] = $this->_sections['files']['index'] - $this->_sections['files']['step'];
$this->_sections['files']['index_next'] = $this->_sections['files']['index'] + $this->_sections['files']['step'];
$this->_sections['files']['first']      = ($this->_sections['files']['iteration'] == 1);
$this->_sections['files']['last']       = ($this->_sections['files']['iteration'] == $this->_sections['files']['total']);
?>
    <refsect2 id="<?php echo $this->_tpl_vars['id']; ?>
.details.<?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['page']; ?>
">
     <title>
      Constants defined in <?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['name']; ?>

     </title>
     <para>
      <table>
       <title>Constants defined in <?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['name']; ?>
</title>
<?php if (isset($this->_sections['d'])) unset($this->_sections['d']);
$this->_sections['d']['name'] = 'd';
$this->_sections['d']['loop'] = is_array($this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']) ? count($this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']) : max(0, (int)$this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']);
$this->_sections['d']['show'] = true;
$this->_sections['d']['max'] = $this->_sections['d']['loop'];
$this->_sections['d']['step'] = 1;
$this->_sections['d']['start'] = $this->_sections['d']['step'] > 0 ? 0 : $this->_sections['d']['loop']-1;
if ($this->_sections['d']['show']) {
    $this->_sections['d']['total'] = $this->_sections['d']['loop'];
    if ($this->_sections['d']['total'] == 0)
        $this->_sections['d']['show'] = false;
} else
    $this->_sections['d']['total'] = 0;
if ($this->_sections['d']['show']):

            for ($this->_sections['d']['index'] = $this->_sections['d']['start'], $this->_sections['d']['iteration'] = 1;
                 $this->_sections['d']['iteration'] <= $this->_sections['d']['total'];
                 $this->_sections['d']['index'] += $this->_sections['d']['step'], $this->_sections['d']['iteration']++):
$this->_sections['d']['rownum'] = $this->_sections['d']['iteration'];
$this->_sections['d']['index_prev'] = $this->_sections['d']['index'] - $this->_sections['d']['step'];
$this->_sections['d']['index_next'] = $this->_sections['d']['index'] + $this->_sections['d']['step'];
$this->_sections['d']['first']      = ($this->_sections['d']['iteration'] == 1);
$this->_sections['d']['last']       = ($this->_sections['d']['iteration'] == $this->_sections['d']['total']);
?>
<?php if ($this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines'][$this->_sections['d']['index']]['conflicts']): ?><?php echo smarty_function_assign(array('var' => 'defineconflict','value' => true), $this) ; ?>
<?php endif; ?>
<?php endfor; endif; ?>
       <tgroup cols="<?php if ($this->_tpl_vars['defineconflict']): ?>4<?php else: ?>3<?php endif; ?>">
        <thead>
         <row>
          <entry>Name</entry>
          <entry>Value</entry>
          <entry>Line Number</entry>
<?php if ($this->_tpl_vars['defineconflict']): ?>
          <entry>Conflicts with other packages</entry>
<?php endif; ?>
 	     </row>
        </thead>
        <tbody>
<?php if (isset($this->_sections['d'])) unset($this->_sections['d']);
$this->_sections['d']['name'] = 'd';
$this->_sections['d']['loop'] = is_array($this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']) ? count($this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']) : max(0, (int)$this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines']);
$this->_sections['d']['show'] = true;
$this->_sections['d']['max'] = $this->_sections['d']['loop'];
$this->_sections['d']['step'] = 1;
$this->_sections['d']['start'] = $this->_sections['d']['step'] > 0 ? 0 : $this->_sections['d']['loop']-1;
if ($this->_sections['d']['show']) {
    $this->_sections['d']['total'] = $this->_sections['d']['loop'];
    if ($this->_sections['d']['total'] == 0)
        $this->_sections['d']['show'] = false;
} else
    $this->_sections['d']['total'] = 0;
if ($this->_sections['d']['show']):

            for ($this->_sections['d']['index'] = $this->_sections['d']['start'], $this->_sections['d']['iteration'] = 1;
                 $this->_sections['d']['iteration'] <= $this->_sections['d']['total'];
                 $this->_sections['d']['index'] += $this->_sections['d']['step'], $this->_sections['d']['iteration']++):
$this->_sections['d']['rownum'] = $this->_sections['d']['iteration'];
$this->_sections['d']['index_prev'] = $this->_sections['d']['index'] - $this->_sections['d']['step'];
$this->_sections['d']['index_next'] = $this->_sections['d']['index'] + $this->_sections['d']['step'];
$this->_sections['d']['first']      = ($this->_sections['d']['iteration'] == 1);
$this->_sections['d']['last']       = ($this->_sections['d']['iteration'] == $this->_sections['d']['total']);
?>
         <row>
          <entry><?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines'][$this->_sections['d']['index']]['name']; ?>
</entry>
          <entry><?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines'][$this->_sections['d']['index']]['value']; ?>
</entry>
          <entry><?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines'][$this->_sections['d']['index']]['line_number']; ?>
</entry>
<?php if ($this->_tpl_vars['defineconflict']): ?>
          <entry><?php echo $this->_tpl_vars['defines'][$this->_sections['files']['index']]['defines'][$this->_sections['d']['index']]['conflicts']; ?>
</entry>
<?php endif; ?>
         </row>
<?php endfor; endif; ?>
        </tbody>
       </tgroup>
      </table>
     </para>
    </refsect2>
<?php endfor; endif; ?>
 </refsect1>
</refentry>
<!-- Generated by phpDocumentor v <?php echo $this->_tpl_vars['phpdocversion']; ?>
 <?php echo $this->_tpl_vars['phpdocwebsite']; ?>
 -->
<!-- Keep this comment at the end of the file
Local variables:
mode: sgml
sgml-omittag:t
sgml-shorttag:t
sgml-minimize-attributes:nil
sgml-always-quote-attributes:t
sgml-indent-step:1
sgml-indent-data:t
sgml-parent-document:nil
sgml-default-dtd-file:"../../../../manual.ced"
sgml-exposed-tags:nil
sgml-local-catalogs:nil
sgml-local-ecat-files:nil
End:
vim600: syn=xml fen fdm=syntax fdl=2 si
vim: et tw=78 syn=sgml
vi: ts=1 sw=1
-->