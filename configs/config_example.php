#<?php
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Ronald van Raaij <git_ronald@vanraay.org>
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Michael Keck <https://github.com/mkkeck>
 */

$placeholder_sshpubkey = "---- BEGIN SSH2 PUBLIC KEY ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDwsywGDE9YvkbwWwSNTUwEF0dNsASqimstuO+VIZgggNRs7B+MdoIlf/uXY+9lUGWzDv9OLmp+4WjcKK2vfz65R4Iv7PgUH3d7Fr1iUfOJ50qE/Sr3aKw4oeZ7Q3pkzyU2m8es+EAbzROtF/pjaPwsUXhdNnPBtWkaW6AOXrqpw6Fw+jPdLqUn0DrWnCcDnbyNL3iwL6RnkTMoHAHlk0l+Ns+zXC0fUEa2Yz3zwl3r0bWbCM/z6WoY5KpMZ2ZGKjKzhW9FiyHgOR1CepTqolFYjTIWPEsCZqmNuD0kFuV9Y4ohtPfqknnEeqJhwogLaGoQHecqtIh8m7uw1VMZ0fOj ---- END SSH2 PUBLIC KEY ----";

$cfg = array();

/**
 * Login data
 *
 * Important: Please change this values on
 *            live systems!
 */
$cfg['login'] = array(
  /* Username. Please use any username you want */
  'username' => 'admin',
  /* Password. CHANGE IT and use secure password! */
  'password' => '@Admin2023',
  /* Blowfish secret key (22 chars). CHANGE IT! */
  'blowfish' => 'XBu5pjOTa8H7UIwYSzMZxD'
);

/**
 * Force SSL usage
 *
 * Important: You should change this to true on live systems or configure
 *            your webserver to use SSL!
 */
$cfg['force_ssl'] = false;

$cfg['table_users'] = "users";
$cfg['field_userid'] = "userid";
$cfg['field_id'] = "users_pk";
$cfg['field_uid'] = "uid";
$cfg['field_ugid'] = "ugid";
$cfg['field_passwd'] = "passwd";
$cfg['field_expiration'] = "expiration";
$cfg['field_homedir'] = "homedir";
$cfg['field_shell'] = "shell";
$cfg['field_sshpubkey'] = "sshpubkey";
$cfg['field_title'] = "title";
$cfg['field_name'] = "name";
$cfg['field_company'] = "company";
$cfg['field_email'] = "email";
$cfg['field_comment'] = "comment";
$cfg['field_disabled'] = "disabled";
$cfg['field_login_count'] = "login_count";
$cfg['field_last_login'] = "last_login";
$cfg['field_last_modified'] = "last_modified";
$cfg['field_expiration'] = "expiration";
$cfg['field_bytes_in_used'] = "bytes_in_used";
$cfg['field_bytes_out_used'] = "bytes_out_used";
$cfg['field_files_in_used'] = "files_in_used";
$cfg['field_files_out_used'] = "files_out_used";

$cfg['table_groups'] = "groups";
$cfg['field_groupname'] = "groupname";
$cfg['field_gid'] = "gid";
$cfg['field_members'] = "members";
// This version collects groups and users from the linus environment
//the view is a union so not accidentally (or on purpose) corresponding users can be assigned UserIDs or group membership that should not happen
$cfg['view_user_check'] = "check_users";
$cfg['view_group_check'] = "check_groups";




$cfg['default_uid'] = "1001"; //if empty next incremental will be default
$cfg['default_homedir'] = "/srv/ftp";
// Use either SHA1 or MD5 or any other supported by your MySQL-Server and ProFTPd
// "pbkdf2" is supported if you are using ProFTPd 1.3.5.
// "crypt" uses the unix crypt() function.
// "OpenSSL:sha1" other digest-names also possible; see: http://www.proftpd.org/docs/directives/configuration_full.html#SQLAUTHTYPES
$cfg['passwd_encryption'] = "pbkdf2";
$cfg['min_passwd_length'] = "10";
$cfg['max_userid_length'] = "64";
$cfg['max_groupname_length'] = "32";
// the expressions used to validate user and groupnames are used in two places
// on the website (HTML5) and on the server (PHP)
// the HTML5 validation doesn't understand the i modifier so you need to specify lowercase and uppercase characters
// for some reason the PHP validation still needs the i modifier so just leave it in
$cfg['userid_regex']    = "/^([a-zA-Z][a-zA-Z0-9_\-]{0,".($cfg['max_userid_length']-1)."})$/i"; //every username must comply with this regex
$cfg['groupname_regex'] = "/^([a-zA-Z][a-zA-Z0-9_\-]{0,".($cfg['max_groupname_length']-1)."})$/i"; //every username must comply with this regex
// Set any of these to -1 to remove the constraint
$cfg['min_uid'] = 1001;
$cfg['max_uid'] = 65534;
$cfg['min_gid'] = 1001;
$cfg['max_gid'] = 65534;
// Uncomment this to read crypt() settings from login.defs.
$cfg['read_login_defs'] = true;

// next option activates a userid filter on users.php. Usefull if you want to manage a lot of users
// that have a prefix like "pre-username", the first occurence of separator is recognized only!
$cfg['userid_filter_separator'] = ""; // try "-" or "_" as separators

// use this block for a mysql backend
/*$cfg['db_type'] = "mysqli"; // if unset, 'db_type' defaults to mysqli
$cfg['db_host'] = "localhost";
$cfg['db_name'] = "database";
$cfg['db_user'] = "user";
$cfg['db_pass'] = "password";*/

// use this block for an sqlite3 backend
$cfg['db_type'] = "sqlite3";
$cfg['db_path'] = "db/";
$cfg['db_name'] = "auth.sqlite";

