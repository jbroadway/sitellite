<?php /* Smarty version 2.5.0, created on 2003-04-23 16:55:18
         compiled from pkgelementindex.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<a name="top"></a>
<h1>Element index for package <?php echo $this->_tpl_vars['package']; ?>
</h1>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("basicindex.tpl", array('indexname' => "elementindex_".$this->_tpl_vars['package']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>