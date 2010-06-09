<?php

$data = array (
	'body' => '<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Ut enim. In dictum. Curabitur fermentum. Nullam neque diam, bibendum at, semper vel, euismod vitae, erat. Sed velit neque, iaculis id, tincidunt et, rhoncus at, sapien. Curabitur ultricies. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Vestibulum dictum bibendum neque. Aliquam erat volutpat. Pellentesque accumsan suscipit lectus. Nullam mi ipsum, dictum eu, auctor ac, sagittis nec, wisi. Praesent pharetra tortor vel metus. Vivamus et sapien vitae turpis vestibulum luctus. Phasellus aliquet risus id felis. Praesent vel metus. Fusce faucibus nisl vitae ante euismod pharetra.</p><p>In hac habitasse platea dictumst. Suspendisse potenti. Duis viverra dui. Praesent scelerisque. Proin eget est sed urna tempor vehicula. Praesent eget magna at tellus bibendum consequat. Curabitur dignissim. Donec pulvinar interdum ante. Cras semper vehicula justo. Integer nec sem eu orci rutrum vehicula. Aliquam gravida. Sed aliquam arcu a nibh. Aenean eu mauris non nulla convallis ultrices.</p>',
	'date' => date ('F j, Y'),
	'email' => 'joe@example.com',
	'firstname' => 'Joe',
	'fullname' => 'Joe Smith',
	'lastname' => 'Smith',
	'organization' => 'Example Inc.',
	'tracker' => '',
	'unsubscribe' => site_prefix () . '/index/sitemailer2-unsubscribe-action?email=joe@example.com',
	'website' => 'http://www.example.com/',
);

$parameters['body'] = str_replace ('{body}', '{filter none}{body}{end filter}', $parameters['body']);

page_title ('SiteMailer 2 - Preview Template');

echo template_simple (
	'preview_template.spt',
	array ('body' => template_simple ($parameters['body'], $data))
);

?>