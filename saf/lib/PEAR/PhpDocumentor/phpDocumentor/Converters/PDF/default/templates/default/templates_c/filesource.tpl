<?php /* Smarty version 2.5.0, created on 2003-07-27 20:31:21
         compiled from filesource.tpl */ ?>
<?php $this->_load_plugins(array(
array('modifier', 'rawurlencode', 'filesource.tpl', 4, false),)); ?><?php ob_start(); ?><?php echo $this->_tpl_vars['name']; ?>
|||Source code<?php $this->_smarty_vars['capture']['gindex'] = ob_get_contents(); ob_end_clean(); ?>
<newpage />
<pdffunction:addDestination arg="<?php echo $this->_tpl_vars['dest']; ?>
" arg="FitH" arg=$this->y />
<text size="26" justification="centre"><C:index:<?php echo $this->_run_mod_handler('rawurlencode', true, $this->_smarty_vars['capture']['gindex']); ?>
><C:rf:3source code: <?php echo $this->_tpl_vars['name']; ?>
>File Source for <?php echo $this->_tpl_vars['name']; ?>

</text>
<text size="12"><i>Documentation for this file is available at <?php echo $this->_tpl_vars['docs']; ?>
</i>

</text>
<font face="Courier" />
<text size="8"><?php echo $this->_tpl_vars['source']; ?>
</text>
<font face="Helvetica" />