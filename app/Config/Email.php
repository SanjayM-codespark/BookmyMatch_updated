<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Email extends BaseConfig
{
    public $fromEmail  = 'noreply.bookmymatch@gmail.com';
    public $fromName   = 'BookMyMatch';
    public $recipients = '';

    public $userAgent = 'CodeIgniter';
    public $protocol = 'smtp';
    public $mailPath = '/usr/sbin/sendmail';
    public $SMTPHost = 'smtp.gmail.com';
    public $SMTPUser = 'bookmymatch123@gmail.com';
    public $SMTPPass = 'ttcifnpzlxdffuio';
    public $SMTPPort = 587;
    public $SMTPTimeout = 60;
    public $SMTPKeepAlive = false;
    public $SMTPCrypto = 'tls';
    public $wordWrap = true;
    public $wrapChars = 76;
    public $mailType = 'html';
    public $charset = 'UTF-8';
    public $validate = false;
    public $priority = 3;
    public $CRLF = "\r\n";
    public $newline = "\r\n";
    public $BCCBatchMode = false;
    public $BCCBatchSize = 200;
    public $DSN = false;
}
