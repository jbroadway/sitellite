<?php /* Smarty version 2.5.0, created on 2003-04-23 16:13:42
         compiled from page.tpl */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("header.tpl", array('top3' => true));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<h2>File: <?php echo $this->_tpl_vars['source_location']; ?>
</h2>
<div class="tab-pane" id="tabPane1">
<script type="text/javascript">
tp1 = new WebFXTabPane( document.getElementById( "tabPane1" ) );
</script>

<div class="tab-page" id="Description">
<h2 class="tab">Description</h2>
<?php if ($this->_tpl_vars['tutorial']): ?>
<div class="maintutorial">Main Tutorial: <?php echo $this->_tpl_vars['tutorial']; ?>
</div>
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("docblock.tpl", array('desc' => $this->_tpl_vars['desc'],'sdesc' => $this->_tpl_vars['sdesc'],'tags' => $this->_tpl_vars['tags']));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- =========== Used Classes =========== -->
<A NAME='classes_summary'><!-- --></A>
<h3>Classes defined in this file</h3>

<TABLE CELLPADDING='3' CELLSPACING='0' WIDTH='100%' CLASS="border">
	<THEAD>
		<TR><TD STYLE="width:20%"><h4>CLASS NAME</h4></TD><TD STYLE="width: 80%"><h4>DESCRIPTION</h4></TD></TR>
	</THEAD>
	<TBODY>
		<?php if (isset($this->_sections['classes'])) unset($this->_sections['classes']);
$this->_sections['classes']['name'] = 'classes';
$this->_sections['classes']['loop'] = is_array($this->_tpl_vars['classes']) ? count($this->_tpl_vars['classes']) : max(0, (int)$this->_tpl_vars['classes']);
$this->_sections['classes']['show'] = true;
$this->_sections['classes']['max'] = $this->_sections['classes']['loop'];
$this->_sections['classes']['step'] = 1;
$this->_sections['classes']['start'] = $this->_sections['classes']['step'] > 0 ? 0 : $this->_sections['classes']['loop']-1;
if ($this->_sections['classes']['show']) {
    $this->_sections['classes']['total'] = $this->_sections['classes']['loop'];
    if ($this->_sections['classes']['total'] == 0)
        $this->_sections['classes']['show'] = false;
} else
    $this->_sections['classes']['total'] = 0;
if ($this->_sections['classes']['show']):

            for ($this->_sections['classes']['index'] = $this->_sections['classes']['start'], $this->_sections['classes']['iteration'] = 1;
                 $this->_sections['classes']['iteration'] <= $this->_sections['classes']['total'];
                 $this->_sections['classes']['index'] += $this->_sections['classes']['step'], $this->_sections['classes']['iteration']++):
$this->_sections['classes']['rownum'] = $this->_sections['classes']['iteration'];
$this->_sections['classes']['index_prev'] = $this->_sections['classes']['index'] - $this->_sections['classes']['step'];
$this->_sections['classes']['index_next'] = $this->_sections['classes']['index'] + $this->_sections['classes']['step'];
$this->_sections['classes']['first']      = ($this->_sections['classes']['iteration'] == 1);
$this->_sections['classes']['last']       = ($this->_sections['classes']['iteration'] == $this->_sections['classes']['total']);
?>
		<TR BGCOLOR='white' CLASS='TableRowColor'>
			<TD><?php echo $this->_tpl_vars['classes'][$this->_sections['classes']['index']]['link']; ?>
</TD>
			<TD><?php echo $this->_tpl_vars['classes'][$this->_sections['classes']['index']]['sdesc']; ?>
</TD>
		</TR>
		<?php endfor; endif; ?>
	</TBODY>
</TABLE>
</div>
<script type="text/javascript">tp1.addTabPage( document.getElementById( "Description" ) );</script>
<div class="tab-page" id="tabPage1">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("include.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div class="tab-page" id="tabPage2">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("global.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div class="tab-page" id="tabPage3">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("define.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<div class="tab-page" id="tabPage4">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("function.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
</div>
<script type="text/javascript">
//<![CDATA[

setupAllTabs();

//]]>
</script>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include("footer.tpl", array());
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>