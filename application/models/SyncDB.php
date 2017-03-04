<?php
/**
 * Created by PhpStorm.
 * User: sahil333
 * Date: 9/1/17
 * Time: 3:55 PM
 */
final class SyncDB{

    public static $TABLE = "Sync_Table";
    public static $_ID = "_id";
    public static $ROW_ID = "row_id";
    public static $TABLE_NAME = "table_name";
    public static $EDITOR = "changed_by";
    public static $TIME = "time";
    public static $CHANGE_TYPE = "change_type"; // add/edit/ (well till now there is no remove type)remove
    public static $EDITOR_ROLE = "editor_role";
    public static $NOTIFY_SCOPE = "notify_scope";
}