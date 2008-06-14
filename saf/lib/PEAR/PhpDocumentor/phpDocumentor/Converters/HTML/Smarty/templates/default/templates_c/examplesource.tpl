<?php /* Smarty version 2.5.0, created on 2003-04-28 19:43:07
         compiled from examplesource.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('title' => $this->_tpl_vars['title']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<h1 align="center"><?php echo $this->_tpl_vars['title']; ?>
</h1>
<?php echo $this->_tpl_vars['source']; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>