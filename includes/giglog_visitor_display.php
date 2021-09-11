<?php declare(strict_types=1);
function display_giglog() : string
{
    $c = new GiglogAdmin_ConcertsTable();
    return $c->render();
}

add_shortcode('getconcerts', 'display_giglog');
?>
