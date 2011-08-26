<?php

// standard form
// upload functionality 

class ExampleThumbnail1Form extends MailForm {

    function ExampleThumbnail1Form () {
        parent::MailForm ();

        // create form widgets here
        $this->parseSettings ('inc/app/example/forms/thumbnail1/settings.php');

        echo "<strong>ATTENTION: IE caches the images.</strong>";
    }

    function onSubmit ($vals) {
        // handle form action here
        // image uploaden en in juiste map zetten
        $fname = "upload.jpg";
        $vals['image1']->move ('inc/app/example/data', $fname);
        // make sure directory has right (write) permission. CHMOD.

        // "image" class includen
        loader_import("saf.Ext.thumbnail");

        // get settings here

        // thumbnail maken
        $orig_filename = 'inc/app/example/data/upload.jpg';
        $thumbnail = 'inc/app/example/data/thumbnail.jpg';

        // get conf. parameters (settings.ini.php)
        $max_width = appconf("thumb_width");
        $max_height = appconf("thumb_height");
        makethumbnail($orig_filename , $thumbnail , $max_width , $max_height );

        // watermark maken (tekst)
        $extra = array();
        $extra['image_text'] = "example.nl";
        $extra['image_text_color'] = "#888888";
        $extra['image_text_background'] = "#FFFFFF";
        $extra['image_text_background_percent'] = 70;
        $extra['image_text_padding_x'] = 5;
        $extra['image_text_padding_y'] = 5;

        $orig_filename = 'inc/app/example/data/upload.jpg';
        $thumbnail = 'inc/app/example/data/watermark.jpg';
        makethumbnail($orig_filename , $thumbnail , 9999 , 9999 , $extra );

        page_title ('example IMAGE handling');

        echo '<p>From submission OK. Thank you!</p>';

        echo "<p>Thumbnail</p>";
        echo "<p><img src='/inc/app/example/data/thumbnail.jpg'></p>";

        echo "<p>Watermark</p>";
        echo "<p><img src='/inc/app/example/data/watermark.jpg'></p>";

        echo "<p>UPLOADED IMAGE (original)</p>";
        echo "<p><img src='/inc/app/example/data/upload.jpg'></p>";

        echo '<p><a href="/index/example-thumbnail1-form">Try again</a></p>';
    }
}

?>