<?php

	file_put_contents(__DIR__.'/.log.txt',$_SERVER['REQUEST_URI'].': '.print_r($_REQUEST,1),FILE_APPEND);
