<?php
require_once __DIR__.'/bootstrap.php';
json_response(['success'=>true,'csrf'=>csrf_token()]);
