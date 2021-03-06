/**
 * CuCustomField : baserCMS Custom Field Multiple Plugin
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link             https://catchup.co.jp
 * @package          CuCfMultiple.js
 * @license          MIT LICENSE
 */


$(function(){
    var fieldType = $("#CuCustomFieldDefinitionFieldType");

    fieldType.change(switchRelated);
    $("#CuCustomFieldDefinitionParentId").change(switchRelated);
    switchRelated();

    function switchRelated() {
        if(fieldType.val() === 'file') {
            $("#RowCuCfDefaultValue").hide();
        }
    }
});

