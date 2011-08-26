<?php

class XedExampleForm extends MailForm {
	function XedExampleForm () {
		parent::MailForm ();
		$this->parseSettings ('inc/app/xed/forms/example/settings.php');
		$this->widgets['xeditor']->setDefault ('<xt:box name="sitellite/nav/breadcrumb" title="sitellite/nav/breadcrumb" style="display: list-item; list-style-type: none; border: 1px solid black; background-color: #A9B7C4; /* #cde */ width: 90%; height: 50px; font-weight: bold; padding: 5px; margin: 5px;"></xt:box>

<h2>SpaceShipOne Captures the X Prize</h2>

<p>SpaceShipOne\'s second flight was a success, the craft successfully
launching from mothership White Knight and returning safely about 20
minutes later. If the flight is certified to have reached the X Prize\'s
target height (62.5 miles) before its safe return, it will win the $10
million purse, and more importantly attain the prestige of repeatably
(if only technically) reaching space, on a budget embarrassingly
smaller than NASA\'s. Today\'s flight was manned by 51-year-old test
pilot Brian Binnie (rather than Mike Melvill, who piloted last week\'s
trip), and according to spectators present at both launches seemed even
smoother than last week\'s flight. The view from the sidelines was
incredible. <a href="mailto:kraetzja@clarkson.edu">flapjack</a> submits
a link to <a href="http://www.cnn.com/2004/TECH/space/10/04/spaceshipone.attempt.cnn/index.html">CNN\'s
coverage of the launch</a> (which lists a claimed height attained of 368,000 feet),
noting <em>"Interesting to note that a majority of its funding
($20-$30 million) was put up by Microsoft\'s own, Paul Allen."</em>
See also the <a href="http://www.xprize.com/">official X Prize site</a>
for continuing live coverage. <strong>Update: 10/04 17:05 GMT</strong> by
<strong><a href="http://www.monkey.org/%7Etimothy/">T</a></strong>: I was
able to attend the launch; read below for my short sketch of the event.</p>

<h2>Lad loved farming</h2>
<p>AUSTIN -- Friends, family and neighbours of Jayden Martens mourned the loss yesterday of a little boy who loved living on the farm. On Friday night, six-year-old Jayden was watching his father Harvey remove a gearbox from a grain auger on the family farm just north of Austin on Springbrook Road. But when he took the gear box out, the auger collapsed on Jayden.</p>

<h2>Hoggone it</h2>
<p>Another trade battle erupted yesterday as the U.S. Commerce Department announced preliminary plans to impose duties of up to 15% on imported live Canadian hogs. Reaction on the Canadian side of the border was swift.</p>

<h2>Really Big Shows</h2>
<p>Every concert has a story. And everyone who ever took in a show at Winnipeg Arena has a favourite memory. Whether the concert was amazing or lacklustre, sounded bad or perfect, made you glad you came or made you wish stayed home, no two Arena shows were the same.</p>

<h2>Stamp of approval</h2>
<p>CALGARY -- The wonky wing, source of so much speculation the past three weeks, passed its first real test yesterday. Its owner, quarterback Khari Jones, also gets straight A\'s for every aspect of his Calgary Stampeders debut.</p>

<h2>Portage shuns sexy biz</h2>
<p>Portage la Prairie\'s city council wants to officially XXX-tinguish the prospect of XXX-rated business ventures ever setting up shop in town. "Anything to do with adult entertainment -- read: sex entertainment -- we\'re not interested in having in Portage," said Coun. Dave Quinn.</p>');
//		$this->widgets['xeditor']->setDefault ('<p>one two threee four fiev syx sevn</p><p>one two threee four fiev syx sevn</p><p>one two threee four fiev syx sevn</p>');
//		$this->widgets['xeditor']->setDefault ('<p>Quand j\'etais jeune, j\'avais un chien</p><p>qui nous visitais de loin</p><p>chaque printemps, quand il avait besoin</p><p>d\'une famille pour lui prendre soin</p>');
	}
	function onSubmit ($vals) {
		page_onload (false);
		page_onclick (false);
		page_onfocus (false);

		echo '<ul><li><a href="#rendered">Rendered HTML</a></li><li><a href="#source">HTML Source</a></li><li><a href="xed-example-form">Back</a></li></ul>';
		echo '<a name="rendered"></a><h2>Rendered HTML:</h2><div style="border: #369 1px dashed; padding: 10px; width: 600px">';
		echo $vals['xeditor'];
		echo '<br clear="all" /></div><p><a href="#top">[ top ]</a></p><a name="source"></a><h2>HTML Source:</h2><div style="border: #369 1px dashed">';
		echo '<pre>' . htmlentities ($vals['xeditor']) . '</pre></div>';
	}
}

page_title ('Xed Example Form');
$form = new XedExampleForm ();
echo $form->run ();

?>