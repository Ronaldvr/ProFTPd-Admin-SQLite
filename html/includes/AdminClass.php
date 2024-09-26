<?php /** @noinspection ALL */
/**
 * This file is part of ProFTPd Admin
 *
 * @package ProFTPd-Admin
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 *
 * @copyright Lex Brugman <lex_brugman@users.sourceforge.net>
 * @copyright Christian Beer <djangofett@gmx.net>
 * @copyright Ricardo Padilha <ricardo@droboports.com>
 *
 */
global $cfg;
/*
include_once ("configs/config.php");
// hash_pbkdf2 implementation for 5.3 <= PHP < 5.5
if ($cfg['passwd_encryption'] == "pbkdf2") {
    require "hash_pbkdf2_compat.php";
} elseif ($cfg['passwd_encryption'] == "crypt") {
    require "unix_crypt.php";
}
*/
/**
 * Provides all functions needed by the individual scripts
 *
 * @author Christian Beer
 * @package ProFTPd-Admin
 *
 * @todo streamline usage of $config and create a Class for it
 * @todo make database calls generic to the caller
 * @todo create standard user and group objects
 */
class AdminClass {
    /**
     * database layer
     * PDO
     */
    var $dbConn ;
    /**
     * configuration store
     * @var Array
     */
    var $config = false;
    /**
     * version number
     * @access private
     * @var String
     */
    var $version = "4";

    /**
     * $utbl_fields
     * Fieldnames of user table mapped to generic names
     * @access private
     * @var Array
     */
    var $utbl_fields = array();

    /**
     * user fields as string to paste into queries
     * @var string
     */
    var $utbl_fields_string = '';

    /**
     * user fields as string with an alias prefix (u) to paste into (insert) queries
     * 
     * @var string
     */
    var $utbl_fields_string_wprefix = '';

    /**
     * user fields as parameter string ( :fieldname ) to paste into insert queries
     * @var string
     */
    var $utbl_fields_string_param_i = '';

    /**
     * user fields as parameter string ( =:fieldname ) to paste into update queries
     * @var string
     */
    var $utbl_fields_string_param_u = '';


    /**
     * $gtbl_fields
     * Fieldnames of groups table mapped to generic names
     * @access private
     * @var Array
     */    
     var $gtbl_fields = array();

    /**
     * group fields as string to paste into queries
     * @var string
     */
    var $gtbl_fields_string;

    /**
     * group fields as string with an alias prefix to paste into (insert) queries
     * 
     * @var string
     */
    var $gtbl_fields_string_wprefix ;

    var $gtbl_fields_string_param_i='';


    var $gtbl_fields_string_param_u;

    var $LinUsers;

    /**
     * initialize the database connection via ezSQL_mysql
     * @param Array $cfg configuration array retrieved from config.php to store in the object
     */
    function __construct(array $cfg){
		
        $this->config = $cfg;
        // if db_type is not set, default to mysqli
        if (!isset($cfg['db_type']) || $cfg['db_type'] == "mysqli") {
            $dbConn = new PDO('mysql:host='.$cfg['db_host'].';dbname='.$cfg['db_name'], $cfg['db_user'], $cfg['db_pass']);
        } elseif ($cfg['db_type'] == "mysql") {
            $this->dbConn = new PDO('mysql:host='.$cfg['db_host'].';dbname='.$cfg['db_name'], $cfg['db_user'], $cfg['db_pass']);
        } elseif ($cfg['db_type'] == "postgresql") {
            $this->dbConn = new PDO('pgsql:host='.$this->config['db_host'].';dbname='.$this->config['db_name']. 'user='.$this->config['db_user'].'password='.$this->config['db_pass'].'port=5432');
        }
        elseif ($cfg['db_type'] == "sqlite3") {
            $this->dbConn = new PDO('sqlite:'.$this->config['db_path'].$this->config['db_name']);
        } else {
            trigger_error('Unsupported database type: "' . $cfg['db_type'] . '"', E_USER_WARNING);
        }
        /*groups general items */
        $this->gtbl_fields['field_groupname']=$this->config['field_groupname'];
        $this->gtbl_fields['field_gid']=$this->config['field_gid'];
        $this->gtbl_fields['field_members']=$this->config['field_members'];
        $this->gtbl_fields_string='';
        foreach ($this->gtbl_fields as $key => $fieldname) {
            $this->gtbl_fields_string = $this->gtbl_fields_string .  ', '.$fieldname;
        }
        $this->gtbl_fields_string = ltrim($this->gtbl_fields_string, ',');
        $this->gtbl_fields_string_wprefix='';
        foreach ($this->gtbl_fields as $key => $fieldname) {
            $this->gtbl_fields_string_wprefix = $this->gtbl_fields_string_wprefix .  ', g.'.$fieldname;
        }
        $this->gtbl_fields_string_wprefix = ltrim($this->gtbl_fields_string_wprefix, ',');
        $this->gtbl_fields_string_param_i='';
        foreach ($this->gtbl_fields as $key => $fieldname) {
            $this->gtbl_fields_string_param_i = $this->gtbl_fields_string_param_i .  ', :'.$fieldname;
        }
        $this->gtbl_fields_string_param_i = ltrim($this->gtbl_fields_string_param_i, ',');
        $this->gtbl_fields_string_param_u='';
        foreach ($this->gtbl_fields as $key => $fieldname) {
            $this->gtbl_fields_string_param_u = $this->gtbl_fields_string_param_u .  ', '.$fieldname.'=:'.$fieldname;
        }
        $this->gtbl_fields_string_param_u = ltrim($this->gtbl_fields_string_param_u, ',');
        /*usertable general items*/
         $this->utbl_fields = array(
                'field_userid'=>$this->config['field_userid'] ,
                'field_uid' => $this->config['field_uid'],
                'field_ugid' =>$this->config['field_ugid'],
                'field_passwd' => $this->config['field_passwd'],
                'field_homedir'=>$this->config['field_homedir'],
                'field_shell'=>$this->config['field_shell'],
                'field_sshpubkey' => $this->config['field_sshpubkey'],
                'field_title'=>$this->config['field_title'],
                'field_name' => $this->config['field_name'],
                'field_company' => $this->config['field_company'],
                'field_email' => $this->config['field_email'],
                'field_comment' => $this->config['field_comment'],
                'field_disabled' => $this->config['field_disabled'],
                'field_last_modified' => $this->config['field_last_modified'],
                'field_expiration' => $this->config['field_expiration']);
            $this->utbl_fields_string='';
            foreach ($this->utbl_fields as $key => $fieldname) {
                    $this->utbl_fields_string = $this->utbl_fields_string .  ', '.$fieldname;
             }
            $this->utbl_fields_string = ltrim($this->utbl_fields_string, ',');
            $this->utbl_fields_string_wprefix='';
            foreach ($this->utbl_fields as $key => $fieldname) {
                 $this->utbl_fields_string_wprefix = $this->utbl_fields_string_wprefix .  ', u.'.$fieldname;
            }
            $this->utbl_fields_string_wprefix = ltrim($this->utbl_fields_string_wprefix, ',');
            $this->utbl_fields_string_param_i='';
            foreach ($this->utbl_fields as $key => $fieldname) {
                $this->utbl_fields_string_param_i = $this->utbl_fields_string_param_i .  ', :'.$fieldname;
            }
            $this->utbl_fields_string_param_i = ltrim($this->utbl_fields_string_param_i, ',');
            $this->utbl_fields_string_param_u='';
            foreach ($this->utbl_fields as $key => $fieldname) {
                $this->utbl_fields_string_param_u = $this->utbl_fields_string_param_u .  ', '.$fieldname.'=:'.$fieldname;
            }
            $this->utbl_fields_string_param_u = ltrim($this->utbl_fields_string_param_u, ',');
            $this->LinUsers=$this->getLinuxUsers();        
            $this->writeLinuxUserTmp( $this->LinUsers);           
            }

        /**
         * return the version number to outside class
         * @return String
         */
        function get_version() {
            return $this->version;
        }



    /**
         * retrieves groups for each user and populates an array of $data[userid][gid] = groupname
         * @return Array like $data[userid][gid] = groupname
         */
        function parse_groups($userid = false) {
            $query = $this->dbConn->prepare('SELECT * FROM '.$this->config['table_groups']);
            $query->execute();
            $data = array();
            if ($query->errorCode()==0) {
                while ($group = $query->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
                    $names = explode(",", $group[$this->config['field_members']]);
                    reset($names);
                    foreach ($names as $key => $name) {
                        $data[$name][$this->config['field_gid']] = $group[$this->config['field_groupname']];
                    }
                }
            }
            /* no userid provided, return all data */
            if ($userid === false) return $data;
            /* if there is data for provided userid, return only that */
            if (array_key_exists($userid, $data)) return $data[$userid];
            /* return nothing otherwise */
            return array();
        }

        /**
         * retrieves the list of groups and populates an array of $data[gid] = groupname
         * @return Array like $data[gid] = groupname
         */
        function get_groups() {
            $field_gid = $this->config['field_gid'];
            $field_groupname = $this->config['field_groupname'];
            $fiels=$this->gtbl_fields_string;
            $string='SELECT '.$this->gtbl_fields_string_wprefix.',  count(u.'.$this->config['field_uid'].') as usercount 
            FROM '.$this->config['table_groups']. ' AS g
            JOIN '.$this->config['table_users'].' AS u ON g.'.$this->config['field_gid'].' = u.'.$this->config['field_ugid'].'
            GROUP BY '.$this->gtbl_fields_string_wprefix;
            $query = $this->dbConn->prepare('SELECT '.$this->gtbl_fields_string_wprefix.',  count(u.'.$this->config['field_uid'].') as usercount 
                                                  FROM '.$this->config['table_groups']. ' AS g
                                                  LEFT JOIN '.$this->config['table_users'].' AS u ON g.'.$this->config['field_gid'].' = u.'.$this->config['field_ugid'].'
                                                  GROUP BY '.$this->gtbl_fields_string_wprefix);
            $data = array();
            if ($query->execute() == true) {
            $groups = $query->fetchAll(PDO::FETCH_ASSOC) ;
            foreach ($groups as $group)
                    $data[$group[$field_gid]] = ['name'=>$group[$field_groupname], 'usercount' =>$group['usercount']];
                }
                return $data;
        }

        /**
         * retrieves all users from db and populates an associative array
         * @return Array an array containing the users or false on failure
         */
        function get_users() {
            $query = $this->dbConn->prepare('SELECT * FROM '.$this->config['table_users'].' ORDER BY '.$this->utbl_fields['field_name'].' ASC');
            $query->execute();
            if ($query->errorCode()<>0)  return false;
            $data =  $query->fetchAll();
            return $data;
        }


        /**
         * returns either the total number or the number of empty groups in the db
         * @param Boolean $only_emtpy
         * @return int number or false on error
         * (Refactor: Split into 2 functions: this makes no sense as it is now)
         */
        function get_group_count($only_empty = false) {
            if ($only_empty) {
                $query = $this->dbConn->prepare('SELECT COUNT(*) FROM '. $this->config['table_groups'].'  WHERE 1=1 AND '.$this->config['field_members'].' =""');
                $query->execute();
            }
            else {
                $query = $this->dbConn->prepare('SELECT COUNT(*) FROM '.$this->config['table_groups']);
                $query->execute();
            }
            return $query->fetchColumn(0);
        }

        /**
         * returns either the total number or the number of disabled users in the db
         * @param Boolean $only_disabled
         * @return int number or false on error
         */
        function get_user_count($only_disabled = false) {
            if ($only_disabled) {
                $query = $this->dbConn->prepare('SELECT COUNT(*) FROM '.$this->config['table_users'].' WHERE 1=1 AND '.$this->config['field_disabled'].' ="1"');
                $query->execute();
            } else {
                $query = $this->dbConn->prepare('SELECT COUNT(*) FROM '.$this->config['table_users']);
                $query->execute();
            }
            return $query->fetchColumn(0);
        }


        /**
         * returns the last index number of the user table
         * @return int
         */
        function get_last_uid() {
            $query = $this->dbConn->prepare('SELECT MAX('.$this->config['field_uid'].') FROM '.$this->config['table_users']);
            $query->execute();
            return $query->fetchColumn();
    }

        /**
         * Checks if the given value is already in a table
         * @param String $type, $value
         * @return boolean true if item exists, false if not
         */

        function check_exists($type, $value) {
            if ( $type=='groupname') {
                $query = $this->dbConn->prepare('SELECT 1 FROM '.$this->config['view_group_check'].' WHERE '.$this->config['field_groupname'].'=?');
            } elseif ( $type=='gid') {
                $query = $this->dbConn->prepare('SELECT 1 FROM '.$this->config['view_group_check'].' WHERE '.$this->config['field_gid'].'=?');
            } elseif  ( $type== 'username') {
                $query = $this->dbConn->prepare('SELECT 1 FROM '.$this->config['view_user_check'].' WHERE '.$this->config['field_userid'].'=?');
            }  elseif ( $type== 'id_user') {
                $query = $this->dbConn->prepare('SELECT 1 FROM '.$this->config['view_user_check'].' WHERE '.$this->config['field_id'].'=?');
            }  elseif ( $type== 'uid') {
                $query = $this->dbConn->prepare('SELECT 1 FROM '.$this->config['view_user_check'].' WHERE '.$this->config['field_uid'].'=?');
            }
            $query->execute(array($value));
            return $query->fetchColumn(0);
        }


        /**
         * Adds a group entry into the database
         * @param Array $groupdata
         * @return Boolean true on success, false on failure
         */
        function add_group($groupdata) {
            $query = $this->dbConn->prepare('INSERT INTO '.$this->config['table_groups']. ' ('. $this->gtbl_fields_string.') VALUES ('. $this->gtbl_fields_string_param_i.')');
            foreach ($groupdata as $fieldname => $content) {
                $query->bindValue(  ':'.$this->gtbl_fields[ 'field_'.$fieldname ], $content ) ;
            }
            if ($query->execute() == false) {
                return false;
            }
            return true;
        }

        /**
         * Adds a user entry into the database
         * @param Array $userdata
         * @return Boolean true on success, false on failure
         * TODO: Test on MySQL if quotes are needed
         */
        function add_or_update_user($userdata)
        {
            $field_passwd = $this->config['field_passwd'];
            $passwd_encryption = $this->config['passwd_encryption'];
            $passwd = "";
            $isupdate=false;
            foreach ($userdata as $key => $inputvalue) {
                $valuestoinsert[$key] =$inputvalue;
            }
            if ($passwd_encryption == 'pbkdf2') {
                $passwd = hash_pbkdf2("sha1", $userdata[$field_passwd], $userdata[$this->config['field_userid'] ], 5000, 20);
                /*
                $passwd = '"' . $passwd . '"';
                These additional quotes do not work when using SQLITE at least. Test with MySQL.
                */
            } else if ($passwd_encryption == 'crypt') {
                $passwd = crypt($userdata[$field_passwd],'');
                $passwd = '"' . $passwd . '"';
            } else if (strpos($passwd_encryption, "OpenSSL:") === 0) {
                $passwd_digest = substr($passwd_encryption, strpos($passwd_encryption, ':') + 1);
                $passwd = 'CONCAT("{' . $passwd_digest . '}",TO_BASE64(UNHEX(' . $passwd_digest . '("' . $userdata[$field_passwd] . '"))))';
            } else {
                $passwd = $passwd_encryption . '("' . $userdata[$field_passwd] . '")';
            }
            $valuestoinsert[$field_passwd] = $passwd;
            $valuestoinsert[$this->config['field_sshpubkey']] = addslashes($userdata[ $this->config['field_sshpubkey']]);
            $valuestoinsert[$this->config['field_last_modified']] = date('Y-m-d H:i:s');
            $valuestoinsert[$this->config['field_expiration']] = date("Y-m-d H:i:s", strtotime("+1 year", time()));
            if ($this->check_exists('id_user', $userdata[$this->config['field_id']])) {
                $isupdate=true;
                 $query = $this->dbConn->prepare('UPDATE ' . $this->config['table_users'] . ' SET ' .$this->utbl_fields_string_param_u. ' WHERE  '.$this->config['field_id']. '=:gid2');
            } else {
                $query = $this->dbConn->prepare('INSERT INTO ' . $this->config['table_users'] . '( ' .  $this->utbl_fields_string. ' ) VALUES (' . $this->utbl_fields_string_param_i . ')');
            }
            foreach ($valuestoinsert as $fieldname => $value) {
                if ($fieldname == $this->config['field_id']) {
                    /*skip*/
                } else {
                $query->bindValue(':' . $this->utbl_fields['field_' . $fieldname], $value);
                }
            }
            if ($isupdate ==true) {
                $query->bindValue(':gid2', $userdata[$this->config['field_id']]);
            }
                if ($query->execute() == false) {
                    return $query->errorCode();
                }
            return true;

        }

        /**
         * retrieve a group by gid
         * @param int $gid
         * @return Object
         */
        function get_group_by_gid($gid) {
            if (empty($gid)) return false;
            $query = $this->dbConn->prepare('SELECT * FROM '.$this->config['table_groups'].' WHERE '.$this->config['field_gid'].'=?');
            if ( $query->execute(array($gid)) == false) {
                return false;
            }
            $result = $query->fetchAll();
            return $result[0];
        }

        /**
         * retrieve a user by userid
         * @param string $userid
         * @return Array
         */
        function get_user_by_userid($userid) {
            $userid_param=array(0=>$userid);
            if (empty($userid)) return false;
            $query = $this->dbConn->prepare('SELECT * FROM '.$this->config['table_users'].' WHERE '.$this->config['field_userid'].'="?"');
            $query->execute($userid_param);
            $result = $query->fetchAll();
            return $result; 
        }

        /**
         * retrieve a user by id
         * @param int $id
         * @return Array
         */
        function get_user_by_id($id) {
            if (empty($id)) return false;
            $query = $this->dbConn->prepare('SELECT * FROM '.$this->config['table_users'].' WHERE '.$this->config['field_id'].'=?');
            $query->execute(array($id));
            $result = $query->fetchAll();
            if($result && count($result))
            { return $result[0]; }
            return false;
        }

        /**
         * retrieves user from database with given maingroup
         * and populates an array of $data[id] = userid
         * @param int $gid
         * @return Array form is $data[id] = userid
         */
        function get_users_by_gid($gid)
        {
            if (empty($gid)) return false;
            $query = $this->dbConn->prepare('SELECT '.$this->config['field_id'].' ,'. $this->config['field_userid'].' FROM '. $this->config['table_users'].' WHERE '.$this->config['field_ugid'].'=?');
            $query->execute( array($gid));
            $result = $query->fetchAll();

            if ($result && count($result)) {
                foreach ($result as $user) {
/*                    $data[$user->$this->config['field_id']] = $user->$this->config['field_userid'];*/
                    $data[$user[$this->config['field_id']]] = $user[$this->config['field_userid']];

                }
                if (count($data) == 0) return false;
                return $data;
            }
            return false;

        }
        /**
         * retrieves user from database with given maingroup
         * and returns their count
         * obsolete, done within get_groups_query
         */

        /**
         * retrieves members from group and populates an array of $data[id] = userid
         * @param int $gid
         * @return Array form is $data[id] = userid
         * TODO: Create users/groups linking table that does this as should be in a properly normalized database
         */
        function get_add_users_by_gid($gid) {
            if (empty($gid)) return false;
            $group = $this->get_group_by_gid($gid);
            if (!$group) return false;

            $field_id = $this->config['field_id'];
            $field_userid = $this->config['field_userid'];
            $field_members = $this->config['field_members'];

            $userids = explode(",", $group[$field_members]);
            $data = array();
            foreach ($userids as $userid) {
                $user = $this->get_user_by_userid($userid);
                if (!$user) continue;
                $data[$user[$field_id]] = $user[$field_userid];
            }
            if (count($data) == 0) return false;
            return $data;
        }

        /**
         * retrieves user from database with given maingroup
         * and returns their count
         * @param int $gid
         * @return int number
         * TODO: Could replace this with complicated SQL but the TODO on the previous function would achive
         * TODO: the desired result in another manner anyway
         */
        function get_user_add_count_by_gid($gid) {
            if (empty($gid)) return false;
            $group = $this->get_group_by_gid($gid);
            if (!$group) return false;

            $field_id = $this->config['field_id'];
            $field_userid = $this->config['field_userid'];
            $field_members = $this->config['field_members'];

            $userids = explode(",", $group[$field_members]);
            $data = array();
            foreach ($userids as $userid) {
                $user = $this->get_user_by_userid($userid);
                if (!$user) continue;
                $data[$user[$field_id]] = $user[$field_userid];
            }
            return count($data);
        }

        /**
         * Adds a user to a comma separated group string using the groupid, and inserts it into members field of group table
         * @param string $userid
         * @param int $gid
         * @return boolean false on error
         */
        function add_user_to_group($userid, $gid) {
            if (empty($userid) || empty($gid)) return false;
            if ( $this->check_exists('group',$gid)) {            
                $query = $this->dbConn->prepare('SELECT '.$this->config['field_members'].' FROM '.$this->config['table_groups'].' WHERE '. $this->config['field_gid'].'=?');
                $query->execute( array($gid));
                $result = $query->fetchColumn();
                if ($result != "") {
                    if(strpos($result, $userid) !== false) {
                        return true;
                    } else {
                        $members = $result.','.$userid;
                    }
                } else {
                    $members = $userid;
                }
                $query = $this->dbConn->prepare('UPDATE '.$this->config['table_groups'].' SET '. $this->config['field_members'].'="?" WHERE '.$this->config['field_gid'].'=?');
                if ($query ->execute(array($members, $gid)) == false ) {
                    return false; }
            } else { 
                return false; 
            }
            return true;
        }

        /**
         * removes a user from a given group using the groupid
         * @param string $userid
         * @param int $gid
         * @return boolean false on error
         */
        function remove_user_from_group($userid, $gid) {
            if (empty($userid) || empty($gid)) return false;
            $query = $this->dbConn->prepare( 'SELECT '.$this->config['field_members'].' FROM '.$this->config['table_groups'].' WHERE '.$this->config['field_gid'].'=?');
            $query->execute( array($gid));
            $result = $query->fetchColumn();
            if(strpos($result, $userid) === false) {
                return true;
            }
            $members_array = explode(",", $result);
            $members_new_array = array_diff($members_array, array("$userid", ""));
            if (is_array($members_new_array)) {
                $members_new = implode(",", $members_new_array);
            } else {
                $members_new = "";
            }

            $query = $this->dbConn->prepare(  'UPDATE '. $this->config['table_groups'].' SET '.$this->config['field_members'].'=? WHERE '.$this->config['field_gid'].'=?');
            if ( $query->execute(array($members_new , $gid)) == false ) {
                 return false; }
            return true;
        }

        /**
         * updates the group entry in the database (currently only the gid!)
         * @param int $gid
         * @param int $new_gid
         * @return Boolean true on success, false on failure
         */
        function update_group($gid, $new_gid) {
            $query = $this->dbConn->prepare( 'UPDATE '.$this->config['table_users'].' SET '.$this->config['field_ugid'].'=? WHERE '. $this->config['field_ugid'].'=?');
            if ($query->execute(array( $new_gid, $gid)) ==false) {
             return false;
            }
            $query = $this->dbConn->prepare( 'UPDATE '.$this->config['table_groups'].' SET '.$this->config['field_gid'].'=? WHERE '. $this->config['field_gid'].'=?');
            if ($query->execute( array($new_gid,  $gid)) == false ) {
                 return false;}
            return true;
        }

        /**
         * delete a group by gid
         * @param int $gid
         * @return Boolean true on success, false on failure
         */
        function delete_group_by_gid($gid) {
            $query = $this->dbConn->prepare( 'DELETE FROM '.$this->config['table_groups'].' WHERE '.$this->config['field_gid'].'=?');
            if( $query ->execute( array($gid)) == false) {
              return false; }
            return true;
        }

        /**
         * removes the user entry from the database
         * @param int $id
         * @return Boolean true on success, false on failure
         */
        function remove_user_by_id($id) {
            $query = $this->dbConn->prepare( 'DELETE FROM '.$this->config['table_users'].' WHERE '.$this->config['field_id'].'=?');
            if($query ->execute( array($id)) ==false ){
                return false;}
            return true;
        }

        function getLinuxUsers() {
            $result = [];
            /** @see http://php.net/manual/en/function.posix-getpwnam.php */
            $keys = ['name', 'passwd', 'uid', 'gid', 'gecos', 'dir', 'shell'];
            $handle = fopen('/var/www/hostpasswd', 'r');
            if(!$handle){
            throw new RuntimeException("failed to open /etc/passwd from the host for reading! ".print_r(error_get_last(),true));
            }
            while ( ($values = fgetcsv($handle, 1000, ':')) !== false ) {
                $result[] = array_combine($keys, $values);
            }
            fclose($handle);
            return $result;
        }

        function writeLinuxUserTmp( $valuestoinsert )
        {
        $this->dbConn->query('DELETE FROM tmpLinuxUsers');

        $query = $this->dbConn->prepare('INSERT INTO tmpLinuxUsers  (userid, passwd, uid, ugid, comment, homedir, shell) VALUES (:name, :passwd, :uid, :gid, :gecos, :dir, :shell)');
        foreach ($valuestoinsert as $rowtoinsert ) {
            foreach ($rowtoinsert as $paramname => $value) {
                $query->bindValue(':' . $paramname, $value);
            }
            if ($query->execute() == false) {
                    return $query->errorCode();
            }
        }
//        $query = $this->dbConn->prepare('INSERT INTO tmpLinuxUsers  (userid, passwd, uid, ugid, comment, homedir, shell) VALUES (:name, :passwd, :uid, :gid, :gecos, :dir, :shell)');

        return true;
        }

         function ExportNewUsertoLinux( ) {
        /**
         * Exports not yet exisitng users to a file that can be used with the newusers linux command
         * https://www.man7.org/linux/man-pages/man8/newusers.8.html
         * NOTE:This route means it may be prohibited to use a colon in the password
         */
        $query = $this->dbConn->prepare('SELECT * FROM New_Linux_Users');
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 0) {
            return null;
          }
          $df = fopen("newusers.txt", 'w');
          /* header lline not neessary or even wanted
          fputcsv($df, array_keys($result[0]),":");
          */
          foreach ($result as $row) {
                     fputcsv($df, $row, ":");
          }
          fclose($df);
        return true;
        }
	
        /**
         * generate a random string
         * @param int $length default 6
         * @return String of random characters of the specified length
         */
        function generate_random_string($length = 6) {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ._-,';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }

        /**
         * check the validity of the id
         * @param int $id
         * @return Boolean true if the given id is a positive int
         */
        function is_valid_id($id) {
            return is_numeric($id) && (int)$id > 0 && $id == round($id);
        }
    }

