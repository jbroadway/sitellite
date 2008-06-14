<?php /* Smarty version 2.5.0, created on 2003-07-27 19:40:57
         compiled from toc.tpl */ ?>
<?php $this->_load_plugins(array(
array('function', 'assign', 'toc.tpl', 6, false),)); ?><pdffunction:ezStopPageNumbers arg="1" arg="1" />
<pdffunction:ezInsertMode arg="1" arg="1" arg="after" />
<newpage />
<text size="26" justification="centre">Contents
</text>
<?php echo smarty_function_assign(array('var' => 'xpos','value' => '520'), $this) ; ?>

<?php if (count((array)$this->_tpl_vars['contents'])):
    foreach ((array)$this->_tpl_vars['contents'] as $this->_tpl_vars['k'] => $this->_tpl_vars['v']):
?>
<?php if ($this->_tpl_vars['v'][2] == '1'): ?>
<text size="16" aright="<?php echo $this->_tpl_vars['xpos']; ?>
"><c:ilink:toc<?php echo $this->_tpl_vars['k']; ?>
><?php echo $this->_tpl_vars['v'][0]; ?>
</c:ilink><C:dots:3<?php echo $this->_tpl_vars['v'][1]; ?>
></text>
<?php elseif ($this->_tpl_vars['v'][2] == '2'): ?>
<text size="12" aright="<?php echo $this->_tpl_vars['xpos']; ?>
" left="30"><c:ilink:toc<?php echo $this->_tpl_vars['k']; ?>
><?php echo $this->_tpl_vars['v'][0]; ?>
</c:ilink><C:dots:3<?php echo $this->_tpl_vars['v'][1]; ?>
></text>
<?php elseif ($this->_tpl_vars['v'][2] == '3'): ?>
<text size="12" aright="<?php echo $this->_tpl_vars['xpos']; ?>
" left="40"><c:ilink:toc<?php echo $this->_tpl_vars['k']; ?>
><?php echo $this->_tpl_vars['v'][0]; ?>
</c:ilink><C:dots:3<?php echo $this->_tpl_vars['v'][1]; ?>
></text>
<?php endif; ?>
<?php endforeach; endif; ?>