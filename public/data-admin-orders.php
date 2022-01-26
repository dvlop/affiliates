<?php /*

Copyright (c) 2008 Metathinking Ltd.

This file is part of Affiliates For All.

Affiliates For All is free software: you can redistribute it and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

Affiliates For All is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with Affiliates For All.  If not, see
<http://www.gnu.org/licenses/>.

*/

$admin_required = TRUE;
require_once '../lib/bootstrap.php';

function validate_record($fields) {
    $db = new Database();

    if(!$db->get_row_by_key('affiliates', 'id', $fields['affiliate'])) {
        echo json_encode('That affiliate does not exist.');
        return FALSE;
    }

    if($db->get_row_by_key('orders', 'id', $fields['id'])
            && (!isset($fields['key']) || $fields['id'] != $fields['key'])) {
        echo json_encode('That order number is already in use.');
        return FALSE;
    }

    return TRUE;
}

$pager = new Pager('orders', Database::$order_fields,Database::$order_headings);
$pager->set_admin_mode();
$pager->set_editable();

if($_GET['format'] == 'download') {
    $pager->download();
} else if($_GET['format'] == 'json') {
    Template::check_ajax_key();
    $pager->json($_GET['page']);
} else if($_GET['format'] == 'write') {
    Template::check_ajax_key();
    if(validate_record($_GET))
        $pager->write($_GET);
} else if($_GET['format'] == 'delete') {
    Template::check_ajax_key();
    $pager->delete($_GET['key']);
} else {
    Template::check_ajax_key();
    $pager->json_single($_GET['key']);
}
