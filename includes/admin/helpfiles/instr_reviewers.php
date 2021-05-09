<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'Instructions_Reviewers' ) ) {

    class Instructions_Reviewers {

        static function render_instr_rev_html(): void {

            ?>

            <div class="wrap">

<!-- wp:paragraph -->
<p>Click Post -&gt; Add new on the tool bar</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":812,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img class="wp-image-812" src="https://wp.eternal-terror.com/wp-content/uploads/2021/03/image.png" alt=""></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>First thing to do is to load the correct template from the post menu on the right</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":814,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img class="wp-image-814" src="https://wp.eternal-terror.com/wp-content/uploads/2021/03/image-1.png" alt=""></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>You have some basic instructions in the template that loads, but it will anyway be reviewed before publishing.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>It would be nice if you could fill as much info as possible based on the template, upload the album image (please don't upload very big files, nor very small images that don't scale well on bigger screens)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>In order to upload image, click on the image placeholder in the template - where it says Album Cover be here and use the Add Media button</p>
<!-- /wp:paragraph -->

<!-- wp:image {"id":817,"sizeSlug":"large","linkDestination":"none"} -->
<figure class="wp-block-image size-large"><img class="wp-image-817" src="https://wp.eternal-terror.com/wp-content/uploads/2021/03/image-2.png" alt=""></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Use upload files tab in the Media management and upload your image. You can just drag and drop from your PC, no need to browse to its location.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>As soon as you placed it on the screen, it gets uploaded and you get taken back to the list of all images. Your newly created image is now selected. You might like to have this sorted by your images in the drop down menu (I'll check to see if this can be default/restricted)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Just click insert into post, no need to select anything else (unless you want to properly align it to the left already)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>&nbsp;</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>After you have entered all info in the review text, please add some tags and the correct category - album, book, concert review and UNSELECT any other category that might be selected by default. Leave everything else as it is</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>When you click publish, you will be asked if you are sure you want to submit for review. You are not allowed to automatically publish the posts. An email is sent to an ET inbox warning that you have published new content. If you want to add any info about your review, please do it by mail (mainly if it is urgent)</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>&nbsp;</p>
<!-- /wp:paragraph -->
            </div>

            <?php

        }

    }

}

?>
