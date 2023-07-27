/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
var protocol = window.location.protocol;
var base_url_cke = urlbase + "themes/admin/asset/js/ckeditor/";
CKEDITOR.editorConfig = function (config) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    config.height = '400px';
    config.filebrowserBrowseUrl = base_url_cke + 'filemanager/index.html';
    config.filebrowserImageBrowseUrl = base_url_cke + 'filemanager/index.html?Type=Images';
    config.filebrowserFlashBrowseUrl = base_url_cke + 'filemanager/index.html?Type=Flash';
    config.filebrowserUploadUrl = base_url_cke + 'filemanager/ckupload.php?&type=Files';
    config.filebrowserImageUploadUrl = base_url_cke + 'filemanager/ckupload.php?&type=Images';
    config.filebrowserFlashUploadUrl = base_url_cke + 'filemanager/ckupload.php?type=Flash';
    config.allowedContent = true;
    // ALLOW <i></i>
    config.protectedSource.push(/<i[^>]*><\/i>/g);
    config.extraAllowedContent = '*{*}';
};
