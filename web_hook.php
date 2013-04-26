<?php
exec('/usr/bin/perl /usr/local/bin/git/git_pull.pl -r farm', $output);
print_r($output);

?>
