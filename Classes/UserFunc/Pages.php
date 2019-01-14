<?php
declare(strict_types=1);

namespace Plan2net\PageForwarding\UserFunc;

use TYPO3\CMS\Backend\Form\Element\UserElement;

/**
 * Class Pages
 *
 * @package Plan2net\PageForwarding\UserFunc
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class Pages {

    /**
     * @param array $recordData
     * @param \TYPO3\CMS\Backend\Form\Element\UserElement $parent
     * @return string
     */
    public function getParentId($recordData, UserElement $parent) : string {
        // a little hack to get the parent record (page) ID,
        // as we have to set the storage PID for these records to a folder ID
        // see ext_localconf -> addPageTSConfig for details
        $ajaxParts = explode('-', $_POST['ajax'][0]);
        $pageId = (integer)$ajaxParts[1];

        return '<input type="text" name="' . $recordData['itemFormElName'] . '" value="' . $pageId . '" class="form-control" readonly="readonly">';
    }

}
