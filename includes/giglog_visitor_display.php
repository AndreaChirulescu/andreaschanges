<?php declare(strict_types=1);
function display_giglog()
{
        require_once __DIR__ . '/admin/views/_concerts_table.php';
		require_once __DIR__ . '/view-helpers/select_field.php';
		require_once __DIR__ . '/venue.php';
		require_once __DIR__ . '/concert.php';
        $c = new GiglogAdmin_ConcertsTable();
        $html = $c->render();
		return ($html);
}

add_shortcode('getconcerts', 'display_giglog');
?>
