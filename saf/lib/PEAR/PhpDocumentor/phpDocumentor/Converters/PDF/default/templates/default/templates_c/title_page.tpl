<?php /* Smarty version 2.5.0, created on 2003-04-23 18:09:43
         compiled from title_page.tpl */ ?>
<pdffunction:ezSetDy arg="-100" />
<text size="30" justification="centre"><b><?php echo $this->_tpl_vars['title']; ?>
</b></text>
<pdffunction:ezSetDy arg="-150" />
<?php if ($this->_tpl_vars['logo']): ?>
<pdffunction:getYPlusOffset return="newy" offset="0" />
<pdffunction:addJpegFromFile arg="<?php echo $this->_tpl_vars['logo']; ?>
" x="250" y=$newy />
<?php endif; ?>