<pre>
<?php

require 'classes/Updater.php';
require 'classes/Api.php';

$t = new Updater(new Api);
$t->run();

?>

</pre>