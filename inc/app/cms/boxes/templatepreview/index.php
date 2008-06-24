<?php

page_title ('Lorem Ipsum');
page_nav_title ('Lorem Ipsum');
page_head_title ('Lorem Ipsum');

echo '<p><strong><a href="#" onclick="window.close ()">Close Preview</a></strong></p>';
echo '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Fusce arcu libero, eleifend quis, egestas non, sodales eu, lacus. Nullam eget justo. Fusce interdum est sit amet ipsum euismod consectetuer. Fusce cursus ultricies metus. Morbi quis nunc. Mauris porttitor justo ut mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Fusce lobortis sapien ac neque. Quisque et odio. Sed ac magna. Duis nulla. Morbi gravida, lacus condimentum dignissim sagittis, mauris ipsum adipiscing velit, et tempus arcu odio in diam. Suspendisse ac quam.</p>';
echo '<p>Mauris congue mollis metus. Donec sit amet nisl et tellus tempor pretium. Maecenas sit amet dolor vitae erat blandit auctor. Donec in tellus vitae lacus accumsan convallis. Integer a tortor. Duis at nisl at metus sodales convallis. Vivamus nulla turpis, mattis et, sollicitudin lacinia, vulputate ac, orci. Pellentesque pulvinar tincidunt leo. Morbi tellus mauris, venenatis sed, accumsan sed, semper sit amet, mi. Sed ipsum. Aenean ornare rhoncus magna. Nullam eget enim.</p>';
echo '<h2>Ut Aenean</h2>';
echo '<ul><li>Ut adipiscing lectus malesuada lectus.</li><li>Aenean volutpat leo sed nunc dignissim ultricies.</li><li>Nulla aliquet rutrum arcu.</li><li>Ut porta tincidunt nulla.</li><li>Aenean varius fermentum tellus.</li></ul>';
echo '<h2>Pulvinar Tellus</h2>';
echo '<p>In pulvinar magna ac augue. In mauris tellus, rhoncus vel, eleifend vitae, tincidunt et, odio. Duis gravida libero vitae mauris. Nunc volutpat arcu sit amet leo. Mauris risus. Morbi eleifend pede sed sem. Nam id erat. Morbi dapibus dolor ac arcu. Curabitur tempor, odio non posuere porta, dolor ipsum porttitor ante, pellentesque scelerisque nisl lorem vitae dui. Sed mi. Phasellus diam wisi, viverra ut, gravida at, consectetuer et, ligula. Ut mauris neque, tempor et, rutrum in, tempus vitae, enim. Mauris porta turpis at dui. Donec vitae nulla. Pellentesque eu felis. Mauris iaculis elit vel est. Duis ultrices diam. Vivamus nonummy, metus sit amet accumsan pretium, nisl dui pellentesque mauris, eget vestibulum metus nunc eu est.</p>';

global $session;

$session->acl->roles[$session->acl->user['role']]['admin'] = false;

page_template ($parameters['tpl']);

?>