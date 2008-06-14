<?php /* Smarty version 2.5.0, created on 2003-08-13 11:27:58
         compiled from method.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'default', 'method.tpl', 5, false),
array('modifier', 'replace', 'method.tpl', 17, false),)); ?><!-- $Revision: 1.1.1.1 $ -->
<refentry id="<?php echo $this->_tpl_vars['id']; ?>
">
   <refnamediv>
    <refname><?php if ($this->_tpl_vars['function_call']['constructor']): ?>constructor <?php endif; ?><function><?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['function_name']; ?>
</function></refname>
    <refpurpose><?php echo $this->_run_mod_handler('default', true, @$this->_tpl_vars['sdesc'], @$this->_tpl_vars['function_name']); ?>
</refpurpose>
   </refnamediv>
   <refsynopsisdiv>
    <funcsynopsis>
     <funcsynopsisinfo>
      require_once &apos;<?php echo $this->_tpl_vars['source_location']; ?>
&apos;;
     </funcsynopsisinfo>
     <funcprototype>
      <funcdef><?php echo $this->_tpl_vars['function_return']; ?>
<?php if ($this->_tpl_vars['function_call']['returnsref']): ?>&amp;<?php endif; ?>
      <?php if ($this->_tpl_vars['function_call']['constructor']): ?>constructor <?php endif; ?><function><?php echo $this->_tpl_vars['class']; ?>
::<?php echo $this->_tpl_vars['function_name']; ?>
</function></funcdef>
<?php if (count ( $this->_tpl_vars['function_call']['params'] )): ?>
<?php if (isset($this->_sections['params'])) unset($this->_sections['params']);
$this->_sections['params']['name'] = 'params';
$this->_sections['params']['loop'] = is_array($this->_tpl_vars['function_call']['params']) ? count($this->_tpl_vars['function_call']['params']) : max(0, (int)$this->_tpl_vars['function_call']['params']);
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
      <paramdef><?php if (strpos ( '>' , $this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['type'] )): ?><replaceable><?php endif; ?><?php echo $this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['type']; ?>
<?php if (strpos ( '>' , $this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['type'] )): ?></replaceable><?php endif; ?> <parameter><?php if ($this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['default'] != ''): ?> <optional><?php endif; ?><?php echo $this->_run_mod_handler('replace', true, $this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['name'], "&", "&amp;"); ?>
<?php if ($this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['default']): ?> = <?php echo $this->_tpl_vars['function_call']['params'][$this->_sections['params']['index']]['default']; ?>
</optional><?php endif; ?></parameter></paramdef>
<?php endfor; endif; ?>
<?php else: ?>
<paramdef></paramdef>
<?php endif; ?>
     </funcprototype>
    </funcsynopsis>
    </refsynopsisdiv>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('cparams' => $this->_tpl_vars['params'],'params' => $this->_tpl_vars['function_call']['params'],'desc' => $this->_tpl_vars['desc'],'tags' => $this->_tpl_vars['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</refentry>
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
