<?php
declare(strict_types=1);

namespace Plan2net\PageForwarding\Hook;

use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Class TceMain
 *
 * @package Plan2net\PageForwarding\Hook
 * @author  Wolfgang Klinger <wk@plan2.net>
 */
class TceMain extends \PatrickBroens\UrlForwarding\Hook\TceMain {

    /**
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $referringObject
     * @return void
     */
    public function processDatamap_beforeStart(DataHandler $referringObject)
    {
        // the parent method uses 'trim' without testing if a value exists,
        // so we have to set an empty string here
        if (!empty($referringObject->datamap['tx_urlforwarding_domain_model_redirect'])) {
            $extensionConfiguration = (array)unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['page_forwarding']);

            foreach ($referringObject->datamap['tx_urlforwarding_domain_model_redirect'] as $uidEditedRecord => &$editedRecord) {
                $editedRecord['domain'] = $editedRecord['domain'] ?? '';
            }
        }

        parent::processDatamap_beforeStart($referringObject);
    }

}
