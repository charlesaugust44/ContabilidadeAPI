<?php

function encrypt_password($password)
{
    return hash('sha256', sha1(md5($password)));
}